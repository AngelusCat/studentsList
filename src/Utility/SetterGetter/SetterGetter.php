<?php

namespace App\Utility\SetterGetter;

use \ReflectionProperty;
use App\Utility\AttributeClasses\Setter;
use App\Utility\AttributeClasses\Getter;

trait SetterGetter
{

    /**
     *@var array <string, callable> имя метода (имена геттеров и сеттеров) - колбэк
     */

    private static array $listOfGettersAndSetters = [];

    private function getFieldNameFromMethodName(string $methodName): string
    {
        $fieldName = mb_substr($methodName, 3);
        $firstChar = mb_strtolower(mb_substr($fieldName, 0, 1));
        $lastChars = mb_substr($fieldName, 1);
        $fieldName = $firstChar . $lastChars;
        return $fieldName;
    }

    private function getPropertyAccessMethods(string $methodName, object $object, string $fieldName): array
    {
        $methods = [];
        $reflAttributes = new ReflectionProperty(get_class($object), $fieldName);
        $properties = $reflAttributes->getAttributes();
        foreach ($properties as $property) {
            $methodName = $property->getName();
            if ($methodName === Setter::class) {
                $methods[] = 'set';
            } elseif ($methodName === Getter::class) {
                $methods[] = 'get';
            }
        }
        return $methods;
    }

    public function __call(string $name, array $arguments)
    {

        $prefix = mb_substr($name, 0, 3);

        $fieldName = $this->getFieldNameFromMethodName($name);

        $fieldValue = $arguments[0] ?? null;

        if (isset(self::$listOfGettersAndSetters[$name]) && $prefix === 'get') {
            return self::$listOfGettersAndSetters[$name]($this, $fieldName);
        } elseif (isset(self::$listOfGettersAndSetters[$name]) && $prefix === 'set') {
            return self::$listOfGettersAndSetters[$name]($this, $fieldValue);
        } else {
        
        if ($prefix !== 'get' && $prefix !== 'set') {
            throw new CallingInaccessibleUndeclaredMethod('Из необъявленных методов этого класса можно вызывать только геттер и сеттер.');
        }
        
        if (!property_exists(get_class($this), $fieldName)) {
            throw new AccessingNonExistentProperty('В классе ' . get_class($this) . 'не существует свойства с именем' . $fieldName);
        }

        $methods = $this->getPropertyAccessMethods($name, $this, $fieldName);

        if (!in_array($prefix, $methods)) {
            throw new CallingInaccessibleUndeclaredMethod('К этому полю нельзя обращаться через метод ' . $prefix);
        }
        
        if ($prefix === 'get') {
            if (count($arguments) !== 0) {
                throw new PassingInvalidNumberOfArguments('В геттер нельзя передавать аргументы.');
            }
            self::$listOfGettersAndSetters[$name] = fn (object $object, string $fieldName): mixed => (!isset($object->$fieldName)) ? null : $object->$fieldName;
            return self::$listOfGettersAndSetters[$name]($this, $fieldName);
        } elseif ($prefix === 'set') {
            if (count($arguments) !== 1) {
                throw new PassingInvalidNumberOfArguments('В сеттер можно передавать только один аргумент.');
            }
            self::$listOfGettersAndSetters[$name] = fn(object $object, mixed $fieldValue) => $object->$fieldName = $fieldValue;
            self::$listOfGettersAndSetters[$name]($this, $fieldValue);
        }
    }
    }
}