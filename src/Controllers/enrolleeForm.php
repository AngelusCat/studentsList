<?php

use App\Enrollee\Enrollee;
use App\Utility\OtherExceptionClasses\XSRFTokenFromPOSTAndCookieAreNotEqual;

/*Никогда не проверяй с помощью isset и empty пустое свойство или нет, если свойство приватное
(empty($test->number)) = $test->number равен 5, но условие считает, что оно пустое
(isset($test->number)) = $test->number равен 5, но условие считает, что оно пустое
(isset($test->number)) = $test->number ничего не равен, условие считает, что оно пустое
(isset($test->getNumber())) = выдает ошибку
(empty($test->getNumber())) = $test->number равен 5, условие считает, что оно не пустое, т.е. истина
Значит проверять, установлено значение у приватной переменной или нет, стоит через empty и только через геттер
*/

if (!array_key_exists('token', $_COOKIE)) {

    $token = generateToken(32);

    setcookie('token', $token, array(
        'expires' => time() + 3600,
        'domain' => 'localhost',
        'httponly' => true,
        'samesite' => 'Strict'
    ));
} else {
    $token = $_COOKIE['token'];

    setcookie('token', $token, array(
        'expires' => time() + 3600,
        'domain' => 'localhost',
        'httponly' => true,
        'samesite' => 'Strict'
    ));
}

/**
 *@var bool $authorization хранит значение true (пользователь авторизован) и false (пользователь не авторизован)
 */

$authorization = isset($_COOKIE['authorizationToken']);

/**
 *@var Enrollee будет хранить данные, переданные с массивом $_POST
 */

$enrollee;

/**
 *@var ErrorList будет хранить ошибки валидации
 */

$errors;

if ($authorization) {
    $id = $authorizationTokensTDG->getIdByToken($_COOKIE['authorizationToken']);
    $enrolleeArray = $personalDataTDG->getStudentById($id);
    $enrollee = new Enrollee();
    transferValuesFromArrayToObject($enrollee, $enrolleeArray);
} else {
    $enrollee = new Enrollee();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_COOKIE['token'] === '' || $_POST['token'] === '' || $_COOKIE['token'] !== $_POST['token']) {
        throw new XSRFTokenFromPOSTAndCookieAreNotEqual();
    }

    if (!$authorization) {
        require_once('C:\localhost\src\Controllers\registration.php');
    } else {
        require_once('C:\localhost\src\Controllers\editing.php');
    }
}

/**
 * @var string $textForFormByUserStatus содержит надпись, значение которой зависит от того, авторизован пользователь или нет
 */

$textForFormByUserStatus = (isset($_COOKIE['authorizationToken'])) ? 'редактирования данных' : 'регистрации';

require_once(TEMPLATES . 'enrolleeForm.html');