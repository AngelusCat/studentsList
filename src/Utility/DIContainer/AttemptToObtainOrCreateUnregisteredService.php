<?php

namespace App\Utility\DIContainer;

use \Exception;
use Psr\Container\NotFoundExceptionInterface;

class AttemptToObtainOrCreateUnregisteredService extends Exception implements NotFoundExceptionInterface
{

}