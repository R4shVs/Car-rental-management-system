<?php

require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/../Models/Member.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Action.php');

class MembersController extends Controller
{
    public static function index(User $auth)
    {
        $current_page = 'Soci';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $actions = $user->getActionsOnMembers();
        $operations = $user->getOperations();

        $table = Member::getMembersTable($actions);

        include(__DIR__ . '/../Views/Members/index.php');
    }

    public static function show(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/index.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {
            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Soci';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $info =  $member->getProfileWithRentals();

        include(__DIR__ . '/../Views/Members/show.php');
    }

    public static function create(User $auth)
    {
        $current_page = 'Registra socio';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $mFields = Member::getFields();
        $uFields = User::getFields();

        include(__DIR__ . '/../Views/Members/create.php');
    }

    public static function store()
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/create.php';

        self::checkFormSubmit($formPath);

        $formName = 'user';
        $fields = User::getFields();
        $uFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (User::emailExist($uFields['email'])) // Email già presente
        {
            $msg .= '_error=Email_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $formName = 'member';
        $fields = Member::getFields();
        $mFields = self::checkFields($fields, $formPath, $formName);

        $msg = $formName;

        if (strlen($mFields['cf']) != 16) {
            $msg .= '_error=Lunghezza_codice_fiscale_errata';
            self::redirect($formPath, $msg);
        }

        if (Member::cfExist($mFields['cf'])) {
            $msg .= '_error=Codice_fiscale_non_disponibile';
            self::redirect($formPath, $msg);
        }

        $uFields['password'] = password_hash($uFields['password'],  PASSWORD_BCRYPT);

        $uFields['ruolo'] = User::MEMBER;

        date_default_timezone_set('Europe/Rome');
        $mFields['data_di_iscrizione'] = date('Y-m-d');


        if (!Member::create($uFields, $mFields)) {
            $msg = 'error=Non_è_stato_possibile_creare_il_socio';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/rentals/checkin/car.php?email='
            . $uFields['email']);
    }

    public static function edit(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/index.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Soci';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $email = $_GET['email'];

        $fields = $member->getUpdateFields();

        include(__DIR__ . '/../Views/Members/edit.php');
    }

    public static function update()
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/index.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_POST['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_POST['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/edit.php';

        self::checkFormSubmit($formPath);

        $formName = 'member';
        $fields = $member->getUpdateFields();
        $mFields = self::checkFields($fields, $formPath, $formName);

        $msg = 'email=' . $_POST['email'] . '&' . $formName;

        if (strlen($mFields['cf']) != 16) {
            $msg .= '_error=Lunghezza_codice_fiscale_errata';
            self::redirect($formPath, $msg);
        }

        if (
            $mFields['cf'] != $member->getCF() && // Cambio CF
            Member::cfExist($mFields['cf']) // CF gia' presente
        ) {
            $msg .= '_error=Codice_fiscale_non_disponibile';
            self::redirect($formPath, $msg);
        }

        if (!$member->update($mFields)) {
            $msg .= '_error=Impossibile_aggiornare_socio';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }

    public static function confirmDelete(User $auth)
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/confirm_delete.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        $current_page = 'Soci';

        $user = $auth->hasRole();

        if (is_null($user)) {
            exit('Error 404');
        }

        $operations = $user->getOperations();

        $info =  $member->getProfileWithRentals();

        $email = $_GET['email'];

        include(__DIR__ . '/../Views/Members/delete.php');
    }

    public static function delete()
    {
        $formPath = 'http://localhost/ProgettoDB/gas_n_go/resources/members/index.php';

        $formName = 'member';

        $msg = $formName;

        if (empty($_GET['email'])) {

            $msg .= '_error=Seleziona_un_socio';
            self::redirect($formPath, $msg);
        }

        $member = Member::readFromEmail($_GET['email']);

        if (is_null($member)) { // Email non trovata
            $msg .= '_error=Socio_non_trovato';
            self::redirect($formPath, $msg);
        }

        if ($member->getMemberActiveRentalsCount() > 0) { // Noleggi attivi
            $msg .= '_error=Concludere_i_noleggi_attivi_prima_di_eliminare_il_socio';
            self::redirect($formPath, $msg);
        }

        if (!$member->delete()) {
            $msg .= '_error=Impossibile_eliminare_socio';
            self::redirect($formPath, $msg);
        }

        self::redirect('http://localhost/ProgettoDB/gas_n_go/resources/');
    }
}
