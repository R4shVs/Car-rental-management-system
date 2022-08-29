<?php

require_once(__DIR__ . '/../Controller.php');
require_once(__DIR__ . '/../../Models/User.php');
require_once(__DIR__ . '/../../Models/Employee.php');

class LoginController extends Controller
{
    public static function show()
    {
        $fields = User::getFields();

        require_once(__DIR__ . '/../../Views/Auth/LoginView.php');
    }

    public static function login()
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/login.php';
        
        self::checkFormSubmit($formPath);

        $formName = 'user';
        $fields = User::getFields();
        $uFields = self::checkFields($fields, $formPath, $formName);

        $user = User::read($uFields['email']);

        $msg = $formName;

        if (
            is_null($user) || // Email non trovata
            !$user->passwordVerify($uFields['password']) // Le password non corrispondono
        ) {
            $msg .= '_error=Credenziali_non_valide._Per_favore_riprova.';
            self::redirect($formPath, $msg);
        }

        // Controllo se l'addetto lavora ancora nella sua filiale
        if ($user->getRole() === User::EMPLOYEE) {
            $employee =  $user->hasRole();

            if (is_null($employee) || $employee->hasContractExpired()) {
                $msg .= '_error=Accesso_negato._Il_suo_contratto_risulta_scaduto';
                self::redirect($formPath, $msg);
            }
        }

        session_start();

        $_SESSION['user'] = serialize($user);

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }

    public static function logout()
    {
        session_start();
    
        session_unset();
        session_destroy();
        
        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
