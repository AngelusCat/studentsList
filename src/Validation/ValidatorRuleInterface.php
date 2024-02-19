<?php

namespace App\Validation;

interface ValidatorRuleInterface
{
    public function __invoke(mixed $value): ?ValidationError;
}