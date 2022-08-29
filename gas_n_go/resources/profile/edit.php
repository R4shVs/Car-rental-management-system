<?php
include('../auth.inc.php');

require_once('../../app/Controllers/ProfileController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        ProfileController::edit($auth);
        break;
    case 'POST':
        ProfileController::update($auth);
        break;
    default:
        echo ('Error 405');
}
