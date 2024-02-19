<?php

use App\Enrollee\Enrollee;
use App\Utility\OtherExceptionClasses\XSRFTokenFromPOSTAndCookieAreNotEqual;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_COOKIE['token'] === '' || $_POST['token'] === '' || $_COOKIE['token'] !== $_POST['token']) {
        throw new XSRFTokenFromPOSTAndCookieAreNotEqual();
    }
    
    /**
     * @var int|string $searchQuery содержит цифру или слово (часть слова), которое нужно искать в таблице БД
     */

    $searchQuery = trim($_POST['search'] ?? '');
}

/**
 * @var array<int, list<string, string|int>>
 */

$searchResults = $personalDataTDG->getSearchResults($searchQuery, ['name', 'surname', 'groupNumber', 'totalPointsUSE'], ['name', 'surname', 'groupNumber']);

$matriculants = [];

if ($searchResults) {
    foreach ($searchResults as $searchResult) {
        $matriculants [] = new Enrollee();
        transferValuesFromArrayToObject($matriculants[array_key_last($matriculants)], $searchResult);
    }
} else {
    $matriculants [] = 'Ничего не найдено';
}

require_once(TEMPLATES . 'search.html');