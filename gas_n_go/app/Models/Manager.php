<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Person.php');
require_once(__DIR__ . '/Operation.php');
require_once(__DIR__ . '/Action.php');
require_once(__DIR__ . '/Stat.php');
require_once(__DIR__ . '/Chart.php');
require_once(__DIR__ . '/Car.php');

class Manager extends Person
{
    public static function getOperations()
    {
        return array(
            'hireEmployee' => new Operation('Assumi addetto', 'employees/create.php'),
            'addCar' => new Operation('Aggiungi veicolo', 'cars/create.php'),
            'employees' => new Operation('Addetti', 'employees/index.php'),
            'rentals' => new Operation('Noleggi', 'branches/rentals/index.php'),
            'cars' => new Operation('Veicoli', 'cars/index.php'),
        );
    }

    public function getStats()
    {
        $db = new DB();

        $monthNames = array(
            'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno',
            'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'
        );

        $stats = array(
            'Cars' => new Stat(
                'Veicoli disponibili',
                $this->queryCarsCount($db)
            ),
            'Employees' => new Stat(
                'Addetti',
                $this->queryEmployeesCount($db)
            ),
            'Members' => new Stat(
                'Soci',
                $this->queryMembersCount($db)
            ),
            'Revenue' => new Stat(
                'Fatturato ' . $monthNames[idate('m') - 1],
                $this->queryMonthlyRevenue($db) ?? '0'
            )
        );

        $db->disconnect();

        return $stats;
    }

    public function getChart()
    {
        $char = new Chart('Noleggi', Chart::LINE, 'rentals');

        $db = new DB();

        $rentals = $this->queryMonthlyRentalCount($db);

        if (!is_null($rentals)) {
            foreach ($rentals as $rental) {
                $day = (int) $rental['day'];
                $char->add($day - 1, $rental['rentals']);
            }
        }

        $db->disconnect();

        return $char;
    }

    public function getProfile()
    {
        $branch =  $this->getManagedBranch();
        $memberProfile = array(
            'Manager filiale' => $branch['name'],
        );

        return array_merge($memberProfile, parent::getProfile());
    }

    // QUERIES

    public function getManagedBranch()
    {
        $db = new DB();

        $query = '
            SELECT f.nome AS name, f.data_di_apertura AS openingDate
            FROM manager AS m
            JOIN filiale AS f ON f.manager = m.cf
            WHERE m.cf = :cf
            LIMIT 1
        ';
        $params = array('cf' => $this->cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        $db->disconnect();

        return $result;
    }

    // STATS
    private function queryCarsCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM manager AS m
            JOIN filiale AS f ON f.manager = m.cf
            JOIN veicolo AS v ON v.filiale = f.nome
            WHERE m.cf = :cf AND v.stato <> :stato
        ';

        $params = array('cf' => $this->cf, 'stato' => Car::UNAVAILABLE);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryEmployeesCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM manager AS m
            JOIN filiale AS f ON f.manager = m.cf
            JOIN addetto AS a ON f.nome = a.filiale
            WHERE m.cf = :cf AND
            (CURDATE() < a.data_scadenza_contratto OR a.data_scadenza_contratto IS NULL)
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryMembersCount($db)
    {
        $query = '
            SELECT COUNT(DISTINCT s.cf) AS count
            FROM manager AS m
            JOIN filiale AS f ON f.manager = m.cf
            JOIN noleggio AS n ON n.filiale = f.nome
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            JOIN socio AS s ON s.cf = n.socio
            WHERE m.cf = :cf AND
            YEAR(i.data_operazione) = YEAR(CURRENT_DATE())
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }
    
    private function queryMonthlyRevenue($db)
    {
        $query = '
            SELECT CONCAT(SUM(costo), "€") AS revenue
            FROM noleggiocheckout AS o
            JOIN noleggio AS n ON n.codice_noleggio = o.noleggio
            JOIN filiale AS f ON f.nome = n.filiale
            JOIN manager AS m ON m.cf = f.manager
            WHERE m.cf = :cf
            AND MONTH(o.data_operazione) = MONTH(CURRENT_DATE())
            AND YEAR(o.data_operazione) = YEAR(CURRENT_DATE())
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['revenue'];
    }
    
    // CHART
    private function queryMonthlyRentalCount($db)
    {
        $query = '
            SELECT DAY(i.data_operazione) AS day, COUNT(*) AS rentals
            FROM noleggio AS n
            JOIN filiale AS f ON f.nome = n.filiale
            JOIN manager AS m ON m.cf = f.manager
            JOIN noleggiocheckin AS i ON i.noleggio = n.codice_noleggio
            WHERE m.cf = :cf
            AND MONTH(i.data_operazione) = MONTH(CURRENT_DATE())
            AND YEAR(i.data_operazione) = YEAR(CURRENT_DATE())
            GROUP BY DAY(i.data_operazione)
        ';

        $params = array('cf' => $this->cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        return $result;
    }
    
    // CRUD OPERATIONS
    public static function create($db, $data)
    {
        $query = '
            INSERT INTO manager
            (user_id, cf, nome, cognome, data_di_nascita, domicilio, numero_di_telefono)
            VALUES
            (:user_id, :cf, :nome, :cognome, :data_di_nascita, :domicilio, :numero_di_telefono)
        ';

        $params = [];

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        return $status;
    }

    public static function read($id)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM manager
            WHERE user_id = :id
            LIMIT 1
        ';
        $params = array('id' => $id);
        $stmt = $db->bindQuery($query, $params);

        $manager = $db->get($stmt);

        $db->disconnect();

        return  is_null($manager) ? NULL : new Manager($manager);
    }
}
