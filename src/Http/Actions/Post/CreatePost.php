<?php

namespace Veliafar\PhpBlog\Http\Actions\Post;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\Auth\IdentificationUsernameInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class CreatePost implements ActionInterface
{
  public function __construct(
    private PostRepositoryInterface $postsRepository,
    private IdentificationUsernameInterface $identification,
    private LoggerInterface $logger,
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {

    $user = $this->identification->user($request, false);

    try {
      $newPostUuid = UUID::random();

      $post = new Post(
        $newPostUuid,
        $user,
        $request->jsonBodyField('title'),
        $request->jsonBodyField('text'),
      );
    } catch (HttpException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    $this->postsRepository->save($post);
    $this->logger->info("Post created: $newPostUuid");

    return new SuccessfulResponse([
      'uuid' => (string)$newPostUuid,
    ]);
  }

}