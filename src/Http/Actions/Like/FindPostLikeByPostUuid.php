<?php

namespace Veliafar\PhpBlog\Http\Actions\Like;

use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\PostLikeRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindPostLikeByPostUuid implements ActionInterface
{
  public function __construct(
    private PostLikeRepositoryInterface $likeRepository
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
      return new ErrorResponse($e->getMessage());
    }
    try {
      $like = $this->likeRepository->getByPostUUID(new UUID($postUUID));
    } catch (LikeNotFoundException $e) {
      return new ErrorResponse($e->getMessage());
    }
    // Возвращаем успешный ответ
    return new SuccessfulResponse([
//      'likeUUID' => (string)$like->uuid(),
//
//      'postUUID' => (string)$like->getPost()->uuid(),
//      'postUserUUID' => (string)$like->getPost()->getUser()->uuid(),
//      'postUserFirstName' => $like->getPost()->getUser()->name()->first(),
//      'postUserLastName' => $like->getPost()->getUser()->name()->last(),
//      'postUserUsername' => $like->getPost()->getUser()->username(),
//      'postTitle' => $like->getPost()->getTitle(),
//      'postText' => $like->getPost()->getText(),
//
//      'userUUID' => (string)$like->getUser()->uuid(),
//      'userFirstName' => $like->getUser()->name()->first(),
//      'userSecondName' => $like->getUser()->name()->last(),
//      'username' => $like->getUser()->username(),

    ]);
  }
}