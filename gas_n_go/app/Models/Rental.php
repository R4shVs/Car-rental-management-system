<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Car.php');
require_once(__DIR__ . '/Member.php');


class Rental
{
    protected $code;
    protected $car;
    protected $branch;
    protected $member;

    public function __construct($data)
    {
        $this->code = $data['codice_noleggio'];
        $this->car = $data['veicolo'];
        $this->branch = $data['filiale'];
        $this->member = $data['socio'];
    }

    /* resources/rentals/index.php */
    public static function getRentalsTable($actions = NULL)
    {
        $tHeaders = array(
            'codice', 'filiale', 'socio', 'veicolo',
            'durata', 'costo'
        );

        $data = self::readRentalsInfo();

        foreach ($data ?? [] as $key => $value) {
            if (is_null($value['duration'])) {
                $data[$key]['duration'] = 'In noleggio';
            } else if ($value['duration'] === 0) {
                $data[$key]['duration'] = 'Checkout in giornata';
            } else $data[$key]['duration'] .=
                ($value['duration'] > 1) ? ' giorni' : ' giorno';
        }

        return new Table(
            $tHeaders,
            'codice_noleggio',
            $data,
            $actions
        );
    }

    /* resources/rentals/checkout/rental.php */
    public static function getActiveMemberRentalsTable($cf, $branch, $actions = NULL)
    {
        $tHeaders = array(
            'codice', 'targa', 'veicolo',
            'inizio noleggio', 'costo'
        );

        return new Table(
            $tHeaders,
            'codice_noleggio',
            self::readActiveMemberRentals($cf, $branch),
            $actions
        );
    }

    /* resources/members/rentals/index.php */
    public static function getMemberRentalsTable($cf, $actions = NULL)
    {
        $tHeaders = array(
            'codice', 'filiale', 'targa', 'veicolo',
            'inizio noleggio', 'fine noleggio', 'costo'
        );

        $data = self::readMemberRentals($cf);

        foreach ($data ?? [] as $key => $value) {
            if (is_null($value['checkout'])) {
                $data[$key]['checkout'] = 'In noleggio';
            }
        }

        return new Table($tHeaders, 'rental_code', $data, $actions);
    }

    /* resources/branches/rentals/index.php */
    public static function getBranchRentalsTable($cf, $actions = NULL)
    {
        $tHeaders = array(
            'codice', 'socio', 'targa', 'veicolo',
            'inizio noleggio', 'fine noleggio', 'costo'
        );

        $data = self::readBranchRentals($cf);

        foreach ($data ?? [] as $key => $value) {
            if (is_null($value['checkout'])) {
                $data[$key]['checkout'] = 'In noleggio';
            }
        }

        return new Table($tHeaders, 'rental_code', $data, $actions);
    }

    /* resources/rentals/checkout/member.php */
    public static function getActiveMembersRentalsTable($branch, $actions = NULL)
    {
        $table = Member::getIndexTable(self::readActiveMembersRentals($branch), $actions);
        array_push($table->tableHeaders, 'numero noleggi');
        return $table;
    }

    public static function getMembersRentalsTable($branch, $actions = NULL)
    {
        $table = self::getIndexTable(self::readActiveRentals($branch), $actions);
        array_push($table->tableHeaders, 'numero noleggi');
        return $table;
    }

    // QUERIES
    
    public function isActive()
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM noleggio
            WHERE codice_noleggio NOT IN (
                SELECT noleggio
                FROM noleggiocheckout
            ) AND codice_noleggio = :codice
        ';

        $params = array('codice' => $this->code);
        $stmt = $db->bindQuery($query, $params);

        $reuslt = $db->exist($stmt);

        $db->disconnect();

