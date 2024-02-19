<?php

use App\Enrollee\Enrollee;

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
 * @var array<string, int|string> содержит название GET-параметра и его значение
 */

$getParameters = [];

if ($_SERVER['REQUEST_URI'] === '/home' || $_SERVER['REQUEST_URI'] === '/') {
    
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            setcookie($key, $value, array(
                'expires' => time() + 3600,
                'domain' => 'localhost',
                'httponly' => true,
                'samesite' => 'Strict'
            ));
            $_COOKIE[$key] = $value;
        }
    }

    $getParameters ['page'] = $_COOKIE['page'] ?? 1;
    $getParameters ['sort'] = $_COOKIE['sort'] ?? 'pointsUp';
    
    header('Location: /home?page=' . $getParameters['page'] . '&sort=' . $getParameters['sort'] . '');
    die;
}

/**
 * @var int $totalNumberOfRecords содержит общее количество записей таблицы personalData в БД applicants
 */

$totalNumberOfRecords = $personalDataTDG->countNumberOfAllRecords();

/**
 * @var int $totalNumberOfPages содержит общее количество страниц, которые будут печаться на Главной странице
 */

$totalNumberOfPages = $pager->getTotalNumberOfPages($totalNumberOfRecords, 50);

if ($totalNumberOfPages > 1) {

    /**
     * @var array<int, Page> содержит список объектов класса Page, каждый из которых содержит информаneцию об одной странице
     */

    $pages = $pager->getPages($totalNumberOfPages, 50, $totalNumberOfRecords);

    foreach ($pages as $page) {
        if ($page->getPageNumber() === (int) $_GET['page']) {
            $range = $page->getRecordRange();
            break;
        }
    }
} else {
    $range = [
        0 => $personalDataTDG->getMinimumId(),
        1 => $personalDataTDG->getMaximumId()
    ];
}

$applicants = $personalDataTDG->getSortedRecordsByIdRange($range, ['name', 'surname', 'groupNumber', 'totalPointsUSE'] , $_GET['sort']);

$matriculants = [];

foreach($applicants as $applicant) {
    $matriculants [] = new Enrollee();
    transferValuesFromArrayToObject($matriculants[array_key_last($matriculants)], $applicant);
}

/**
 * @var string $textForFormByUserStatus содержит надпись, значение которой зависит от того, авторизован пользователь или нет
 */

$textForFormByUserStatus = (array_key_exists('authorizationToken', $_COOKIE)) ? 'Редактировать свои данные' : 'Добавить свои данные';

require_once(TEMPLATES . 'homePage.html');