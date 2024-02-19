<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;

/*
    Если количество символов меньше, чем стандарт = ошибка
 */

class MinLengthValidator implements ValidatorRuleInterface
{
    private const FORMAT = 'Это поле содержит %d символов. Минимальное количество символов для этого поля - %u.';

    private int $standard;

    public function __construct(int $standard)
    {
        $this->standard = $standard;
    }

    public function __invoke(mixed $value): ?ValidationError
    {
        $numberOfCharacters = iconv_strlen($value);

        if ($numberOfCharacters < $this->standard) {
            return new ValidationError(sprintf(
                self::FORMAT, 
                $numberOfCharacters, 
                $this->standard));
        } else {
            return null;
        }
    }
}