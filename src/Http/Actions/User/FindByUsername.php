<?php

namespace Veliafar\PhpBlog\Http\Actions\User;

use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
  public function __construct(
    private UserRepositoryInterface $usersRepository
  )
  {
  }

  // Функция, описанная в контракте
  public function handle(Request $request): Response
  {
    try {
      // Пытаемся получить искомое имя пользователя из запроса
      $username = $request->query('username');
    } catch (HttpException $e) {
      // Если в запросе нет параметра username -
      // возвращаем неуспешный ответ,
      // сообщение об ошибке берём из описания исключения
      return new ErrorResponse($e->getMessage());
    }
    try {
      // Пытаемся найти пользователя в репозитории
      $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      // Если пользователь не найден -
      // возвращаем неуспешный ответ
      return new ErrorResponse($e->getMessage());
    }
    // Возвращаем успешный ответ
    return new SuccessfulResponse([
      'username' => $user->username(),
      'name' => $user->name()->first() . ' ' . $user->name()->last(),
    ]);
  }

}