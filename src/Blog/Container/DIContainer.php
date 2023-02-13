<?php

namespace Veliafar\PhpBlog\Blog\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Veliafar\PhpBlog\Blog\Exceptions\NotFoundException;

class DIContainer implements ContainerInterface
{

  private array $resolvers = [];

  public function __construct(private ContainerStorage $storage)
  {
  }


  public function bind(string $type, string|object $resolver): void
  {
    $this->resolvers[$type] = $resolver;
  }

  public function has(string $id): bool
  {
    try {
      $this->get($id);
    } catch (NotFoundException) {
      return false;
    }
    return true;
  }

  /**
   * @throws NotFoundException
   */
  public function get(string $id): mixed
  {
    if (array_key_exists($id, $this->resolvers)) {
      $typeToCreate = $this->resolvers[$id];
      if (is_object($typeToCreate)) {
        return $typeToCreate;
      }
      return $this->get($typeToCreate);
    }
    if (!class_exists($id)) {
      throw new NotFoundException("Cannot resolve type: $id");
    }

    $reflectionClass = new ReflectionClass($id);
    $constructor = $reflectionClass->getConstructor();


    $className = $reflectionClass->getName();
    if ($constructor === null) {
      return $this->storage->getFromStorage($className, $id);
    }

    $parameters = [];

    foreach ($constructor->getParameters() as $parameter) {
      $parameterType = $parameter->getType()->getName();
      $parameters[] = $this->get($parameterType);
    }

    return $this->storage->getFromStorage($className, $id, $parameters);
  }


}