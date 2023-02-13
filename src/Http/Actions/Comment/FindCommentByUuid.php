<?php

namespace Veliafar\PhpBlog\Http\Actions\Comment;

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
    private CommentRepositoryInterface $commentsRepository
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
      return new ErrorResponse($e->getMessage());
    }
    try {
      $comment = $this->commentsRepository->get(new UUID($commentUUID));
    } catch (CommentNotFoundException $e) {

      return new ErrorResponse($e->getMessage());
    }
    // Возвращаем успешный ответ
    return new SuccessfulResponse([
      'commentUUID' => (string)$comment->uuid(),

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