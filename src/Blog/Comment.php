<?php

namespace Veliafar\PhpBlog\Blog;

class Comment
{
  public function __construct(private UUID $uuid, private Post $post, private User $user, private string $text)
  {
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @return Post
   */
  public function getPost(): Post
  {
    return $this->post;
  }

  public function getPostUUID(): UUID {
    return $this->post->uuid();
  }

  /**
   * @return User
   */
  public function getUser(): User
  {
    return $this->user;
  }

  public function getUserUUID(): UUID {
    return $this->user->uuid();
  }


  public function setUUID(UUID $uuid): Comment
  {
    $this->uuid = $uuid;
    return $this;
  }


  public function setPost(Post $post): Comment
  {
    $this->post = $post;
    return $this;
  }


  public function setUser(User $user): Comment
  {
    $this->user = $user;
    return $this;
  }


  public function setText(string $text): Comment
  {
    $this->text = $text;
    return $this;
  }


  public function __toString(): string
  {
    return "комментарий с ID $this->uuid текст: $this->text" . PHP_EOL . "для поста: $this->post";
  }
}