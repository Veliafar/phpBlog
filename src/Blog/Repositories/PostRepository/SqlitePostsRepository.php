<?php

namespace Veliafar\PhpBlog\Blog\Repositories\PostRepository;

use PDO;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class SqlitePostsRepository implements PostRepositoryInterface
{
  public function __construct(private PDO $connection)
  {
  }

  public function save(Post $post): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
    );

    $statement->execute([
      ':uuid' => (string)$post->uuid(),
      ':author_uuid' => (string)$post->getUserUUID(),
      ':title' => $post->getTitle(),
      ':text' => $post->getText(),
    ]);
  }


  /**
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   * @throws UserNotFoundException
   */
  public function get(UUID $uuid): Post
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM posts WHERE uuid = :uuid'
    );
    $statement->execute([
      'uuid' => (string)$uuid,
    ]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new PostNotFoundException(
        "Cannot get post: $uuid"
      );
    }

    $userRepository = new SqliteUsersRepository($this->connection);
    $user = $userRepository->get(new UUID($result['author_uuid']));

    return new Post(
      new UUID($result['uuid']),
      $user,
      $result['title'],
      $result['text'],
    );
  }

}