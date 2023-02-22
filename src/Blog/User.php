<?php

namespace Veliafar\PhpBlog\Blog;

use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;

class User
{
  public function __construct(
    private UUID   $uuid,
    private Name   $name,
    private string $username,
    private string $password
  )
  {
  }

  private static function hash(string $password, UUID $uuid): string
  {
    return hash('sha256',  $password . $uuid);
  }

  public function checkPassword(string $password, UUID $uuid): bool
  {
    return $this->password === self::hash($password, $uuid);
  }

  /**
   * @throws InvalidArgumentException
   */
  public static function createFrom(
    Name $name,
    string $username,
    string $password,
  ): self
  {
    $uuid = UUID::random();
    return new self(
      $uuid,
      $name,
      $username,
      self::hash($password, $uuid),
    );
  }

  public function password(): string
  {
    return $this->password;
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function username(): string
  {
    return $this->username;
  }

  public function name(): Name
  {
    return $this->name;
  }


  public function setName(Name $name): User
  {
    $this->name = $name;
    return $this;
  }

  public function setLogin(string $username): User
  {
    $this->username = $username;
    return $this;
  }


  public function __toString(): string
  {
    return "Пользователь $this->uuid с именем  $this->name и логином $this->username";
  }
}