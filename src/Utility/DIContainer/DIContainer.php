<?php

namespace App\Utility\DIContainer;

/*Описывает интерфейс контейнера, который предоставляет методы для чтения его записей. */
use Psr\Container\ContainerInterface;

class DIContainer implements ContainerInterface
{

    /**
     * @var array<string, callable> Название сервиса - колбэк создания объекта этого сервиса
     */

    private array $registered = [];

    /**
     * @var array<string, object> Название сервиса - экземпляр этого сервиса
     */

    private array $created = [];

    public function register(string $id, callable $factory): void
    {
        if ($this->has($id)) {
            throw new AttemptingToRegisterServiceUsingBusyName('Сервис с таким именем уже зарегистрирован.');
        }

        $this->registered[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->created)) {
            return $this->created[$id];
        } elseif ($this->has($id)) {
            $object = call_user_func($this->registered[$id], $this);
            $this->created[$id] = $object;
            return $object;
        } else {
            throw new AttemptToObtainOrCreateUnregisteredService('Сервис с таким именем не зарегистрирован.');
        }
    }

    public function has(string $id): bool
    {
        return isset($this->registered[$id]);
    }
}