<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Employee.php');
require_once(__DIR__ . '/../Models/Branch.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');

class EmployeesController extends Controller
{
    public static function index(User $auth)
    {
        $current_page = 'Addetti';

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $actions = array(
            new Action('Info', 'employees/show.php?', array('email'), Action::SELECT),
            new Action('Modifica', 'employees/edit.php?', array('email'), Action::UPDATE),
        );

        $operations = $manager->getOperations();

        $branch = $manager->getManagedBranch();
        $table = Branch::getBranchEmployeesTable($branch['name'], $actions);

        include(__DIR__ . '/../Views/Employees/index.php');
    }

    public static function show(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/employees/index.php';

        $formName = 'employee';

        $msg = $formName;

        if (empty($_GET['email'])) {
            $msg .= '_error=Seleziona_un_addetto';
            self::redirect($formPath, $msg);
        }

        $employee = Employee::readFromEmail($_GET['email']);

        if (is_null($employee)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Addetti';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $info =  $employee->getProfile();

        include(__DIR__ . '/../Views/Employees/show.php');
    }

    public static function create(User $auth)
    {
        $current_page = 'Assumi addetto';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $uFields = User::getFields();
        $eFields = Employee::getFields();


        include(__DIR__ . '/../Views/Employees/create.php');
    }

    public static function store(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/employees/create.php';

        self::checkFormSubmit($formPath);

        $formName = 'user';
        $fields = User::getFields();
        $uFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (User::emailExist($uFields['email'])) // Email già presente
        {
            $msg .= '_error=Email_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $formName = 'employee';
        $fields = Employee::getFields();
        $eFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (strlen($eFields['cf']) != 16) {
            $msg .= '_error=Lunghezza_codice_fiscale_errata';
            self::redirect($formPath, $msg);
        }

        if (Person::cfExist($eFields['cf'])) {
            $msg .= '_error=Codice_fiscale_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $eFields['filiale'] = $branch['name'];

        self::checkDates(
            $eFields['data_di_assunzione'],
            $branch['openingDate'],
            $formPath, $formName,
            'La_data_di_assunzione_è_antecedente_all\'apertura_della_filiale'
        );

        $uFields['password'] = password_hash($uFields['password'],  PASSWORD_BCRYPT);

        $uFields['ruolo'] = User::EMPLOYEE;
        

        if (empty($_POST['data_scadenza_contratto']))
            $eFields['data_scadenza_contratto'] = NULL;
        else
        {
            self::checkDates(
                $eFields['data_scadenza_contratto'],
                $eFields['data_di_assunzione'],
                $formPath, $formName,
                'La_data_di_fine_contratto_è_antecedente_a_quella_di_assunzione'
            );
        }

        if (!Employee::create($uFields, $eFields)) {
            $msg = 'error=Non_è_stato_possibile_assumere_l\'_addetto';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }

    public static function edit(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/employees/index.php';

        $formName = 'employee';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_addetto';
            self::redirect($formPath, $msg);
        }

        $employee = Employee::readFromEmail($_GET['email']);

        if (is_null($employee)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Addetti';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $email = $_GET['email'];

        $fields = $employee->getUpdateFields();

        include(__DIR__ . '/../Views/Employees/edit.php');
    }

    public static function update(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/employees/index.php';

        $formName = 'employee';

        $msg = $formName;

        if (empty($_POST['email'])) {

            $msg .= '_error=Seleziona_un_addetto';
            self::redirect($formPath, $msg);
        }

        $employee = Employee::readFromEmail($_POST['email']);

        if (is_null($employee)) { // Email non trovata
            $msg .= '_error=Addetto_non_trovato';
            self::redirect($formPath, $msg);
        }

        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/employees/edit.php';

        self::checkFormSubmit($formPath);

        $formName = 'employee';
        $fields = $employee->getUpdateFields();
        $eFields = self::checkFields($fields, $formPath, $formName);

        $msg = 'email=' . $_POST['email'] . '&' . $formName;

        if (strlen($eFields['cf']) != 16) {
            $msg .= '_error=Lunghezza_codice_fiscale_errata';
            self::redirect($formPath, $msg);
        }

        if (
            $eFields['cf'] != $employee->getCF() && // Cambio CF
            Employee::cfExist($eFields['cf']) // CF gia' presente
        ) {
            $msg .= '_error=Codice_fiscale_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        
        self::checkDates(
            $eFields['data_di_assunzione'],
            $branch['openingDate'],
            $formPath, $msg,
            'La_data_di_assunzione_è_antecedente_all\'apertura_della_filiale'
        );
        
        if (empty($_POST['data_scadenza_contratto']))
            $eFields['data_scadenza_contratto'] = NULL;
        else
        {
            self::checkDates(
                $eFields['data_scadenza_contratto'],
                $eFields['data_di_assunzione'],
                $formPath, $msg ,
                'La_data_di_fine_contratto_è_antecedente_a_quella_di_assunzione'
            );
        }

        if (!$employee->update($eFields)) {
            $msg .= '_error=Impossibile_aggiornare_addetto';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
