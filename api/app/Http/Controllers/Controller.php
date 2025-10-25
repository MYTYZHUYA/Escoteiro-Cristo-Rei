<?php
namespace App\Http\Controllers;

require_once __DIR__ . "/BaseController.php";

class CorrectLengthFormat {
    static public int $MINIMUM = 0;
    static public int $MAXIMUM = 1;
}

class ErrorCodes {
    static public string $FieldLengthTooSmall = "FieldLengthTooSmall";
    static public string $FieldLengthTooBig = "FieldLengthTooBig";
}

class Controller extends BaseController {
    protected function checkFieldLengths(array $fields, array $correct_length, array $data): array {
        $errors = [];
        for ($idx = 0; $idx < sizeof($fields); $idx++) {
            if (strlen($data[$fields[$idx]]) < $correct_length[$idx][CorrectLengthFormat::$MINIMUM]) {
                $errors[$fields[$idx]] = ErrorCodes::$FieldLengthTooSmall;
                continue;
            }

            if (strlen($data[$fields[$idx]]) > $correct_length[$idx][CorrectLengthFormat::$MAXIMUM]) {
                $errors[$fields[$idx]] = ErrorCodes::$FieldLengthTooBig;
                continue;
            }
        }

        return $errors;
    }
}