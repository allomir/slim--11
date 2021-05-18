<?php

namespace App;

class Validator implements ValidatorInterface
{
    public function validate(array $fields): array
    {
        $errors = [];
        $requiredFields = ["name","email","password","passwordConfirmation","city"];
        foreach($requiredFields as $required) {
            $value = $fields[$required] ?? '';

            if (!trim($value)) {
                $errors[$required] = "Can't be blank";
            }
        }

        return $errors;
    }
}
