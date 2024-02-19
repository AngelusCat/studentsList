<?php

namespace App\Enrollee;

use App\Utility\SetterGetter\SetterGetter;
use App\Utility\AttributeClasses\Getter;
use App\Utility\AttributeClasses\Setter;

class Enrollee
{

    use SetterGetter;

    #[Setter]
    #[Getter]
    private string $name;

    #[Setter]
    #[Getter]
    private string $surname;

    #[Setter]
    #[Getter]
    private string $gender;

    #[Setter]
    #[Getter]
    private string $groupNumber;

    #[Setter]
    #[Getter]
    private string $email;

    #[Setter]
    #[Getter]
    private int $totalPointsUSE;

    #[Setter]
    #[Getter]
    private int $yearOfBirth;

    #[Setter]
    #[Getter]
    private string $location;
}