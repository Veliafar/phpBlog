<?php

namespace Veliafar\PhpBlog\Comment;

class Comment
{
  public function __construct(private string $id, private string $authorID, private string $articleID, private string $text)
  {
    $this->id = $id;
    $this->authorID = $authorID;
    $this->articleID = $articleID;
    $this->text = $text;
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
  public function getAuthorID(): string
  {
    return $this->authorID;
  }

  /**
   * @return string
   */
  public function getArticleID(): string
  {
    return $this->articleID;
  }

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  public function __toString(): string
  {
    return PHP_EOL . $this->text;
  }
}