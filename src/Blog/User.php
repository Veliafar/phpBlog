<?php

namespace Veliafar\PhpBlog\Blog;

class User
{
  public function __construct(private string $id, private Name $name, private string $login)
  {
  }

  public function id(): string
  {
    return $this->id;
  }

  public function login(): string {
    return $this->login;
  }

  public function name(): string {
    return $this->name;
  }


  public function __toString(): string
  {
    return "Пользователь $this->id с именем  $this->name и логином $this->login";
  }
}