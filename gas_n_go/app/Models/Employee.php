<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Person.php');
require_once(__DIR__ . '/Operation.php');
require_once(__DIR__ . '/Action.php');
require_once(__DIR__ . '/Stat.php');
require_once(__DIR__ . '/Chart.php');

class Employee extends Person
{
    protected $hireDate;
    protected $contractExpiryDate;
    protected $branch;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->hireDate = $data['data_di_assunzione'];
        $this->contractExpiryDate = $data['data_scadenza_contratto'];
        $this->branch = $data['filiale'];
    }

    public static function getFields()
    {
        $employeeFields = array(
            'hireDate' => new Field(
                'date',
                'data_di_assunzione',
                'Data di assunzione',
                true
            ),
            'contractExpiryDate' => new Field(
                'date',
                'data_scadenza_contratto',
                'Data di scadenza contratto',
                false
            )
        );

        return array_merge(parent::getFields(), $employeeFields);
    }
    
    public function getUpdateFields()
    {
        $employeeUpdateFields = array(
            'hireDate' => new Field(
                'date',
                'data_di_assunzione',
                'Data di assunzione',
                true,
                $this->hireDate
            ),
            'contractExpiryDate' => new Field(
                'date',
                'data_scadenza_contratto',
                'Data di scadenza contratto',
                false,
                $this->contractExpiryDate
            )
        );

        return array_merge(parent::getUpdateFields(), $employeeUpdateFields);
    }

    public static function getOperations()
    {
        return array(
            'createMember' => new Operation('Registra socio', 'members/create.php'),
            'checkIn' => new Operation('Check-in noleggio', 'rentals/checkin/member.php'),
            'checkOut' => new Operation('Check-out noleggio', 'rentals/checkout/member.php'),
            'indexMember' => new Operation('Soci', 'members/index.php')
        );
    }

    public static function getActionsOnMembers()
    {
        return array(
            new Action('Modifica', 'members/edit.php?', array('email'), Action::UPDATE),
            new Action('Cancella', 'members/confirm_delete.php?', array('email'), Action::DELETE),
        );
    }

    public function getStats()
    {
        $db = new DB();

        $stats = array(
            'membersManaged' => new Stat(
                'Soci gestiti',
                $this->queryMembersManagedCount($db)
            ),
            'completeRentalsCount' => new Stat(
                'Noleggi completi',
                $this->queryCompleteRentalsCount($db)
            ),
            'checkInCount' => new Stat(
                'Noleggi registrati',
                $this->queryCheckInCount($db)
            ),
            'checkOutCount' => new Stat(
                'Noleggi terminati',
                $this->queryCheckOutCount($db)
            )
        );

        $db->disconnect();

        return $stats;
    }

    public function getChart()
    {
        $char = new Chart('Noleggi gestiti', Chart::LINE, 'rentals');

        $db = new DB();

        $rentals = $this->queryMonthlyRentalsCount($db);

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
        $employeeProfile = array(
            'Data di assunzione' => $this->hireDate,
            'Data scadenza contratto' => $this->contractExpiryDate ?? 'Tempo indeterminato'
        );

        return array_merge(parent::getProfile(), $employeeProfile);
    }

    public function hasContractExpired()
    {
        date_default_timezone_set('Europe/Rome');
        $now = date('Y-m-d');

        // Se il contratto non ha una data di scadeza (NULLL) e' valido
        return strtotime($now) > strtotime($this->contractExpiryDate ?? $now);
    }

    // QUERIES

    // STATS
    private function queryMembersManagedCount($db)
    {
        $query = '
            SELECT COUNT(DISTINCT s.cf) AS count
            FROM addetto AS a, socio AS s
            JOIN noleggio AS n ON n.socio = s.cf
            WHERE a.cf = :cf AND (n.codice_noleggio IN (
                SELECT i.noleggio FROM noleggiocheckin AS i WHERE i.addetto = a.cf
            ) OR n.codice_noleggio IN (
                SELECT o.noleggio FROM noleggiocheckout AS o WHERE o.addetto = a.cf
            ))
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryCompleteRentalsCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM addetto AS a
            JOIN noleggiocheckin AS i ON i.addetto = a.cf
            JOIN noleggiocheckout AS o ON o.addetto = a.cf
            WHERE a.cf = :cf AND i.noleggio = o.noleggio
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryCheckInCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM addetto AS a
            JOIN noleggiocheckin AS i ON i.addetto = a.cf
            WHERE a.cf = :cf
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryCheckOutCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM addetto AS a
            JOIN noleggiocheckout AS o ON o.addetto = a.cf
            WHERE a.cf = :cf
        ';

        $params = array('cf' => $this->cf);
        $stmt = $db->bindQuery($query, $params);

        $result = $db->get($stmt);

        return is_null($result) ? '#' : $result['count'];
    }

    // CHART
    private function queryMonthlyRentalsCount($db)
    {
        $query = '
            SELECT SUM(rentals) AS rentals, day
            FROM (
                SELECT DAY(i.data_operazione) AS day, COUNT(*) AS rentals
                FROM noleggiocheckin AS i
                WHERE i.addetto = :cf
                AND MONTH(i.data_operazione) = MONTH(CURRENT_DATE())
                AND YEAR(i.data_operazione) = YEAR(CURRENT_DATE())
                GROUP BY DAY(i.data_operazione)

                UNION ALL

                SELECT DAY(o.data_operazione) AS day, COUNT(*) AS rentals
                FROM noleggiocheckout AS o
                WHERE o.addetto = :cf
                AND MONTH(o.data_operazione) = MONTH(CURRENT_DATE())
                AND YEAR(o.data_operazione) = YEAR(CURRENT_DATE())
                GROUP BY DAY(o.data_operazione)
            ) t
            GROUP BY day
        ';

        $params = array('cf' => $this->cf);

        $stmt = $db->bindQuery($query, $params);

        $result = $db->getAll($stmt);

        return $result;
    }

    // GETTERS

    public function getBranch()
    {
        return $this->branch;
    }

    // CRUD
    public static function create($uData, $eData)
    {
        $db = new DB();

        $db->begin();

        if (!User::create($db, $uData)) {
            $db->rollBack();
            return false;
        }

        $query = '
            INSERT INTO addetto
            (
                user_id, cf, nome, cognome, data_di_nascita, domicilio,
                numero_di_telefono, data_di_assunzione, data_scadenza_contratto, filiale
            )
            VALUES
            (
                :user_id, :cf, :nome, :cognome, :data_di_nascita, :domicilio,
                :numero_di_telefono, :data_di_assunzione, :data_scadenza_contratto, :filiale
            )
        ';

        $params = array('user_id' => $db->lastInsertId());

        foreach ($eData as $key => $value) {
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
            FROM addetto
            WHERE user_id = :id
            LIMIT 1
        ';

        $params = array('id' => $id);
        $stmt = $db->bindQuery($query, $params);

        $employee = $db->get($stmt);

        $db->disconnect();

        return  is_null($employee) ? NULL : new Employee($employee);
    }

    public static function readFromEmail($email)
    {
        $db = new DB();

        $query = '
            SELECT a.* FROM addetto AS a
            JOIN utente AS u ON u.ID = a.user_id
            WHERE u.email = :email
            LIMIT 1
        ';

        $params = array('email' => $email);
        $stmt = $db->bindQuery($query, $params);

        $employee = $db->get($stmt);

        $db->disconnect();

        return is_null($employee) ? NULL : new Employee($employee);
    }

    public function update($data)
    {
        $db = new DB();

        $query = '
            UPDATE addetto
            SET cf = :cf, nome = :nome, cognome = :cognome,
            data_di_nascita = :data_di_nascita, domicilio = :domicilio,
            numero_di_telefono = :numero_di_telefono, data_di_assunzione = :data_di_assunzione,
            data_scadenza_contratto = :data_scadenza_contratto
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
}
