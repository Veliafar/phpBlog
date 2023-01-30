<?php

namespace Veliafar\PhpBlog\Blog;

class Comment
{
  public function __construct(private string $id, private Post $post, private User $user, private string $text)
  {
  }

  public function id(): string
  {
    return $this->id;
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

  /**
   * @return User
   */
  public function getUser(): User
  {
    return $this->user;
  }

  public function getPostID(): string {
    return $this->post->getId();
  }

  public function __toString(): string
  {
    return "комментарий с ID $this->id текст: $this->text" . PHP_EOL . "для поста: $this->post";
  }
}