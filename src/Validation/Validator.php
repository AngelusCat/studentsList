<?php

namespace App\Validation;

use App\Enrollee\Enrollee;

use App\Validation\RuleClasses\NotBlankValidator;
use App\Validation\RuleClasses\MinLengthValidator;
use App\Validation\RuleClasses\MaxLengthValidator;
use App\Validation\RuleClasses\ValidSymbolValidator;
use App\Validation\RuleClasses\NumberInRange;
use App\Validation\RuleClasses\MatchesRegexp;
use App\Validation\RuleClasses\EmailIsUnique;

class Validator
{

    /**
     *@var array<string, list<object>> название поля - список правил валидации, которые применяются к полю
     */

    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(Enrollee $enrollee): ErrorList
    {
        $errorList = new ErrorList();

        foreach ($this->rules as $fieldName => $checklist) {
            foreach ($checklist as $rule) {
                $methodName = getMethodNameFromFieldName('get', $fieldName);
                $result = $rule($enrollee->$methodName());
                
                if ($result) {
                    $errorList->add($fieldName, $result);
                }
            }
        }

        return $errorList;
    }
}