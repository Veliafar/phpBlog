<?php

namespace Veliafar\PhpBlog\Http;

use Veliafar\PhpBlog\Blog\Exceptions\HttpException;

class Request
{
  public function __construct(
    private array $get,
    private array $server
  )
  {
  }

  /**
   * @throws HttpException
   */
  public function path(): string
  {
    $cannotGetPathError = 'Cannot get path from the request';

    if (!array_key_exists('REQUEST_URI', $this->server)) {
      throw new HttpException($cannotGetPathError);
    }

    $components = parse_url($this->server['REQUEST_URI']);

    if (!is_array($components) || !array_key_exists('path', $components)) {
      throw new HttpException($cannotGetPathError);
    }

    return $components['path'];
  }
}