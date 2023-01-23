<?php

namespace Veliafar\PhpBlog\Article;

class Article
{
  public function __construct(private string $id, private string $authorID, private string $header, private string $text)
  {
    $this->id = $id;
    $this->authorID = $authorID;
    $this->header = $header;
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
  public function getHeader(): string
  {
    return $this->header;
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
    return PHP_EOL . $this->header . PHP_EOL . $this->text;
  }
}