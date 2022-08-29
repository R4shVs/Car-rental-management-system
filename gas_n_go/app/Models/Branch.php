<?php

require_once(__DIR__ . '/Car.php');

class Branch
{
    protected $name;
    protected $openingDate;
    protected $address;
    protected $telephoneNumber;
    protected $manager;

    public function __construct($data)
    {
        $this->name = $data['nome'];
        $this->openingDate = $data['data_di_apertura'];
        $this->address = $data['via'];
        $this->telephoneNumber = $data['numero_di_telefono'];
        $this->manager = $data['manager'];
    }

    public static function getFields()
    {
        return array(
            'name' => new Field(
                'text',
                'nome_filiale',
                'Nome filiale',
                true,
                'Agazzini'
            ),
            'address' => new Field(
                'text',
                'via',
                'Via filiale',
                true,
                'Via R.Busacca, 61'
            ),
            'telephoneNumber' => new Field(
                'text',
                'numero_di_telefono',
                'Numero di telefono',
                true,
                '0184/691674'
            )
        );
    }

    /* resources/branches/index.php */
    public static function getBranchesTable($actions = NULL)
    {
        $tHeaders = array(
            'nome', 'recapito', 'manager', 'fatturato'
        );

        return new Table($tHeaders, 'nome', self::readAll(), $actions);
    }

    /* resources/employees/index.php */
    public static function getBranchEmployeesTable($branch, $actions = NULL)
    {
        $tHeaders = array(
            'email', 'addetto', 'data_di_assunzione', 'noleggi gestiti'
        );

        return new Table($tHeaders, 'email', self::readBranchEmployees($branch), $actions);
    }

    /* resources/branches/show.php */
    public function getBranchData()
    {
        $db = new DB();

        $brachInfo = $this->queryBranchInfo($db);

        $branch =  array(
            'Filiale' => $this->name,
            'Data di apertura' => $this->openingDate,
            'Via' => $this->address,
            'Recapito' => $this->telephoneNumber,
            'Manager' => $brachInfo['manager'],
            'Recapito manager' => $brachInfo['manager_cell'],
            'Email manager' => $brachInfo['manager_email'],
            'Noleggi' => $brachInfo['rentals'],
            'Addetti' => $brachInfo['employees'],
            'Veicoli' => $brachInfo['cars']
        );


        $db->disconnect();

        return $branch;
    }

    // QUERIES
    
    public static function nameExist($name)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM filiale
            WHERE nome = :nome
            LIMIT 1
        ';

        $params = array('nome' => $name);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->exist($stmt);

        $db->disconnect();

        return $result;
    }

    private function queryBranchInfo($db)
    {
        $query = '
            SELECT CONCAT(m.nome, " ", m.cognome) AS manager,
            m.numero_di_telefono AS manager_cell,
            u.email AS manager_email,
            COUNT(DISTINCT a.cf) AS employees,
            COUNT(DISTINCT n.codice_noleggio) AS rentals,
            COUNT(DISTINCT v.targa) AS cars
            FROM filiale AS f
            JOIN manager AS m ON m.cf = f.manager
            JOIN utente AS u ON u.id = m.user_id
            LEFT JOIN addetto AS a ON a.filiale = f.nome
            LEFT JOIN noleggio AS n ON n.filiale = f.nome
            LEFT JOIN veicolo AS v ON v.filiale = f.nome AND v.stato <> :stato
            WHERE f.nome = :filiale
            LIMIT 1
        ';

        $params = array(
            'filiale' => $this->name,
            'stato' => Car::UNAVAILABLE,
        );

        $stmt = $db->bindQuery($query, $params);

        $branch = $db->get($stmt);

        return $branch;
    }

    // CRUD OPERATIONS

    public static function create($uData, $mData, $bFields)
    {
        $db = new DB();

        $db->begin();

        if (!User::create($db, $uData)) {
            $db->rollBack();
            return false;
        }

        $mData['user_id'] = $db->lastInsertId();

        if (!Manager::create($db, $mData)) {
            $db->rollBack();
            return false;
        }

        $query = '
            INSERT INTO filiale
            (nome, data_di_apertura, via, numero_di_telefono, manager)
            VALUES (:nome_filiale, :data_di_apertura, :via, :numero_di_telefono, :manager)
        ';

        $params = array();

        foreach ($bFields as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        if ($status) {
            $status = $db->commit();
        }

        $db->disconnect();

        return $status;
    }

    public static function read($name)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM filiale
            WHERE nome = :nome
            LIMIT 1
        ';

        $params = array('nome' => $name);
        $stmt = $db->bindQuery($query, $params);

        $branch = $db->get($stmt);

        $db->disconnect();

        return  is_null($branch) ? NULL : new Branch($branch);
    }

    public static function readAll()
    {
        $db = new DB();

        $query = '
            SELECT f.nome,
            f.numero_di_telefono, CONCAT(m.nome, " ", m.cognome) AS manager,
            CONCAT(SUM(o.costo),"€") AS revenue
            FROM filiale AS f
            JOIN manager AS m ON m.cf = f.manager
            LEFT JOIN noleggio AS n ON n.filiale = f.nome
            LEFT JOIN noleggiocheckout AS o ON o.noleggio = n.codice_noleggio
            GROUP BY f.nome
            ORDER BY SUM(o.costo) DESC, f.nome ASC
        ';

        $result = $db->getAll($db->query($query));

        $db->disconnect();

        return $result;
    }

    public static function readBranchEmployees($branch)
    {
        $db = new DB();

        $query = '
            SELECT u.email, CONCAT(a.nome, " ", a.cognome) AS employee, a.data_di_assunzione,
            COUNT(DISTINCT i.noleggio) + COUNT(DISTINCT o.noleggio) AS n_out
            FROM filiale AS f
            JOIN addetto AS a ON a.filiale = f.nome
            JOIN utente AS u ON u.id = a.user_id
            LEFT JOIN noleggiocheckin AS i on i.addetto = a.cf
            LEFT JOIN noleggiocheckout AS o on o.addetto = a.cf
            WHERE f.nome = :filiale
            GROUP by a.cf
            ORDER BY n_out DESC
        ';

        $params = array('filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }
}
