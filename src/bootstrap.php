<?php

use App\Utility\DIContainer\DIContainer;
use App\Enrollee\PersonalDataTDG;
use App\Enrollee\AuthorizationTokensTDG;
use App\Validation\Validator;
use App\Validation\RuleClasses\NotBlankValidator;
use App\Validation\RuleClasses\MinLengthValidator;
use App\Validation\RuleClasses\MaxLengthValidator;
use App\Validation\RuleClasses\ValidSymbolValidator;
use App\Validation\RuleClasses\NumberInRange;
use App\Validation\RuleClasses\MatchesRegexp;
use App\Validation\RuleClasses\EmailIsUnique;
use App\Utility\Pages\Pager;
use App\Utility\ViewHelper;

$container = new DIContainer();

$container->register('config', function(DIContainer $container) {
    $configPath = 'conf.ini';
    $config = parse_ini_file($configPath);
    return $config;
});

$container->register('PDO', function(DIContainer $container) {
    $config = $container->get('config');
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    return $pdo;
});

$container->register('PersonalDataTDG', function(DIContainer $container) {
    return new PersonalDataTDG($container->get('PDO'));
});

$container->register('AuthorizationTokensTDG', function(DIContainer $container) {
    return new AuthorizationTokensTDG($container->get('PDO'));
});

$container->register('EmailIsUnique', function(DIContainer $container) {
    return new EmailIsUnique($container->get('PersonalDataTDG'));
});

$container->register('Pager', function(DIContainer $container) {
    return new Pager();
});

$container->register('enrolleeValidator', function(DIContainer $container) {
    return new Validator([
        'name' => [
            new NotBlankValidator(),
            new MinLengthValidator(1),
            new MaxLengthValidator(20),
            new ValidSymbolValidator("/[а-яёА-ЯЁ'\-]/u", "заглавные и строчные буквы русского алфавита, дефисы, апостроф"),
            new MatchesRegexp("/^[^']*'{0,}[^']*$/ui", "Апостроф можно использовать только один раз."),
            new MatchesRegexp("/^[^']{1}'?[^']*$/ui", "Апостроф должен быть 2-ым символом в строке."),
        ],
        'surname' => [
            new NotBlankValidator(),
            new MinLengthValidator(1),
            new MaxLengthValidator(45),
            new ValidSymbolValidator("/[а-яёА-ЯЁ\(\)'\- ]/u", "заглавные и строчные буквы русского алфавита, дефисы, апостроф, пробелы, скобки"),
            new MatchesRegexp("/^[^']*'{0,}[^']*$/ui", "Апостроф можно использовать только один раз."),
            new MatchesRegexp("/^[^']{1}'?[^']*$/ui", "Апостроф должен быть 2-ым символом в строке."),
            new MatchesRegexp("/^[^\(\)]*\(?[^\(\)]*\)?$/ui", "Скобки можно использовать только один раз."),
            new MatchesRegexp("/^[^\(\)]*\(?[^\(\)]*\)?$/ui", "Открывающая скобка должна находиться в строке раньше, чем закрывающая скобка."),
            new MatchesRegexp("/^[^\(\)]+$|^[^\(\)]+\({1}[^\(\)]+\){1}$/ui", "Если строка содержит скобки, то должны присутствовать обе."),
        ],
        'groupNumber' => [
            new NotBlankValidator(),
            new MinLengthValidator(2),
            new MaxLengthValidator(5),
            new ValidSymbolValidator("/[а-яёА-ЯЁ0-9]/u", "заглавные и строчные буквы русского алфавита, цифры от 0 до 9"),
        ],
        'email' => [
            new NotBlankValidator(),
            new MinLengthValidator(4),
            new MaxLengthValidator(50),
            $container->get('EmailIsUnique'),
            new ValidSymbolValidator("/[a-zA-Z0-9_\-\.@]/", "заглавные и строчные буквы английского алфавита, цифры от 0 до 9, нижнее подчеркивание, дефис, точку, @"),
            new MatchesRegexp("/^[a-zA-Z0-9]/", "Адрес электронной почты должен начинаться с заглавной или строчной латинской буквы или цифры от 0 до 9."),
            new MatchesRegexp("/^.+@/", "Адрес электронной почты должен содержать уникальное имя почты (набор символов перед @ (разрешаются заглавные и строчные латинские буквы, цифры от 0 до 9; точка, нижнее подчеркивание, дефис, но только не первым символом))."),
            new MatchesRegexp("/@{1}[a-z]+\.{1}[a-z]+$/", "Адрес электронной почты должке содержать доменное имя (набор символов после @ (допускаются доменная имена, состоящие из строчных латинских букв))."),
            new MatchesRegexp("/@{1}/", "Адрес электронной почты должен содержать символ '@', причем в единичном количестве.")
        ],
        'totalPointsUSE' => [
            new NotBlankValidator(),
            new MinLengthValidator(3),
            new MaxLengthValidator(3),
            new NumberInRange(144, 300, "Сумма баллов ЕГЭ"),
            new ValidSymbolValidator("/[0-9]/", "цифры от 0 до 9"),
        ],
        'yearOfBirth' => [
            new NotBlankValidator(),
            new MinLengthValidator(4),
            new MaxLengthValidator(4),
            new NumberInRange(1892, 2019, "Год рождения"),
            new ValidSymbolValidator("/[0-9]/", "цифры от 0 до 9"),
]]);
});

