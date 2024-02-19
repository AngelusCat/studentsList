<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;

//<predicate> ? <expression on true> : <expression on false>

/* Если количество символов = 0, то это ошибка
   Если количество символов 1 или больше 1, то это null
   Посчитать количество символов
   Если количество символов = 0, то выдать ошибку
   Если обнаружена ошибка:
   Создать объект класса ValidationError с текстом ошибки
   Вернуть этот объект
   Т.е. возвращается либо null (нет ошибки), либо объект класса ValidationError
 */

class NotBlankValidator implements ValidatorRuleInterface
{
    private const ERROR_TEXT = "Это поле обязательно для заполнения.";

    public function __invoke(mixed $value): ?ValidationError
    {
        $numberOfCharacters = iconv_strlen($value);

        return ($numberOfCharacters === 0) ? new ValidationError(self::ERROR_TEXT) : null;
    }

}