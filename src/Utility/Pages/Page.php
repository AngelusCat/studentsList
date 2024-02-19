<?php

namespace App\Utility\Pages;

use App\Utility\AttributeClasses\Getter;
use App\Utility\SetterGetter\SetterGetter;

class Page
{

    use SetterGetter;

    #[Getter]
    private int $pageNumber;

    /**
     *@var array<int, int> диапазон id записей, которые должны печататься на этой странице
     */

    #[Getter]
    private array $recordRange;

    public function __construct(int $pageNumber, array $recordRange)
    {
        $this->pageNumber = $pageNumber;
        $this->recordRange = $recordRange;
    }
}