<?php
include('auth.inc.php');

require_once('../app/Controllers/ProfileController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        ProfileController::show($auth);
        break;
    default:
        echo ('Error 405');
}
