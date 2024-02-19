<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;

class MatchesRegexp implements ValidatorRuleInterface
{

    private string $regexp;

    private string $errorText;

    public function __construct(string $regexp, string $errorText)
    {
        $this->regexp = $regexp;
        $this->errorText = $errorText;
    }

    public function __invoke(mixed $value): ?ValidationError
    {
        $result = preg_match($this->regexp, $value);

        if (!$result) {
            return new ValidationError($this->errorText);
        } else {
            return null;
        }
    }
}