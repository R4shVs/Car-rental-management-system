<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Admin.php');
require_once(__DIR__ . '/Manager.php');
require_once(__DIR__ . '/Employee.php');
require_once(__DIR__ . '/Member.php');
require_once(__DIR__ . '/Field.php');

class User
{
    protected $id;
    protected $email;
    protected $password;
    protected $role;

    const ADMIN = 0;
    const MANAGER = 1;
    const EMPLOYEE = 2;
    const MEMBER = 3;

    public function __construct($data)
    {
        $this->email = $data['email'];
        $this->id = $data['ID'];
        $this->password = $data['password'];
        $this->role = $data['ruolo'];
    }

    public static function getFields()
    {
        return array(
            'email' => new Field('email', 'email', 'Email', true),
            'password' => new Field('password', 'password', 'Password', true)
        );
    }

    public function getUpdateAuthFields()
    {
        return array(
            'email' => new Field('email', 'email', 'Email', true, $this->email),
            'current_password' => new Field('password', 'current_password', 'Password attuale', true),
            'password' => new Field('password', 'password', 'Modifica password', false)
        );
    }

    public function passwordVerify($password)
    {
        return password_verify($password,  $this->password);
    }

    public function hasRole()
    {
        switch ($this->role) {
            case self::ADMIN:
                return new Admin();
            case self::MANAGER:
                return Manager::read($this->id);
            case self::EMPLOYEE:
                return Employee::read($this->id);
            case self::MEMBER:
                return Member::read($this->id);;
        }
    }

    public static function emailExist($email)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM utente
            WHERE email = :email
            LIMIT 1
        ';

        $params = array('email' => $email);

        $stmt = $db->bindQuery($query, $params);

        $reuslt = $db->exist($stmt);

        $db->disconnect();

        return $reuslt;
    }


    // GET METHODS

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }

    // SET METHODS
    public function setEmail($email)
    {
        $this->email = $email;
    }


    // CRUD OPERATIONS
    public static function create($db, $data)
    {
        $query = '
            INSERT INTO utente (email, password, ruolo)
            VALUES (:email, :password, :ruolo);
        ';

        $params = [];

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        $status = $db->bindQuery($query, $params);

        return $status;
    }

    public static function read($email)
    {
        $db = new DB();

        $query = '
            SELECT *
            FROM utente
            WHERE email = :email
            LIMIT 1
        ';

        $params = array('email' => $email);
        $stmt = $db->bindQuery($query, $params);

        $user = $db->get($stmt);

        $db->disconnect();

        return is_null($user) ? NULL : new User($user);
    }

    public function update($data)
    {
        $db = new DB();

        try {
            if (
                is_null($data['email']) &&
                $data['email'] == $this->email &&
                is_null($data['password'])
            ) {
                exit('Error 400');
            }
        } catch (Exception $e) {
            exit('Error 500');
        }

        $query = '
            UPDATE utente
            SET email = :email, password = :password
            WHERE ID = :id
        ';

        $params = array(
            'email' => $data['email'],
            'password' => $data['password'] ?? $this->password,
            'id' => $this->id,
        );

        $status = $db->bindQuery($query, $params);

        $db->disconnect();

        return $status;
    }

    public function delete()
    {
    }
}
