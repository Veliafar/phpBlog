<?php

namespace Veliafar\PhpBlog\Http\Actions\Post;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindPostByUuid implements ActionInterface
{
  public function __construct(
    private PostRepositoryInterface $postRepository,
    private LoggerInterface         $logger,
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      $postUUID = $request->query('postUUID');
    } catch (HttpException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    try {
      $post = $this->postRepository->get(new UUID($postUUID));
    } catch (PostNotFoundException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    $this->logger->info("Post found: $postUUID");
    return new SuccessfulResponse([
      'postUUID' => (string)$post->uuid(),
      'userUUID' => (string)$post->getUserUUID(),
      'userFirstName' => $post->getUser()->name()->first(),
      'userSecondName' => $post->getUser()->name()->last(),
      'username' => $post->getUser()->username(),
      'title' => $post->getTitle(),
      'text' => $post->getText(),
    ]);
  }
}