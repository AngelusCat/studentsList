<?php

/**
 * @var string $emailFromEnrollee содержит емайл только что извлеченной из БД записи по id
 */

$emailFromEnrollee = !empty($enrollee->getEmail()) ? $enrollee->getEmail() : '';

/**
 *@var array<int, string> ключи - числа от 0 до n, элементы - названия полей, в которых произошло изменение
 */

$listOfEditedFields = getListOfEditedFields($enrollee, $_POST);

transferValuesFromArrayToObject($enrollee, $_POST);

/**
 *@var ErrorList $errors содержит в себе ссылку на объект ErrorList, который в свою очередь хранит ошибки валидации
 */

$errors = ($emailFromEnrollee !== $_POST['email']) ? $enrolleeValidator->validate($enrollee) : $enrolleeValidatorWithouEmailIsUnique->validate($enrollee);

if (empty($errors->getErrors())) {
        
    foreach($listOfEditedFields as $fieldName) {
        $methodName = getMethodNameFromFieldName('get', $fieldName);

        $value = $enrollee->$methodName();

        $personalDataTDG->updateColumnValueById($fieldName, $value, $id);
    }

    header('Location: http://localhost/home');
    die;
}