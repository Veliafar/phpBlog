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
use Psr\Log\LoggerInterface;

//  php cli.php username=ivan first_name=Ivan last_name=Nikitin
class CreateUserCommand
{
  public function __construct(
    private UserRepositoryInterface $usersRepository,
    private LoggerInterface $logger,
  )
  {
  }

  /**
   * @throws CommandException
   * @throws InvalidArgumentException | ArgumentsException
   */
  public function handle(Arguments $arguments): void
  {

    $this->logger->info("Create user command started");

    $username = $arguments->get('username');
    // Проверяем, существует ли пользователь в репозитории
    $isExist = $this->userExists($username);
    if ($isExist) {
      // Бросаем исключение, если пользователь уже существует
      $this->logger->warning("User already exists: $username");
      throw new CommandException("User already exists: $username");
    }

    $uuid = UUID::random();
    // Сохраняем пользователя в репозиторий
    $this->usersRepository->save(new User(
      $uuid,
      new Name(
        $arguments->get('first_name'), $arguments->get('last_name')
      ),
      $username
    ));

    $this->logger->info("User created: $uuid");
  }

  private function userExists(string $username): bool
  {
    try {
      // Пытаемся получить пользователя из репозитория
      $user = $this->usersRepository->getByUsername($username);
      print_r($user);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }

}

