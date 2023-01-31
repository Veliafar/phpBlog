<?php

namespace Veliafar\PhpBlog\Blog;

class Post
{
  public function __construct(private UUID $uuid, private User $user, private string $title, private string $text)
  {
  }


  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getUserUUID(): UUID {
    return $this->user->uuid();
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function getText(): string
  {
    return $this->text;
  }


  public function setUser(User $user): Post
  {
    $this->user = $user;
    return $this;
  }


  public function setTitle(string $title): Post
  {
    $this->title = $title;
    return $this;
  }

  public function setText(string $text): Post
  {
    $this->text = $text;
    return $this;
  }



  public function __toString(): string
  {
    return PHP_EOL . $this->user . ' пишет: ' . PHP_EOL  . $this->title . PHP_EOL . $this->text;
  }
}