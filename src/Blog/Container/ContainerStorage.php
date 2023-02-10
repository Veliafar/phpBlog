<?php

namespace Veliafar\PhpBlog\Blog\Container;

class ContainerStorage
{
  private array $storage = [];


  public function getFromStorage(string $className, mixed $id, mixed $parameters = null): mixed
  {
    if (!array_key_exists($className, $this->storage)) {
      $this->storage[$className] = $parameters ? new $id(...$parameters) : new $id();
    }

    return $this->storage[$className];
  }

}