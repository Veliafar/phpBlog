<?php

namespace Veliafar\PhpBlog\Blog;

class CommentLike
{
  public function __construct(private UUID $uuid, private Comment $comment,  private User $user)
  {
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function getComment(): Comment
  {
    return $this->comment;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function __toString(): string
  {
    return "like с ID $this->uuid" . "пользователя: $this->user" . PHP_EOL . "для комментария: $this->comment";
  }
}