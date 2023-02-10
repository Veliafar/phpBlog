<?php

namespace Veliafar\PhpBlog\Http\Actions\Like;

use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeAlreadyExistException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Like\PostLike;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\PostLikeRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class CreatePostLike implements ActionInterface
{
  public function __construct(
    private PostLikeRepositoryInterface $likeRepository,
    private PostRepositoryInterface $postsRepository,
    private UserRepositoryInterface $usersRepository
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {

    try {
      $userUuid = new UUID($request->jsonBodyField('user_uuid'));
      $postUuid = new UUID($request->jsonBodyField('post_uuid'));
    } catch (HttpException|InvalidArgumentException $e) {
      return new ErrorResponse($e->getMessage());
    }

    try {
      $this->likeRepository->checkUserLikeForPostExist($postUuid, $userUuid);
    } catch (LikeAlreadyExistException $e) {
      return new ErrorResponse($e->getMessage());
    }

    try {
      $user = $this->usersRepository->get($userUuid);
    } catch (UserNotFoundException $e) {
      return new ErrorResponse($e->getMessage());
    }


    try {
      $post = $this->postsRepository->get($postUuid);
    } catch (PostNotFoundException $e) {
      return new ErrorResponse($e->getMessage());
    }

    try {
      $newLikeUuid = UUID::random();
      $like = new PostLike(
        $newLikeUuid,
        $post,
        $user
      );
    } catch (HttpException $e) {
      return new ErrorResponse($e->getMessage());
    }
    $this->likeRepository->save($like);
    return new SuccessfulResponse([
      'uuid' => (string)$newLikeUuid,
    ]);
  }
}