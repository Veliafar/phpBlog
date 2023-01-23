<?php

namespace Veliafar\PhpBlog\User;

class User
{
  public function __construct(private string $id, private string $firstName, private string $lastName)
  {
    $this->id = $id;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @return string
   */
  public function __toString(): string
  {
    return $this->lastName . ' ' . $this->firstName;
  }
}