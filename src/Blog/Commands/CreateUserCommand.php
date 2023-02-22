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
    private LoggerInterface         $logger,
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

    $isExist = $this->userExists($username);
    if ($isExist) {
      $this->logger->warning("User already exists: $username");
      throw new CommandException("User already exists: $username");
    }

    $user = User::createFrom(
      new Name(
        $arguments->get('first_name'),
        $arguments->get('last_name')
      ),
      $username,
      $arguments->get('password')
    );

    $this->usersRepository->save(
      $user
    );

    $this->logger->info("User created: $user");
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

