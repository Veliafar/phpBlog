<?php

namespace Veliafar\PhpBlog\Blog\Commands;

use PHPUnit\Framework\TestCase;
use Veliafar\PhpBlog\Blog\Exceptions\ArgumentsException;
use Veliafar\PhpBlog\Blog\Exceptions\CommandException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\DummyUsersRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\UnitTests\Blog\DummyLogger;

class CreateUserCommandTest extends TestCase
{
  public function testItThrowAnExceptionWhenUserAlreadyExists(): void
  {
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(CommandException::class);
    $this->expectExceptionMessage("User already exists: user123");
    $command->handle(new Arguments(['username' => 'user123']));
  }

  private function makeUsersRepository(): UserRepositoryInterface
  {
    return new class implements UserRepositoryInterface {
      public function save(User $user): void
      {
        // TODO: Implement save() method.
      }

      public function get(UUID $uuid): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        throw new UserNotFoundException("Not found");
      }
    };
  }

  /**
   * @throws CommandException
   * @throws InvalidArgumentException
   */
  public function testItRequiresFirstName(): void
  {
    $usersRepository = $this->makeUsersRepository();
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage("No such argument: first_name");
    $command->handle(new Arguments(['username' => 'IvanTest']));
  }

  public function testItRequiresLastName(): void
  {
    $usersRepository = $this->makeUsersRepository();
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage("No such argument: last_name");
    $command->handle(new Arguments([
      'username' => 'IvanTest',
      'first_name' => 'IvanTest'
    ]));
  }

  /**
   * @throws CommandException
   * @throws InvalidArgumentException
   * @throws ArgumentsException
   */
  public function testItSavesUserRepository(): void
  {
    $usersRepository = new class implements UserRepositoryInterface {

      private bool $called = false;

      public function save(User $user): void
      {
        $this->called = true;
      }

      public function get(UUID $uuid): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function wasCalled(): bool
      {
        return $this->called;
      }
    };
    $command = new CreateUserCommand($usersRepository, new DummyLogger());
    $command->handle(new Arguments([
      'username' => 'IvanTest',
      'first_name' => 'IvanTest',
      'last_name' => 'Nikitin'
    ]));
    $this->assertTrue($usersRepository->wasCalled());
  }
}