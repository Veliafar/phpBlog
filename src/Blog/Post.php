<?php

namespace Veliafar\PhpBlog\Blog;

class Post
{
  public function __construct(private string $id, private User $user, private string $header, private string $text)
  {
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getHeader(): string
  {
    return $this->header;
  }

  public function getText(): string
  {
    return $this->text;
  }

  public function __toString(): string
  {
    return PHP_EOL . $this->user . ' пишет: ' . PHP_EOL  . $this->header . PHP_EOL . $this->text;
  }
}