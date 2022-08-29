<?php

class Controller
{
    protected static function redirect($formPath, $msg = NULL)
    {
        header('Location: ' . $formPath . (!is_null($msg) ? '?' . $msg : ''));
        die();
    }

    protected static function checkFormSubmit($formPath)
    {
        if (!(isset($_POST['submit']) || isset($_GET['submit']))) {
            self::redirect($formPath);
        }
    }

    protected static function checkFields($formFields, $formPath, $formName)
    {
        $msg = $formName . '_error=';
        $fields = array();
        foreach ($formFields as $field) {
            // Controlla se un campo richiesto e' vuoto
            if ($field->isRequired && empty($_POST[$field->name])) {
                $msg .= 'Campo_' . strtolower(str_replace(' ', '_', $field->name)) . '_mancante';
                self::redirect($formPath, $msg);
            }

            // Se un campo non richiesto e' vuoto passo al prossimo campo
            if (empty($_POST[$field->name])) {
                continue;
            }

            switch ($field->type) {
                case 'email':
                    if (
                        !filter_var($_POST[$field->name], FILTER_VALIDATE_EMAIL) &&
                        !empty($_POST[$field->name])
                    ) {
                        $msg .= 'Email_non_valida';
                        self::redirect($formPath, $msg);
                    }
                    break;
                case 'password':
                    if (
                        strlen($_POST[$field->name]) < 8 &&
                        !empty($_POST[$field->name])
                    ) {
                        $msg .= 'Password_troppo_corta!_Minimo_8_caratteri';
                        self::redirect($formPath, $msg);
                    }
                    break;
                case 'date':
                    if (
                        !date_create_from_format('Y-m-d', $_POST[$field->name]) &&
                        !empty($_POST[$field->name])
                    ) {
                        $msg .= 'La_' . strtolower(str_replace(" ", "_", $field->name))
                            . '_non_è_una_data_valida';
                        self::redirect($formPath, $msg);
                    }
                    break;

                case 'date':
                    if (!is_numeric($_POST[$field->name])) {
                        $msg .= 'Il_campo_'.strtolower(str_replace(" ", "_", $field->name))
                            . '_deve_essere_un_numero';
                        self::redirect($formPath, $msg);
                    }
                    break;

                case 'text':
                    $_POST[$field->name] = htmlentities($_POST[$field->name]);
                    break;
            }

            $fields[$field->name] = $_POST[$field->name];
        }

        return $fields;
    }

    protected static function checkDates($d1, $d2, $formPath, $formName, $errorMsg)
    {
        $msg = $formName . '_error=' . $errorMsg;

        if (
            strtotime($d1)
            <
            strtotime($d2)
        ) {
            self::redirect($formPath, $msg);
        }
    }
}
