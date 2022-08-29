<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Member.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');
require_once(__DIR__ . '/../Models/Table.php');
require_once(__DIR__ . '/../Models/Employee.php');
require_once(__DIR__ . '/../Models/Rental.php');

class CheckoutController extends Controller
{
    public static function selectMember(User $auth)
    {
        $current_page = 'Check-out noleggio';

        $actions = array(
            new Action('Seleziona', 'rentals/checkout/rental.php?', array('email'), Action::SELECT),
        );

        $employee =  $auth->hasRole();

        if (is_null($employee)) {
            exit('Error 404');
        }

        $operations = $employee->getOperations();
        $table = Rental::getActiveMembersRentalsTable($employee->getBranch(), $actions);
        
        include(__DIR__ . '/../Views/Rentals/selectMember.php');
    }

    public static function selectRental(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkout/member.php';

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

        $current_page = 'Check-out noleggio';

        $actions = array(
            new Action(
                'Termina',
                'rentals/checkout/checkout.php?',
                array('codice_noleggio'),
                Action::SELECT
            ),
        );

        $employee =  $auth->hasRole();

        if (is_null($employee)) {
            exit('Error 404');
        }

        $operations = $employee->getOperations();
        $table = Rental::getActiveMemberRentalsTable($member->getCF(), $employee->getBranch(), $actions);

        include(__DIR__ . '/../Views/Rentals/Checkout/selectRental.php');
    }

    public static function store(User $auth)
    {
        // Controllo Noleggio
        
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkout/rental.php';

        $formName = 'rental';

        $msg = $formName;

        if (empty($_GET['codice_noleggio'])) {

            $msg .= '_error=Seleziona_un_noleggio';
            self::redirect($formPath, $msg);
        }

        $rental = Rental::read($_GET['codice_noleggio']);

        $employee =  $auth->hasRole();

        if (
            $rental->getBranch() != $employee->getBranch() ||
            !$rental->isActive()
        ) {
            $msg .= '_error=Noleggio_non_trovato';
            self::redirect($formPath, $msg);
        }

        // Check-out
        $checkoutData = array(
            'addetto' => $employee->getCF(),
            'noleggio' => $rental->getCode()
        );

        date_default_timezone_set('Europe/Rome');
        $checkoutData['data_operazione'] = date('Y-m-d H:i:s');

        $checkinInfo = $rental->getCheckinInfo();

        $checkoutDate = new DateTime($checkoutData['data_operazione']);
        $checkinDate = new DateTime($checkinInfo['date']);

        $checkoutData['costo'] = 
            (($checkinDate->diff($checkoutDate)->format('%a')) + 1)*$checkinInfo['price'];


        if (!Rental::createCheckout($checkoutData, $rental->getCar())) {
            $msg = 'error=Non_è_stato_possibile_terminare_il_noleggio';
            self::redirect($formPath, $msg);
        }
        
        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
