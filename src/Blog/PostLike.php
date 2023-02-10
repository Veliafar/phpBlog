<?php

namespace Veliafar\PhpBlog\Blog;

class PostLike
{
  public function __construct(private UUID $uuid, private Post $post,  private User $user)
  {
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function getPost(): Post
  {
    return $this->post;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function __toString(): string
  {
    return "like с ID $this->uuid" . "пользователя: $this->user" . PHP_EOL . "для поста: $this->post";
  }
}