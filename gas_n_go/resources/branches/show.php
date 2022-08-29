<?php
include('../auth.inc.php');

$role = array(User::ADMIN);
include('../authorization.inc.php');

require_once('../../app/Controllers/BranchesController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        BranchesController::show($auth);
        break;
    default:
        echo ('Error 405');
}
