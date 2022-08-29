<?php
require_once(__DIR__ . '/../Models/User.php');

class DashboardController
{
    public static function show(User $auth)
    {
        $current_page = 'Dashboard';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }
        
        $operations = $user->getOperations();

        $stats = $user->getStats();
        $userChart = $user->getChart();

        $chart = $userChart->plot();
        $chartTitle = $userChart->getTitle();
        $chartType = $userChart->getType();

        include(__DIR__ . '/../Views/DashboardView.php');
    }
}
