<?php
include('../../auth.inc.php');

$role = array(User::MANAGER);
include('../../authorization.inc.php');

require_once('../../../app/Controllers/BranchesController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        BranchesController::rentalsIndex($auth);
        break;
    default:
        echo ('Error 405');
}
