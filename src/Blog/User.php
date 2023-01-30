<?php

namespace Veliafar\PhpBlog\Blog;

class User
{
  public function __construct(private UUID $uuid, private Name $name, private string $username)
  {
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