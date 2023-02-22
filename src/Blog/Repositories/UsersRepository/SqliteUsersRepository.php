<?php

namespace Veliafar\PhpBlog\Blog\Repositories\UsersRepository;

use \PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class SqliteUsersRepository implements UserRepositoryInterface
{
  public function __construct(
    private PDO $connection,
  )
  {
  }

  public function save(User $user): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO users (
                   uuid,
                   first_name,
                   last_name,
                   username,
                   password
                   ) VALUES (
                             :uuid,
                             :first_name,
                             :last_name,
                             :username,
                             :password
                             ) 
                     ON CONFLICT (uuid) DO UPDATE SET
                        first_name = :first_name,
                        last_name = :last_name'
    );

    $statement->execute([
      ':uuid' => (string)$user->uuid(),
      ':first_name' => $user->name()->first(),
      ':last_name' => $user->name()->last(),
      ':username' => $user->username(),
      ':password' => $user->password(),
    ]);
  }

  /**
   * @throws UserNotFoundException
   * @throws InvalidArgumentException
   */
  public function get(UUID $uuid): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE uuid = :uuid'
    );
    $statement->execute([
      'uuid' => (string)$uuid,
    ]);

    return $this->getUser($statement, $uuid);
  }

  /**
   * @throws UserNotFoundException
   * @throws InvalidArgumentException
   */
  public function getByUsername(string $username): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE username = :username'
    );
    $statement->execute([
      'username' => $username,
    ]);

    return $this->getUser($statement, $username);
  }

  /**
   * @throws UserNotFoundException
   * @throws InvalidArgumentException
   */
  private function getUser(PDOStatement $statement, string $usernameOrUUID): User
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new UserNotFoundException(
        "Cannot get user: $usernameOrUUID"
      );
    }

    return new User(
      new UUID($result['uuid']),
      new Name($result['first_name'], $result['last_name']),
      $result['username'],
      $result['password']
    );
  }
}