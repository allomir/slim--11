<?php

namespace App;

class ValidatorForSignin implements ValidatorInterface
{
    public function validate(array $fields): array
    {
        $errors = [];
        $requiredFields = ["email", "password"];
        foreach($requiredFields as $required) {
            $value = $fields[$required] ?? '';

            if (!trim($value)) {
                $errors[$required] = "Can't be blank";
            }
        }

        return $errors;
    }
}
