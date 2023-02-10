<?php

namespace Veliafar\PhpBlog\Blog\Like;

use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class PostLike extends Like
{
  private Post $post;
  public function __construct(private UUID $uuid, Post $post,  private User $user)
  {
    parent::__construct($uuid, $user);
    $this->post = $post;
  }

  public function getPost(): Post
  {
    return $this->post;
  }

  public function __toString(): string
  {
    return "like с ID $this->uuid" . "пользователя: $this->user" . PHP_EOL . "для поста: $this->post";
  }
}