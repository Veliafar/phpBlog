<?php

namespace Veliafar\PhpBlog\Http\Actions\Comment;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
  public function __construct(
    private CommentRepositoryInterface $commentsRepository,
    private PostRepositoryInterface    $postsRepository,
    private UserRepositoryInterface    $usersRepository,
    private LoggerInterface            $logger,
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
    } catch (HttpException|InvalidArgumentException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    try {
      $user = $this->usersRepository->get($authorUuid);
    } catch (UserNotFoundException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $postUuid = new UUID($request->jsonBodyField('post_uuid'));
    } catch (HttpException|InvalidArgumentException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    try {
      $post = $this->postsRepository->get($postUuid);
    } catch (PostNotFoundException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $newCommentUuid = UUID::random();
      $comment = new Comment(
        $newCommentUuid,
        $post,
        $user,
        $request->jsonBodyField('text')
      );
    } catch (HttpException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    $this->commentsRepository->save($comment);
    $this->logger->info("Comment created: $newCommentUuid");
    return new SuccessfulResponse([
      'uuid' => (string)$newCommentUuid,
    ]);
  }
}