<?php

namespace App;

class Validator implements ValidatorInterface
{
    public function validate(array $course)
    {
        // BEGIN (write your solution here)
        $errors = [];
        $requiredFields = ["paid","title"];
        foreach($requiredFields as $required) {
            $value = $course[$required] ?? '';

            if (!trim($value)) {
                $errors[$required] = "Can't be blank";
            }
        }

        return $errors;
        // END
    }
}