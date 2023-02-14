<?php

namespace Veliafar\PhpBlog\Http\Actions\Comment;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindCommentByUuid implements ActionInterface
{
  public function __construct(
    private CommentRepositoryInterface $commentsRepository,
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
      $commentUUID = $request->query('commentUUID');
    } catch (HttpException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    try {
      $comment = $this->commentsRepository->get(new UUID($commentUUID));
    } catch (CommentNotFoundException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    $commentUUID = (string)$comment->uuid();
    $this->logger->info("Comment found: $commentUUID");

    return new SuccessfulResponse([
      'commentUUID' => $commentUUID,

      'postUUID' => (string)$comment->getPost()->uuid(),
      'postUserUUID' => (string)$comment->getPost()->getUser()->uuid(),
      'postUserFirstName' => $comment->getPost()->getUser()->name()->first(),
      'postUserLastName' => $comment->getPost()->getUser()->name()->last(),
      'postUserUsername' => $comment->getPost()->getUser()->username(),
      'postTitle' => $comment->getPost()->getTitle(),
      'postText' => $comment->getPost()->getText(),

      'userUUID' => (string)$comment->getUserUUID(),
      'userFirstName' => $comment->getUser()->name()->first(),
      'userSecondName' => $comment->getUser()->name()->last(),
      'username' => $comment->getUser()->username(),

      'commentText' => $comment->getText(),
    ]);
  }
}