<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Manager.php');
require_once(__DIR__ . '/../Models/Branch.php');
require_once(__DIR__ . '/../Models/Rental.php');

class BranchesController extends Controller
{
    public static function index(User $auth)
    {
        $current_page = 'Filiali';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }
        
        $actions = array(
            new Action('Info', 'branches/show.php?', array('nome'), Action::SELECT),
        );

        $operations = $user->getOperations();
        $table = Branch::getBranchesTable($actions);

        include(__DIR__ . '/../Views/Branches/index.php');
    }

    public static function rentalsIndex(User $auth)
    {
        $current_page = 'Noleggi';

        $manager =  $auth->hasRole();
        
        if (is_null($manager)) {
            exit('Error 404');
        }

        $operations = $manager->getOperations();
        $table = Rental::getBranchRentalsTable($manager->getCF());

        include(__DIR__ . '/../Views/Members/rentals/index.php');
    }
    
    public static function show(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/branches/index.php';

        $formName = 'branch';

        $msg = $formName;

        if (empty($_GET['nome'])) {

            $msg .= '_error=Seleziona_una_filiale';
            self::redirect($formPath, $msg);
        }

        $branch = Branch::read($_GET['nome']);

        if (is_null($branch)) { // Filiale non trovata
            $msg .= '_error=Filiale_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Filiali';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $info =  $branch->getBranchData();

        include(__DIR__ . '/../Views/Branches/show.php');
    }

    public static function create(User $auth)
    {
        $current_page = 'Nuova filiale';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $uFields = User::getFields();
        $mFields = Manager::getFields();
        $bFields = Branch::getFields();

        $operations = $user->getOperations();

        include(__DIR__ . '/../Views/Branches/create.php');
    }

    public static function store()
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/branches/create.php';

        self::checkFormSubmit($formPath);

        // UTENTE 
        $formName = 'user';
        $fields = User::getFields();
        $uFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (User::emailExist($uFields['email'])) // Email già presente
        {
            $msg .= '_error=Email_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $uFields['password'] = password_hash($uFields['password'],  PASSWORD_BCRYPT);

        $uFields['ruolo'] = User::MANAGER;

        // MANAGER
        $formName = 'manager';
        $fields = Manager::getFields();
        $mFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (strlen($mFields['cf']) != 16) {
            $msg .= '_error=Lunghezza_codice_fiscale_errata';
            self::redirect($formPath, $msg);
        }

        if (Person::cfExist($mFields['cf'])) {
            $msg .= '_error=Codice_fiscale_non_disponibile';
            self::redirect($formPath, $msg);
        }

        // FILIALE

        $formName = 'branch';
        $fields = Branch::getFields();
        $bFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (Branch::nameExist($bFields['nome_filiale'])) {
            $msg .= '_error=Nome_non_disponibile';
            self::redirect($formPath, $msg);
        }

        date_default_timezone_set('Europe/Rome');
        $bFields['data_di_apertura'] = date('Y-m-d');
        $bFields['manager'] = $mFields['cf'];

        if (!Branch::create($uFields, $mFields, $bFields)) {
            $msg = 'error=Non_è_stato_possibile_creare_la_filiale';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
