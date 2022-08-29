<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Field.php');

class Car
{
    protected $plate;
    protected $brand;
    protected $model;
    protected $color;
    protected $cost;
    protected $state;
    protected $branch;

    const ON_RENTAL = 'in_noleggio';
    const AVAILABLE = 'disponibile';
    const UNAVAILABLE = 'non_disponibile';

    public function __construct($data)
    {
        $this->plate = $data['targa'];
        $this->brand = $data['marca'];
        $this->model = $data['modello'];
        $this->color = $data['colore'];
        $this->cost = $data['costo_giornaliero'];
        $this->state = $data['stato'];
        $this->branch = $data['filiale'];
    }

    public static function getFields()
    {
        return array(
            'plate' => new Field(
                'text',
                'targa',
                'Targa',
                true
            ),
            'brand' => new Field(
                'text',
                'marca',
                'Marca',
                true
            ),
            'model' => new Field(
                'text',
                'modello',
                'Modello',
                true
            ),
            'color' => new Field(
                'text',
                'colore',
                'Colore',
                true,
            ),
            'cost' => new Field(
                'number',
                'costo_giornaliero',
                'Costo giornaliero',
                true
            )
        );
    }

    public function getUpdateFields()
    {
        return array(
            'plate' => new Field(
                'text',
                'targa',
                'Targa',
                true,
                'MW100DR'
            ),
            'brand' => new Field(
                'text',
                'marca',
                'Marca',
                true,
                'Honda'
            ),
            'model' => new Field(
                'text',
                'modello',
                'Modello',
                true,
                'Insight'
            ),
            'color' => new Field(
                'text',
                'colore',
                'Colore',
                true,
                'Nera'
            ),
            'cost' => new Field(
                'number',
                'costo_giornaliero',
                'Costo giornaliero',
                true,
                9
            )
        );
    }

    /* rentals/checkin/car.php */
    public static function getAvailableCarsTable($branch, $actions = NULL)
    {
        $tHeaders = array(
            'targa', 'marca', 'modello',
            'colore', 'costo_giornaliero'
        );

        return new Table($tHeaders, 'targa', self::readAllAvailable($branch), $actions);
    }

    
    public static function getCarsWithRentalsCountTable($branch, $actions = NULL)
    {
        $tHeaders = array(
            'targa', 'veicolo', 'costo_giornaliero', 'stato', 'noleggi'
        );

        return new Table($tHeaders, 'targa', self::readWithRentals($branch), $actions);
    }

    public static function plateExist($targa)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM veicolo
            WHERE targa = :targa
            LIMIT 1
        ';
        
        $params = array('targa' => $targa);

        $stmt = $db->bindQuery($query, $params);

        $reuslt = $db->exist($stmt);

        $db->disconnect();

        return $reuslt;
    }

    // GETTERS
    public function getPlate()
    {
        return $this->plate;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    // CRUD OPERATIONS

    public static function create($data)
    {
        $db = new DB();

        $db->begin();

        $query = '
            INSERT INTO veicolo
            (targa, marca, modello, colore, costo_giornaliero, stato, filiale)
            VALUES
            (:targa, :marca, :modello, :colore, :costo_giornaliero, :stato, :filiale)
        ';

        $params = array('stato' => self::AVAILABLE);

        foreach ($data as $key => $value) {
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

    public static function read($plate)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM veicolo
            WHERE targa = :targa
            LIMIT 1
        ';

        $params = array('targa' => $plate);
        $stmt = $db->bindQuery($query, $params);

        $car = $db->get($stmt);

        $db->disconnect();

        return is_null($car) ? NULL : new Car($car);
    }

    public static function readFromBranch($branch, $plate)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM veicolo
            WHERE targa = :targa AND filiale = :filiale
            LIMIT 1
        ';

        $params = array('targa' => $plate, 'filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $car = $db->get($stmt);

        $db->disconnect();

        return is_null($car) ? NULL : new Car($car);
    }

    public static function readAllAvailable($branch)
    {
        $db = new DB();

        $query = '
            SELECT targa, marca, modello, colore, costo_giornaliero
            FROM veicolo
            WHERE stato = :stato AND filiale = :filiale
            ORDER BY marca ASC, modello ASC, colore ASC, costo_giornaliero DESC
        ';

        $params = array('stato' => self::AVAILABLE, 'filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }

    public static function readWithRentals($branch)
    {
        $db = new DB();

        $query = '
            SELECT v.targa, CONCAT(v.marca, " ",v.modello," ", v.colore) AS car,
            v.costo_giornaliero, v.stato,
            COUNT(n.codice_noleggio) AS rentals
            FROM veicolo AS v
            LEFT JOIN noleggio AS n ON n.veicolo = v.targa
            WHERE v.filiale = :filiale
            GROUP BY v.targa
            ORDER BY rentals DESC, v.marca ASC, v.modello ASC, v.colore ASC
        ';

        $params = array('filiale' => $branch);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        $db->disconnect();

        return $result;
    }

    public function update($data)
    {
        $db = new DB();

        $query = '
            UPDATE veicolo
            SET targa = :targa, marca = :marca, modello = :modello,
            colore = :colore, costo_giornaliero = :costo_giornaliero
            WHERE targa = :c_targa
        ';

        $params = array('c_targa' => $this->plate);

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        $db->disconnect();

        return $status;
    }

    public static function updateState($db, $plate, $state)
    {
        $query = '
            UPDATE veicolo
            SET stato = :stato
            WHERE targa = :targa
        ';

        $params = array(
            'targa' => $plate,
            'stato' => $state
        );

        $status = $db->bindQuery($query, $params);

        return $status;
    }

    public function updateCarState($state)
    {
        $db = new DB();
        
        $query = '
            UPDATE veicolo
            SET stato = :stato
            WHERE targa = :targa
        ';

        $params = array(
            'targa' => $this->plate,
            'stato' => $state
        );

        $status = $db->bindQuery($query, $params);

        $db->disconnect();

        return $status;
    }
}
