<?php

namespace Veliafar\PhpBlog\Http\Actions\Like;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\AppException;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeAlreadyExistException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Like\CommentLike;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\CommentLikeRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\Auth\AuthenticationUsernameInterface;
use Veliafar\PhpBlog\Http\Auth\AuthenticationUUIDInterface;
use Veliafar\PhpBlog\Http\Auth\TokenAuthenticationInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class CreateCommentLike implements ActionInterface
{
  public function __construct(
    private CommentLikeRepositoryInterface $likeRepository,
    private CommentRepositoryInterface     $commentRepository,
    private TokenAuthenticationInterface   $identification,
    private LoggerInterface                $logger,
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      $user = $this->identification->user($request);
    } catch (AppException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
    } catch (HttpException|InvalidArgumentException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $this->likeRepository->checkUserLikeForCommentExist($commentUuid, $user->uuid());
    } catch (LikeAlreadyExistException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $comment = $this->commentRepository->get($commentUuid);
    } catch (CommentNotFoundException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $newLikeUuid = UUID::random();
      $like = new CommentLike(
        $newLikeUuid,
        $comment,
        $user
      );
    } catch (HttpException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    $this->likeRepository->save($like);
    $this->logger->info("Comment Like created: $newLikeUuid");
    return new SuccessfulResponse([
      'uuid' => (string)$newLikeUuid,
    ]);
  }
}