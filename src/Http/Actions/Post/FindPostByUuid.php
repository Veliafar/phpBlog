<?php

namespace Veliafar\PhpBlog\Http\Actions\Post;

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
    private PostRepositoryInterface $postRepository
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      // Пытаемся получить искомое имя пользователя из запроса
      $postID = $request->query('postID');
    } catch (HttpException $e) {
      // Если в запросе нет параметра username -
      // возвращаем неуспешный ответ,
      // сообщение об ошибке берём из описания исключения
      return new ErrorResponse($e->getMessage());
    }
    try {
      // Пытаемся найти пользователя в репозитории
      $post = $this->postRepository->get(new UUID($postID));
    } catch (PostNotFoundException $e) {
      // Если пользователь не найден -
      // возвращаем неуспешный ответ
      return new ErrorResponse($e->getMessage());
    }
    // Возвращаем успешный ответ
    return new SuccessfulResponse([
      'postID' => (string)$post->uuid(),
      'userID' => (string)$post->getUserUUID(),
      'title' => $post->getTitle(),
      'text' => $post->getText(),
    ]);
  }
}