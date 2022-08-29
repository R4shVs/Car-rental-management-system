<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Car.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');

class CarsController extends Controller
{
    public static function index(User $auth)
    {
        $current_page = 'Veicoli';

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $actions = array(
            new Action('Stato', 'cars/edit_state.php?', array('targa'), Action::SELECT),
            new Action('Modifica', 'cars/edit.php?', array('targa'), Action::UPDATE),
        );

        $operations = $manager->getOperations();

        $branch = $manager->getManagedBranch();
        $table = Car::getCarsWithRentalsCountTable($branch['name'], $actions);

        include(__DIR__ . '/../Views/Cars/index.php');
    }

    public static function create(User $auth)
    {
        $current_page = 'Aggiungi veicolo';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $fields = Car::getFields();

        include(__DIR__ . '/../Views/Cars/create.php');
    }

    public static function store(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/create.php';

        self::checkFormSubmit($formPath);

        $formName = 'car';
        $fields = Car::getFields();
        $cFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (Car::plateExist($cFields['targa'])) { // Targa gia' presente
            $msg .= '_error=La_targa_appartiene_a_un_altro_veicolo';
            self::redirect($formPath, $msg);
        }

        if (strlen($cFields['targa']) != 7) {
            $msg .= '_error=Lunghezza_targa_errata';
            self::redirect($formPath, $msg);
        }

        if ($cFields['costo_giornaliero'] < 5) {
            $msg .= '_error=Il_costo_giornaliero_minimo_deve_essere_di_5€';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $cFields['filiale'] = $branch['name'];

        if (!Car::create($cFields)) {
            $msg = 'error=Non_è_stato_possibile_aggiungere_il_veciolo';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }

    public static function edit(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/index.php';

        $formName = 'car';

        $msg = $formName;

        if (empty($_GET['targa'])) {

            $msg .= '_error=Seleziona_un_veicolo';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $car = Car::readFromBranch($branch['name'], $_GET['targa']);

        if (is_null($car)) { // Targa non trovata
            $msg .= '_error=veicolo_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Veicoli';

        $operations = $manager->getOperations();

        $targa = $_GET['targa'];

        $fields = $car->getUpdateFields();

        include(__DIR__ . '/../Views/Cars/edit.php');
    }

    public static function update(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/index.php';

        $formName = 'car';

        $msg = $formName;

        if (empty($_POST['c_targa'])) {

            $msg .= '_error=Seleziona_un_veicolo';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $car = Car::readFromBranch($branch['name'], $_POST['c_targa']);

        if (is_null($car)) { // Targa non trovata
            $msg .= '_error=veicolo_non_trovato';
            self::redirect($formPath, $msg);
        }

        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/edit.php';

        self::checkFormSubmit($formPath);

        $formName = 'car';
        $fields = $car->getUpdateFields();
        $cFields = self::checkFields($fields, $formPath, $formName);

        $msg = 'targa=' . $_POST['c_targa'] . '&' . $formName;

        if (strlen($cFields['targa']) != 7) {
            $msg .= '_error=Lunghezza_targa_errata';
            self::redirect($formPath, $msg);
        }

        if (
            $cFields['targa'] != $car->getPlate() && // Cambio targa
            Car::plateExist($cFields['targa']) // targa gia' presente
        ) {
            $msg .= '_error=La_targa_appartiene_a_un_altro_veicolo';
            self::redirect($formPath, $msg);
        }

        if ($cFields['costo_giornaliero'] < 5) {
            $msg .= '_error=Il_costo_giornaliero_minimo_deve_essere_di_5€';
            self::redirect($formPath, $msg);
        }

        if (!$car->update($cFields)) {
            $msg .= '_error=Impossibile_aggiornare_veicolo';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }

    public static function editState(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/index.php';

        $formName = 'car';

        $msg = $formName;

        if (empty($_GET['targa'])) {

            $msg .= '_error=Seleziona_un_veicolo';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $car = Car::readFromBranch($branch['name'], $_GET['targa']);

        if (is_null($car)) { // Targa non trovata
            $msg .= '_error=veicolo_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Veicoli';

        $operations = $manager->getOperations();

        $targa = $_GET['targa'];

        $state = $car->getState();

        include(__DIR__ . '/../Views/Cars/edit_state.php');
    }

    public static function updateState(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/index.php';

        $formName = 'car';

        $msg = $formName;

        if (empty($_POST['targa'])) {

            $msg .= '_error=Seleziona_un_veicolo';
            self::redirect($formPath, $msg);
        }

        $manager = $auth->hasRole();

        if (is_null($manager)) {
            exit('Error 404');
        }

        $branch = $manager->getManagedBranch();

        $car = Car::readFromBranch($branch['name'], $_POST['targa']);

        if (is_null($car)) { // Targa non trovata
            $msg .= '_error=veicolo_non_trovato';
            self::redirect($formPath, $msg);
        }

        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/cars/edit_state.php';

        self::checkFormSubmit($formPath);

        $formName = 'car';

        $msg = 'targa=' . $_POST['targa'] . '&' . $formName;

        if($car->getState() === Car::ON_RENTAL){
            $msg .= '_error=Non_è_possibile_aggiornare_veicolo_in_noleggio';
            self::redirect($formPath, $msg);

        }
        else{
            $state = $car->getState() === Car::AVAILABLE ?
            Car::UNAVAILABLE : Car::AVAILABLE;
        }

        if (!$car->updateCarState($state)) {
            $msg .= '_error=Impossibile_aggiornare_veicolo';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
