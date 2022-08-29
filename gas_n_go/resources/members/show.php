<?php
include('../auth.inc.php');

$role = array(User::ADMIN, User::EMPLOYEE);
include('../authorization.inc.php');

require_once('../../app/Controllers/MembersController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        MembersController::show($auth);
        break;
    default:
        echo ('Error 405');
}
