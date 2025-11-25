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
    protected function checkFieldLengths(array $fields, array $data): array {
        $errors = [];
        foreach (array_keys($fields) as $field) {
            if (!array_key_exists($field, array: $data)) {
                continue;
            }

            if (strlen($data[$field]) < $fields[$field][CorrectLengthFormat::$MINIMUM]) {
                $errors[$field] = ErrorCodes::$FieldLengthTooSmall;
                continue;
            }

            if (strlen($data[$field]) > $fields[$field][CorrectLengthFormat::$MAXIMUM]) {
                $errors[$field] = ErrorCodes::$FieldLengthTooBig;
                continue;
            }
        }

        return $errors;
    }
}