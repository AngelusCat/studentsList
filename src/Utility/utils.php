<?php

function getObjectFieldType(object $object): array
{
    $className = get_class($object);

    $class = new ReflectionClass($className);

    $properties = $class->getProperties();
    
    foreach ($properties as $property) {
        $name = $property->getName();
        $type = (string)$property->getType();
        $result[$name] = $type;
    }

    return $result;
}

function transferValuesFromArrayToObject(object $object, array $array): void
{
    $className = get_class($object);
    $standard = getObjectFieldType($object);

    foreach ($array as $key => $value) {
        $fieldName = $key;

        if (!property_exists($className, $fieldName)) {
            continue;
        }

        $fieldValue = $value;

        $fieldValue = trim($fieldValue);

        $type = $standard[$fieldName];

        if ($type === 'string') {
            $fieldValue = strval($fieldValue);
        } elseif ($type === 'int') {
            $fieldValue = intval($fieldValue);
        }

        $methodName = getMethodNameFromFieldName('set', $fieldName);

        $object->$methodName($fieldValue);
    }
}

function getMethodNameFromFieldName(string $prefix, string $fieldName): string
{
    $fieldNameFirstChar = mb_strtoupper(mb_substr($fieldName, 0, 1));
    $fieldNameLastChars = mb_substr($fieldName, 1);
    $methodName = $prefix . $fieldNameFirstChar . $fieldNameLastChars;
    return $methodName;
}

function generateToken(int $characters): string
{
    $token = random_bytes($characters);
    $token = bin2hex($token);
    return $token;
}

function getListOfEditedFields(object $enrollee, array $sentValues): array
{
    $result = [];

    foreach ($sentValues as $key => $value) {
        
        if (!property_exists($enrollee, $key)) {
            continue;
        }

        $methodName = getMethodNameFromFieldName('get', $key);

        $valueFromEnrollee = $enrollee->$methodName();

        if ($value !== $valueFromEnrollee) {
            $result [] = $key;
        } else {
            continue;
        }
    }

    return $result;
}