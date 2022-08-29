<?php
include('../../auth.inc.php');

$role = array(User::EMPLOYEE);
include('../../authorization.inc.php');

require_once('../../../app/Controllers/CheckoutController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        CheckoutController::selectRental($auth);
        break;
    default:
        echo ('Error 405');
}
