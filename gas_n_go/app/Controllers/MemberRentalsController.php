<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Member.php');
require_once(__DIR__ . '/../Models/Rental.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');

class MemberRentalsController extends Controller
{
    public static function index(User $auth)
    {
        $current_page = 'Noleggi';

        $member =  $auth->hasRole();
        
        if (is_null($member)) {
            exit('Error 404');
        }

        $operations = $member->getOperations();
        $table = Rental::getMemberRentalsTable($member->getCF());

        include(__DIR__ . '/../Views/Members/rentals/index.php');
    }
}
