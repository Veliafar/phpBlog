<?php

namespace Veliafar\PhpBlog\Blog;

class Name
{
  public function __construct(private string $firstName, private string $lastName)
  {
  }

  public function firstName(): string
  {
    return $this->firstName;
  }

  public function lastName(): string
  {
    return $this->lastName;
  }

  public function __toString(): string
  {
    return "$this->lastName $this->firstName";
  }
}