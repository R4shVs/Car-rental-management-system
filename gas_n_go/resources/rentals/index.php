<?php
include('../auth.inc.php');

$role = array(User::ADMIN);
include('../authorization.inc.php');

require_once('../../app/Controllers/RentalController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        RentalController::index($auth);
        break;
    default:
        echo ('Error 405');
}
