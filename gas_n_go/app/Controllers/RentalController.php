<?php 

require_once(__DIR__ . '/../Models/Rental.php');
require_once(__DIR__ . '/../Models/User.php');

class RentalController
{
    public static function index(User $auth)
    {
        $current_page = 'Noleggi';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }
        
        $operations = $user->getOperations();

        $table = Rental::getRentalsTable();

        include(__DIR__ . '/../Views/Rentals/index.php');
    }
}
