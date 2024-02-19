<?php

transferValuesFromArrayToObject($enrollee, $_POST);

/**
 *@var ErrorList $errors содержит в себе ссылку на объект ErrorList, который в свою очередь хранит ошибки валидации
 */

$errors = $enrolleeValidator->validate($enrollee);

if (empty($errors->getErrors())) {
    $personalDataTDG->addStudent($enrollee);
    $authorizationToken = generateToken(32);
    $authorizationTokensTDG->addToken($authorizationToken);
    setcookie('authorizationToken', $authorizationToken, array(
        'expires' => time() + 60*60*24*365*10,
        'domain' => 'localhost',
        'httponly' => true,
        'samesite' => 'Strict'
    ));
    header('Location: http://localhost/home');
    die;
}