<?php

namespace Veliafar\PhpBlog\Blog\Commands;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
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
    $command->handle(new Arguments(
      [
        'username' => 'user123',
        'password' => 'password'
      ]
    ));
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
    $command->handle(new Arguments(
      [
        'username' => 'IvanTest',
        'last_name' => 'last_name',
        'password' => 'password'
      ]
    ));
  }

  public function testItRequiresLastName(): void
  {
    $usersRepository = $this->makeUsersRepository();
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage("No such argument: last_name");
    $command->handle(new Arguments([
      'username' => 'IvanTest',
      'first_name' => 'IvanTest',
      'password' => 'password'
    ]));
  }

  public function testItRequiresPassword(): void
  {
    $usersRepository = $this->makeUsersRepository();
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage("No such argument: password");
    $command->handle(new Arguments([
      'username' => 'IvanTest',
      'first_name' => 'IvanTest',
      'last_name' => 'last_name',
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
      'last_name' => 'Nikitin',
      'password' => 'password'
    ]));
    $this->assertTrue($usersRepository->wasCalled());
  }

  /**
   * @throws ExceptionInterface
   */
  public function testItRequiresLastNameCreateUserConsole(): void
  {
    // Тестируем новую команду
    $command = new CreateUserConsole(
      $this->makeUsersRepository(),
    );
    // Меняем тип ожидаемого исключения ..
    $this->expectException(RuntimeException::class);
    // .. и его сообщение
    $this->expectExceptionMessage(
      'Not enough arguments (missing: "last_name").'
    );
    // Запускаем команду методом run вместо handle
    $command->run(
      new ArrayInput([
        'username' => 'Ivan',
        'password' => 'some_password',
        'first_name' => 'Ivan',
      ]),
      // Передаём также объект,
      // реализующий контракт OutputInterface
      // Нам подойдёт реализация,
      // которая ничего не делает
      new NullOutput()
    );
  }

  /**
   * @throws ExceptionInterface
   */
  public function testItSavesUserToRepository(): void
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
    $command = new CreateUserConsole(
      $usersRepository
    );
    $command->run(
      new ArrayInput([
        'username' => 'Ivan',
        'password' => 'some_password',
        'first_name' => 'Ivan',
        'last_name' => 'Nikitin',
      ]),
      new NullOutput()
    );
    $this->assertTrue($usersRepository->wasCalled());
  }
}