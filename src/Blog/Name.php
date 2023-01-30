<?php

namespace Veliafar\PhpBlog\Blog;

class Name
{
  public function __construct(private string $firstName, private string $lastName)
  {
  }

  public function first(): string
  {
    return $this->firstName;
  }

  public function last(): string
  {
    return $this->lastName;
  }

  public function setFirst(string $firstName): Name
  {
    $this->firstName = $firstName;
    return $this;
  }

  public function setLast(string $lastName): Name
  {
    $this->lastName = $lastName;
    return $this;
  }


  public function __toString(): string
  {
    return "$this->lastName $this->firstName";
  }
}