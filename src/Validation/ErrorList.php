<?php

namespace App\Validation;

use App\Utility\AttributeClasses\Getter;
use App\Utility\SetterGetter\SetterGetter;

class ErrorList
{
    use SetterGetter;

    /**
     *@var array<string, list<ValidationError>> название поля - список ошибок валидации
     */

    #[Getter]
    private array $errors = [];

    public function add(string $fieldName, ValidationError $validationError): void
    {
        $this->errors[$fieldName][] = $validationError;
    }

    public function getListOfFieldErrors(string $fieldName): array
    {
        $result = $this->errors[$fieldName] ?? [];

        foreach ($result as $key => $error) {
            $result [$key] = $error->getText();
        }
        
        return $result;
    }

    public function isEmpty(): bool
    {
        return empty($this->errors);
    }
}