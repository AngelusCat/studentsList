<?php

namespace App\Utility\DIContainer;

use \Exception;
use Psr\Container\ContainerExceptionInterface;

class AttemptingToRegisterServiceUsingBusyName extends Exception implements ContainerExceptionInterface
{
    
}