<?php

namespace App\Route;

use App\Utility\SetterGetter\SetterGetter;
use App\Utility\AttributeClasses\Getter;

class Route
{
    use SetterGetter;

    #[Getter]
    private string $url;
    #[Getter]
    private string $controllerName;

    public function __construct(string $url, string $controllerName)
    {
        $this->url = $url;
        $this->controllerName = $controllerName;
    }
}