$container->register('enrolleeValidatorWithouEmailIsUnique', function(DIContainer $container) {
    return new Validator([
        'name' => [
            new NotBlankValidator(),
            new MinLengthValidator(1),
            new MaxLengthValidator(20),
            new ValidSymbolValidator("/[а-яёА-ЯЁ'\-]/u", "заглавные и строчные буквы русского алфавита, дефисы, апостроф"),
            new MatchesRegexp("/^[^']*'{0,}[^']*$/ui", "Апостроф можно использовать только один раз."),
            new MatchesRegexp("/^[^']{1}'?[^']*$/ui", "Апостроф должен быть 2-ым символом в строке."),
        ],
        'surname' => [
            new NotBlankValidator(),
            new MinLengthValidator(1),
            new MaxLengthValidator(45),
            new ValidSymbolValidator("/[а-яёА-ЯЁ\(\)'\- ]/u", "заглавные и строчные буквы русского алфавита, дефисы, апостроф, пробелы, скобки"),
            new MatchesRegexp("/^[^']*'{0,}[^']*$/ui", "Апостроф можно использовать только один раз."),
            new MatchesRegexp("/^[^']{1}'?[^']*$/ui", "Апостроф должен быть 2-ым символом в строке."),
            new MatchesRegexp("/^[^\(\)]*\(?[^\(\)]*\)?$/ui", "Скобки можно использовать только один раз."),
            new MatchesRegexp("/^[^\(\)]*\(?[^\(\)]*\)?$/ui", "Открывающая скобка должна находиться в строке раньше, чем закрывающая скобка."),
            new MatchesRegexp("/^[^\(\)]+$|^[^\(\)]+\({1}[^\(\)]+\){1}$/ui", "Если строка содержит скобки, то должны присутствовать обе."),
        ],
        'groupNumber' => [
            new NotBlankValidator(),
            new MinLengthValidator(2),
            new MaxLengthValidator(5),
            new ValidSymbolValidator("/[а-яёА-ЯЁ0-9]/u", "заглавные и строчные буквы русского алфавита, цифры от 0 до 9"),
        ],
        'email' => [
            new NotBlankValidator(),
            new MinLengthValidator(4),
            new MaxLengthValidator(50),
            new ValidSymbolValidator("/[a-zA-Z0-9_\-\.@]/", "заглавные и строчные буквы английского алфавита, цифры от 0 до 9, нижнее подчеркивание, дефис, точку, @"),
            new MatchesRegexp("/^[a-zA-Z0-9]/", "Адрес электронной почты должен начинаться с заглавной или строчной латинской буквы или цифры от 0 до 9."),
            new MatchesRegexp("/^.+@/", "Адрес электронной почты должен содержать уникальное имя почты (набор символов перед @ (разрешаются заглавные и строчные латинские буквы, цифры от 0 до 9; точка, нижнее подчеркивание, дефис, но только не первым символом))."),
            new MatchesRegexp("/@{1}[a-z]+\.{1}[a-z]+$/", "Адрес электронной почты должке содержать доменное имя (набор символов после @ (допускаются доменная имена, состоящие из строчных латинских букв))."),
            new MatchesRegexp("/@{1}/", "Адрес электронной почты должен содержать символ '@', причем в единичном количестве.")
        ],
        'totalPointsUSE' => [
            new NotBlankValidator(),
            new MinLengthValidator(2),
            new MaxLengthValidator(3),
            new NumberInRange(144, 300, "Сумма баллов ЕГЭ"),
            new ValidSymbolValidator("/[0-9]/", "цифры от 0 до 9"),
        ],
        'yearOfBirth' => [
            new NotBlankValidator(),
            new MinLengthValidator(4),
            new MaxLengthValidator(4),
            new NumberInRange(1892, 2019, "Год рождения"),
            new ValidSymbolValidator("/[0-9]/", "цифры от 0 до 9"),
]]);
});

$container->register('ViewHelper', function(DIContainer $container) {
    return new ViewHelper();
});

$enrolleeValidator = $container->get('enrolleeValidator');
$enrolleeValidatorWithouEmailIsUnique = $container->get('enrolleeValidatorWithouEmailIsUnique');
$personalDataTDG = $container->get('PersonalDataTDG');
$authorizationTokensTDG = $container->get('AuthorizationTokensTDG');
$pager = $container->get('Pager');
$viewHelper = $container->get('ViewHelper');