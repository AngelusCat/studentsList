<?php

namespace App\Validation;

use App\Utility\SetterGetter\SetterGetter;
use App\Utility\AttributeClasses\Getter;

class ValidationError
{
    use SetterGetter;

    #[Getter]
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}