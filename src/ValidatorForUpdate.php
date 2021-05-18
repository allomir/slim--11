<?php

namespace App;

class ValidatorForUpdate implements ValidatorInterface
{
    public function validate(array $fields): array
    {
        $errors = [];
        $requiredFields = ["name", "city"];
        foreach($requiredFields as $required) {
            $value = $fields[$required] ?? '';

            if (!trim($value)) {
                $errors[$required] = "Can't be blank";
            }
        }

        return $errors;
    }
}
