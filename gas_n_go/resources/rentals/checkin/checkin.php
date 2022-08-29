<?php
include('../../auth.inc.php');

$role = array(User::EMPLOYEE);
include('../../authorization.inc.php');

require_once('../../../app/Controllers/CheckinController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        CheckinController::store($auth);
        break;
    default:
        echo ('Error 405');
}
