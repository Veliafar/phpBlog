<?php

namespace Veliafar\PhpBlog\Blog\Commands;

use Veliafar\PhpBlog\Blog\Exceptions\ArgumentsException;
use Veliafar\PhpBlog\Blog\Exceptions\CommandException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

//  php cli.php username=ivan first_name=Ivan last_name=Nikitin
class CreateUserCommand
{
  public function __construct(
    private UserRepositoryInterface $usersRepository
  )
  {
  }

  /**
   * @throws CommandException
   * @throws InvalidArgumentException | ArgumentsException
   */
  public function handle(Arguments $arguments): void
  {
    $username = $arguments->get('username');
    // Проверяем, существует ли пользователь в репозитории
    if ($this->userExists($username)) {
      // Бросаем исключение, если пользователь уже существует
      throw new CommandException("User already exists: $username");
    }

    // Сохраняем пользователя в репозиторий
    $this->usersRepository->save(new User(
      UUID::random(),
      new Name(
        $arguments->get('first_name'), $arguments->get('last_name')
      ),
      $username
    ));
  }

  private function userExists(string $username): bool
  {
    try {
      // Пытаемся получить пользователя из репозитория
      $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }

}

