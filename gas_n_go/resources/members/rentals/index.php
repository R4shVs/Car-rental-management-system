<?php
include('../../auth.inc.php');

$role = array(User::MEMBER);
include('../../authorization.inc.php');

require_once('../../../app/Controllers/MemberRentalsController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        MemberRentalsController::index($auth);
        break;
    default:
        echo ('Error 405');
}