        return $reuslt;
    }

    public function getCheckinInfo()
    {
        $db = new DB();

        $query = '
            SELECT i.data_operazione AS date, v.costo_giornaliero AS price
            FROM noleggiocheckin AS i
            JOIN noleggio AS n ON n.codice_noleggio = i.noleggio
            JOIN veicolo AS v ON v.targa = n.veicolo
            WHERE n.codice_noleggio = :codice
        ';

        $params = array('codice' => $this->code);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        $db->disconnect();

        return $result;
    }

    // GETTERS

    public function getCode()
    {
        return $this->code;
    }

    public function getCar()
    {
        return $this->car;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    // CRUD OPERATIONS

    public static function createCheckin($rentalData, $checkinData)
    {
        $db = new DB();

        $db->begin();

        if (!self::create($db, $rentalData)) {
            $db->rollBack();
            return false;
        }

        $query = '
            INSERT INTO noleggiocheckin
            (noleggio, addetto, data_operazione)
            VALUES
            (:noleggio, :addetto, :data_operazione)    
        ';

        $params = array('noleggio' => $db->lastInsertId());

        foreach ($checkinData as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        if (!$status) {
            $db->rollBack();
            return false;
        }

        if (!Car::updateState($db, $rentalData['veicolo'], Car::ON_RENTAL)) {
            $db->rollBack();
            return false;
        }

        $status = $db->commit();

        $db->disconnect();

        return $status;
    }

    public static function createCheckout($checkoutData, $plate)
    {
        $db = new DB();

        $db->begin();

        $query = '
            INSERT INTO noleggiocheckout
            (noleggio, addetto, data_operazione, costo)
            VALUES
            (:noleggio, :addetto, :data_operazione, :costo)   
        ';

        foreach ($checkoutData as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        if (!$status) {
            $db->rollBack();
            return false;
        }

        if (!Car::updateState($db, $plate, Car::AVAILABLE)) {
            $db->rollBack();
            return false;
        }

        $status = $db->commit();

        $db->disconnect();

        return $status;
    }

    private static function create($db, $data)
    {
        $query = '
            INSERT INTO noleggio
            (veicolo, filiale, socio)
            VALUES
            (:veicolo, :filiale, :socio)
        ';

        $params = [];

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        return $status;
    }

    public static function read($code)
    {
        $db = new DB();

        $query = '
            SELECT * FROM noleggio
            WHERE codice_noleggio = :codice
            LIMIT 1
        ';

        $params = array('codice' => $code);
        $stmt = $db->bindQuery($query, $params);

        $rental = $db->get($stmt);

        $db->disconnect();

        return is_null($rental) ? NULL : new Rental($rental);
    }

    private static function readRentalsInfo()
    {
        $db = new DB();

        $query = '
            SELECT n.codice_noleggio, n.filiale,
            CONCAT(s.nome, " ", s.cognome) AS member,
            CONCAT(v.marca, " ", v.modello, " ", v.colore) AS car,
            DATEDIFF(o.data_operazione, i.data_operazione) AS duration,
            CONCAT(o.costo, "€") AS revenue
            FROM noleggio AS n
            JOIN socio AS s ON s.cf = n.socio
            JOIN veicolo AS v ON v.targa = n.veicolo
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            LEFT JOIN noleggiocheckout AS o ON o.noleggio = n.codice_noleggio
            ORDER BY o.costo DESC
        ';

        $result = $db->getAll($db->query($query));

        $db->disconnect();

        return $result;
    }
    
    private static function readActiveMemberRentals($cf, $branch)
    {
        $db = new DB();

        $query = '
            SELECT n.codice_noleggio, v.targa,
            CONCAT(v.marca, " ", v.modello, " ", v.colore) AS car,
            i.data_operazione AS inizio_noleggio,
            CONCAT((v.costo_giornaliero * (DATEDIFF(NOW(), i.data_operazione)+1)), "€") AS cost
            FROM noleggio AS n
            JOIN veicolo AS v ON v.targa = n.veicolo
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            JOIN socio AS s ON s.cf = n.socio
            WHERE n.codice_noleggio NOT IN (SELECT noleggio FROM noleggiocheckout) 
            AND s.cf = :cf AND n.filiale = :filiale
            ORDER BY i.data_operazione
        ';

        $params = array('cf' => $cf, 'filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }

    public static function readActiveMembersRentals($branch)
    {
        $db = new DB();

        $query = '
            SELECT u.email, s.nome, s.cognome, COUNT(*) AS count
            FROM socio AS s
            JOIN utente AS u ON u.ID = s.user_id
            JOIN noleggio AS n ON n.socio = s.cf
            WHERE n.codice_noleggio NOT IN(
                SELECT noleggio FROM noleggiocheckout
            ) AND n.filiale = :filiale
            GROUP BY s.nome, s.cognome, u.email
            ORDER BY u.email
        ';

        $params = array('filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }
    private static function readMemberRentals($cf)
    {
        $db = new DB();

        $query = '
            SELECT n.codice_noleggio AS rental_code, n.filiale, v.targa,
            CONCAT(v.marca, " ", v.modello, " ", v.colore) AS car,
            DATE(i.data_operazione) AS checkin,
            DATE(o.data_operazione) AS checkout, CONCAT(o.costo, "€") AS revenue
            FROM socio AS s
            JOIN noleggio AS n ON n.socio = s.cf
            JOIN veicolo AS v ON v.targa = n.veicolo
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            LEFT JOIN noleggiocheckout AS o ON o.noleggio = n.codice_noleggio
            WHERE s.cf = :cf
            ORDER BY i.data_operazione DESC
        ';

        $params = array('cf' => $cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }

    private static function readBranchRentals($cf)
    {
        $db = new DB();

        $query = '
            SELECT n.codice_noleggio AS rental_code, CONCAT(s.nome, " ", s.cognome) AS member,
            v.targa, CONCAT(v.marca, " ", v.modello, " ", v.colore) AS car,
            DATE(i.data_operazione) AS checkin,
            DATE(o.data_operazione) AS checkout, CONCAT(o.costo, "€") AS revenue
            FROM filiale AS f
            JOIN manager AS m ON m.cf = f.manager
            JOIN noleggio AS n ON n.filiale = f.nome
            JOIN veicolo AS v ON v.targa = n.veicolo
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            LEFT JOIN noleggiocheckout AS o ON o.noleggio = n.codice_noleggio
            JOIN socio AS s ON s.cf = n.socio
            WHERE m.cf = :cf
            ORDER BY revenue DESC
        ';

        $params = array('cf' => $cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }
}
