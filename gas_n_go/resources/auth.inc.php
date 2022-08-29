<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: http://localhost/ProgettoDB/gas_n_go/resources/login.php');
    die();
}

require_once(__DIR__ . '../../app/Models/User.php');
$auth = unserialize($_SESSION['user']);