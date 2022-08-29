<?php
include('auth.inc.php');

require_once('../app/Controllers/DashboardController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        DashboardController::show($auth);
        break;
    default:
        echo ('Error 405');
}
