<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;

class NumberInRange implements ValidatorRuleInterface
{
    private string $format;
    
    private int $min;
    
    private int $max;

    private string $fieldName;
    
    public function __construct(int $min, int $max, string $fieldName)
    {
        $this->min = $min;
        $this->max = $max;
        $this->fieldName = $fieldName;
        $this->format = '%s не может быть больше ' . $this->max . " и меньше " . $this->min . ".";
    }
    
    public function __invoke(mixed $value): ?ValidationError
    {
        if (!is_int($value)) {
            throw new TypeException("Неправильный тип данных в аргументе функции.");
        }
        
        $resultMin = $value < $this->min;
        $resultMax = $value > $this->max;
        
        if ($resultMin || $resultMax) {
            $errorText = sprintf($this->format, $this->fieldName);
            return new ValidationError($errorText);
        } else {
            return null;
        }
    }
}