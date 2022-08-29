<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Person.php');
require_once(__DIR__ . '/Operation.php');
require_once(__DIR__ . '/Stat.php');
require_once(__DIR__ . '/Chart.php');
require_once(__DIR__ . '/User.php');

class Member extends Person
{
    protected $subscriptionDate;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->subscriptionDate = $data['data_di_iscrizione'];
    }

    public static function getOperations()
    {
        return array(
            'rentals' => new Operation('Noleggi', 'members/rentals/index.php'),
        );
    }

    public function getStats()
    {
        $db = new DB();

        $stats = array(
            'rentalsCount' => new Stat(
                'Noleggi',
                $this->queryRentalsCount($db)
            ),
            'activeRentalsCount' => new Stat(
                'Noleggi attivi',
                $this->queryActiveRentalsCount($db)
            ),
            'favoriteCar' => new Stat(
                'Auto più noleggiata',
                $this->queryFavoriteCar($db)
            ),
            'averageExpense' => new Stat(
                'Spesa media',
                $this->queryAverageExpense($db)
            )
        );

        $db->disconnect();

        return $stats;
    }

    public function getChart()
    {
        $char = new Chart('Noleggi', Chart::BAR, 'rentals');
        
        $db = new DB();

        $rentalsDataSet = $this->queryAnnualRentalsCount($db);

        
        if (!is_null($rentalsDataSet)) {
            foreach ($rentalsDataSet as $rental) {
                $month = (int) $rental['month'];
                $char->add($month - 1, $rental['rentals']);
            }
        }
        
        $db->disconnect();

        return $char;
    }

    public function getProfile()
    {
        $memberProfile = array(
            'Data di iscrizione' => $this->subscriptionDate,
        );

        return array_merge(parent::getProfile(), $memberProfile);
    }
    
    /* resources/rentals/checkin/member.php */
    public static function getMembersTable($actions = NULL)
    {
        return self::getIndexTable(self::readAll(), $actions);
    }
    
    public function getMemberActiveRentalsCount()
    {
        $db = new DB();

        $result = $this->queryActiveRentalsCount($db);

        $db->disconnect();

        return $result;
    }

    public function getProfileWithRentals()
    {
        $db = new DB();

        $profile =  array(
            'Codice fiscale' => $this->cf,
            'Nome' => $this->name,
            'Cognome' => $this->surname,
            'Data di nascita' => $this->birthday,
            'Domicilio' => $this->domicile,
            'Numero di telefono' => $this->telephoneNumber,
            'Data di iscrizione' => $this->subscriptionDate,
            'Noleggi' => $this->queryRentalsCount($db),
            'Noleggi attivi' => $this->queryActiveRentalsCount($db),
        );

        $db->disconnect();

        return $profile;
    }

    // QUERIES

    public static function cfExist($cf)
    {
        $db = new DB();

        $query = '
            SELECT cf
            FROM socio
            WHERE cf = :cf
            LIMIT 1
        ';
        $params = array('cf' => $cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->exist($stmt);

        $db->disconnect();

        return $result;
    }

    // STATS
    private function queryRentalsCount($db)
    {
        $db = new DB();

        $query = '
            SELECT COUNT(*) AS count
            FROM socio AS s
            JOIN noleggio AS n ON n.socio = s.cf
            AND s.cf = :cf
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryActiveRentalsCount($db)
    {
        $db = new DB();

        $query = '
            SELECT COUNT(*) AS count
            FROM socio AS s
            JOIN noleggio AS n ON n.socio = s.cf
            WHERE n.codice_noleggio NOT IN (
                SELECT noleggio
                FROM noleggiocheckout
            ) AND s.cf = :cf
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }


    private function queryFavoriteCar($db)
    {
        $db = new DB();

        $query = '
            SELECT CONCAT(v.marca, " ", v.modello) AS car, COUNT(*) AS rentals 
            FROM noleggio AS n
            JOIN veicolo AS v ON v.targa = n.veicolo 
            WHERE n.socio = :cf
            GROUP BY v.marca, v.modello
            ORDER BY rentals DESC LIMIT 1
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['car'];
    }

    private function queryAverageExpense($db)
    {
        $db = new DB();

        $query = '
            SELECT CONCAT(ROUND(AVG(o.costo), 2), "€") AS cost
            FROM noleggio AS n 
            JOIN noleggiocheckout AS o on o.noleggio = n.codice_noleggio
            WHERE n.socio = :cf
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['cost'];
    }

    // CHART

    private function queryAnnualRentalsCount($db)
    {
        $query = '
            SELECT MONTH(i.data_operazione) AS month, COUNT(*) AS rentals
            FROM noleggio AS n
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            WHERE n.socio = :cf
            AND YEAR(i.data_operazione) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(i.data_operazione)
        ';

        $params = array('cf' => $this->cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        return $result;
    }

    // CRUD OPERATIONS

    public static function create($uData, $mData)
    {
        $db = new DB();

        $db->begin();

        if (!User::create($db, $uData)) {
            $db->rollBack();
            return false;
        }

        $query = '
            INSERT INTO socio
            (user_id, cf, nome, cognome, data_di_nascita, domicilio, numero_di_telefono, data_di_iscrizione)
            VALUES
            (:user_id, :cf, :nome, :cognome, :data_di_nascita, :domicilio, :numero_di_telefono, :data_di_iscrizione);
        ';

        $params = array('user_id' => $db->lastInsertId());

        foreach ($mData as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        if (!$status) {
            $db->rollBack();
            return false;
        }

        $status = $db->commit();

        $db->disconnect();

        return $status;
    }

    public static function read($id)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM socio
            WHERE user_id = :id
            LIMIT 1
        ';
        $params = array('id' => $id);
        $stmt = $db->bindQuery($query, $params);

        $member = $db->get($stmt);

        $db->disconnect();

        return  is_null($member) ? NULL : new Member($member);
    }

    public static function readFromEmail($email)
    {
        $db = new DB();

        $query = '
            SELECT s.* FROM socio AS s
            JOIN utente AS u ON u.ID = s.user_id
            WHERE u.email = :email
            LIMIT 1
        ';

        $params = array('email' => $email);
        $stmt = $db->bindQuery($query, $params);

        $member = $db->get($stmt);

        $db->disconnect();

        return is_null($member) ? NULL : new Member($member);
    }

    public static function readAll()
    {
        $db = new DB();

        $query = '
            SELECT u.email, s.nome, s.cognome
            FROM socio AS s
            JOIN utente AS u ON u.ID = s.user_id
            ORDER BY s.data_di_iscrizione DESC, u.email
        ';

        $result = $db->getAll($db->query($query));

        $db->disconnect();

        return $result;
    }

    public function update($data)
    {
        $db = new DB();

        $query = '
            UPDATE socio
            SET cf = :cf, nome = :nome, cognome = :cognome,
            data_di_nascita = :data_di_nascita, domicilio = :domicilio,
            numero_di_telefono = :numero_di_telefono
            WHERE user_id = :id
        ';

        $params = array('id' => $this->id);

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        $db->disconnect();

        return $status;
    }

    public function delete()
    {
        $db = new DB();

        $query = '
            DELETE FROM utente
            WHERE ID = :id
        ';

        $params = array('id' => $this->id);

        $status = $db->bindQuery($query, $params);

        $db->disconnect();

        return $status;
    }
}
