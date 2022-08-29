<?php

require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/Controller.php');

class ProfileController extends Controller
{
    public static function show(User $auth)
    {
        $current_page = 'Profilo';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();
        $info =  $user->getProfile();

        include(__DIR__ . '/../Views/Profile/show.php');
    }

    public static function edit(User $auth)
    {
        $current_page = 'Profilo';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }
        
        $operations = $user->getOperations();

        $fields = $auth->getUpdateAuthFields();

        include(__DIR__ . '/../Views/Profile/edit.php');
    }

    public static function update(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/profile/edit.php';

        self::checkFormSubmit($formPath);

        $formName = 'user';
        $fields = $auth->getUpdateAuthFields();
        $uFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if ($auth->passwordVerify($uFields['current_password'])) // Le password non corrispondono
        {
            $msg .= '_error=Credenziali_non_valide._Per_favore_riprova.';
            self::redirect($formPath, $msg);
        }

        if (
            $uFields['email'] != $auth->getEmail() && // Cambio email
            User::emailExist($uFields['email']) // Email gia' presente
        ) {
            $msg .= '_error=Email_non_disponibile';
            self::redirect($formPath, $msg);
        }

        // Nessuna modifica
        if ($uFields['email'] == $auth->getEmail() && empty($uFields['password'])) {
            self::redirect($formPath);
        }

        if (!empty($uFields['password'])) {
            $uFields['password'] = password_hash($uFields['password'],  PASSWORD_BCRYPT);
        }

        $result = $auth->update(
            array(
                'email' => $uFields['email'],
                'password' => $uFields['password'] ?? NULL
            )
        );
        if ($result) {
            $auth->setEmail($uFields['email']);
            $_SESSION['user'] = serialize($auth);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/profile.php');
    }
}
