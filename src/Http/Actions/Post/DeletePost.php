<?php

namespace Veliafar\PhpBlog\Http\Actions\Post;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
  public function __construct(
    private PostRepositoryInterface    $postsRepository,
    private CommentRepositoryInterface $commentRepository,
    private LoggerInterface            $logger,
  )
  {
  }


  public function handle(Request $request): Response
  {
    try {
      $postUUID = new UUID($request->query('postUUID'));
    } catch (HttpException|InvalidArgumentException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    $this->postsRepository->delete($postUUID, $this->commentRepository);
    $this->logger->info("Post deleted: $postUUID");
    return new SuccessfulResponse([
      '$postUUID' => (string)$postUUID . ' deleted',
    ]);
  }
}