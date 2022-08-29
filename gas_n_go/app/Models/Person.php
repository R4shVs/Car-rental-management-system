<?php

require_once(__DIR__ . '/Field.php');
require_once(__DIR__ . '/../Models/Table.php');

class Person
{
    protected $id;
    protected $cf;
    protected $name;
    protected $surname;
    protected $birthday;
    protected $domicile;
    protected $telephoneNumber;

    public function __construct($data)
    {
        $this->id = $data['user_id'];
        $this->cf = $data['cf'];
        $this->name = $data['nome'];
        $this->surname = $data['cognome'];
        $this->birthday = $data['data_di_nascita'];
        $this->domicile = $data['domicilio'];
        $this->telephoneNumber = $data['numero_di_telefono'];
    }
    
    public static function getFields()
    {
        return array(
            'cf' => new Field(
                'text',
                'cf',
                'Codice fiscale',
                true
            ),
            'name' => new Field(
                'text',
                'nome',
                'Nome',
                true
            ),
            'surname' => new Field(
                'text',
                'cognome',
                'Cognome',
                true
            ),
            'birthday' => new Field(
                'date',
                'data_di_nascita',
                'Data di nascita',
                true
            ),
            'domicile' => new Field(
                'text',
                'domicilio',
                'Domicilio',
                true
            ),
            'telephoneNumber' => new Field(
                'text',
                'numero_di_telefono',
                'Numero di telefono',
                true
            )
        );
    }

    public function getUpdateFields()
    {
        return array(
            'cf' => new Field(
                'text',
                'cf',
                'Codice fiscale',
                true,
                $this->cf
            ),
            'name' => new Field(
                'text',
                'nome',
                'Nome',
                true,
                $this->name
            ),
            'surname' => new Field(
                'text',
                'cognome',
                'Cognome',
                true,
                $this->surname
            ),
            'birthday' => new Field(
                'date',
                'data_di_nascita',
                'Data di nascita',
                true,
                $this->birthday
            ),
            'domicile' => new Field(
                'text',
                'domicilio',
                'Domicilio',
                true,
                $this->domicile
            ),
            'telephoneNumber' => new Field(
                'text',
                'numero_di_telefono',
                'Numero di telefono',
                true,
                $this->telephoneNumber
            )
        );
    }

    public function getProfile()
    {
        return array(
            'Codice fiscale' => $this->cf,
            'Nome' => $this->name,
            'Cognome' => $this->surname,
            'Data di nascita' => $this->birthday,
            'Domicilio' => $this->domicile,
            'Numero di telefono' => $this->telephoneNumber
        );
    }

    public static function getIndexTable($personInfo, $actions = NULL)
    {
        $tHeaders = array('email', 'nome', 'cognome');

        return new Table($tHeaders, 'email', $personInfo, $actions);
    }

    // GETTERS

    public function getCF()
    {
        return $this->cf;
    }

    public static function cfExist($cf)
    {
        $db = new DB();

        $query = '
            SELECT cf FROM manager WHERE manager.cf = :cf
            UNION
            SELECT cf FROM addetto WHERE addetto.cf = :cf;
            LIMIT 1
        ';
        $params = array('cf' => $cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->exist($stmt);

        $db->disconnect();

        return $result;
    }
}
