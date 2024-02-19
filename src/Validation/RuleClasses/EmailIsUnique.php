<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;
use App\Enrollee\PersonalDataTDG;

class EmailIsUnique implements ValidatorRuleInterface
{
    private PersonalDataTDG $pdTDG;

    private const FORMAT = '%s уже используется другим пользователем. Пожалуйста, введите другой емайл.';

    public function __construct(PersonalDataTDG $pdTDG)
    {
        $this->pdTDG = $pdTDG;
    }

    public function __invoke(mixed $value): ?ValidationError
    {
        $quantity = $this->pdTDG->getNumberOfRecordsThatContainThisEmail($value);

        if (!$quantity) {
            return null;
        } elseif ($quantity) {
            return new ValidationError(sprintf(
                self::FORMAT,
                $value
            ));
        }
    }
}