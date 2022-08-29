<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Member.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');
require_once(__DIR__ . '/../Models/Table.php');
require_once(__DIR__ . '/../Models/Employee.php');
require_once(__DIR__ . '/../Models/Car.php');
require_once(__DIR__ . '/../Models/Rental.php');

class CheckinController extends Controller
{
    public static function selectMember(User $auth)
    {
        $current_page = 'Check-in noleggio';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $actions = array(
            new Action('Seleziona', 'rentals/checkin/car.php?', array('email'), Action::SELECT),
        );
        
        $operations = $user->getOperations();
        $table = Member::getMembersTable($actions);

        include(__DIR__ . '/../Views/Rentals/selectMember.php');
    }

    public static function selectCar(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkin/member.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Check-in noleggio';

        $actions = array(
            new Action(
                'Seleziona',
                'rentals/checkin/checkin.php?email=' . $_GET['email'] . '&',
                array('targa'),
                Action::SELECT
            ),
        );

        $employee =  $auth->hasRole();

        if (is_null($employee)) {
            exit('Error 404');
        }

        $operations = $employee->getOperations();

        $table = Car::getAvailableCarsTable($employee->getBranch(), $actions);

        include(__DIR__ . '/../Views/Rentals/Checkin/selectCar.php');
    }

    public static function store(User $auth)
    {
        // Controllo socio
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkin/member.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        // Controllo veicolo
        $carFormPath = 'http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkin/car.php';

        $carFormName = 'car';

        $carMsg = 'email=' . $_GET['email'] . '&' . $carFormName;

        if (empty($_GET['targa'])) {

            $carMsg .= '_error=Seleziona_un_veicolo';
            self::redirect($carFormPath, $carMsg);
        }

        $car = Car::read($_GET['targa']);

        $employee =  $auth->hasRole();

        if (
            is_null($car) ||
            $car->getState() != 'disponibile' ||
            $car->getBranch() != $employee->getBranch()
        ) { // Veicolo non trovato
            $carMsg .= '_error=Veicolo_non_trovato';
            self::redirect($carFormPath, $carMsg);
        }

        // Check-in

        $rentalData = array(
            'veicolo' =>  $car->getPlate(),
            'filiale' =>  $employee->getBranch(),
            'socio' =>  $member->getCF(),
        );


        $checkinData = array('addetto' => $employee->getCF());

        date_default_timezone_set('Europe/Rome');
        $checkinData['data_operazione'] = date('Y-m-d H:i:s');
 
        if (!Rental::createCheckin($rentalData, $checkinData)) {
            $msg = 'error=Non_è_stato_possibile_inserire_il_noleggio';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
