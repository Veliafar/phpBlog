<?php

namespace Veliafar\PhpBlog\Blog\Repositories\LikeRepository;

use PDO;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeAlreadyExistException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\PostLike;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\UUID;

class SqlitePostLikesRepository implements PostLikeRepositoryInterface
{

  public function __construct(
    private PDO                   $connection,
    private SqlitePostsRepository $postRepository,
    private SqliteUsersRepository $userRepository
  )
  {
  }

  public function save(PostLike $like): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO postLikes (uuid, post_uuid, user_uuid) VALUES (:uuid, :post_uuid, :user_uuid)'
    );

    $statement->execute([
      ':uuid' => (string)$like->uuid(),
      ':post_uuid' => (string)$like->getPost()->uuid(),
      ':user_uuid' => (string)$like->getUser()->uuid(),
    ]);
  }

  /**
   * @throws UserNotFoundException
   * @throws LikeNotFoundException
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   */
  public function get(UUID $uuid): PostLike
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM postLikes WHERE uuid = :uuid'
    );
    $statement->execute([
      ':uuid' => (string)$uuid,
    ]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new LikeNotFoundException(
        "Cannot get like: $uuid"
      );
    }

    $post = $this->postRepository->get(new UUID($result['post_uuid']));
    $user = $this->userRepository->get(new UUID($result['user_uuid']));

    return new PostLike(
      new UUID($result['uuid']),
      $post,
      $user,
    );
  }

  /**
   * @throws UserNotFoundException
   * @throws LikeNotFoundException
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   */
  public function getByPostUUID(UUID $post_uuid): array
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM postLikes WHERE post_uuid = :$post_uuid'
    );
    $statement->execute([
      ':post_uuid' => (string)$post_uuid,
    ]);

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
      throw new LikeNotFoundException(
        "Cannot get like: $post_uuid"
      );
    }

    $postLikes = [];

    foreach ($result as $postLike) {
      $post = $this->postRepository->get(new UUID($postLike['post_uuid']));
      $user = $this->userRepository->get(new UUID($postLike['user_uuid']));

      $postLikes[] = new PostLike(
        uuid: new UUID($postLike['uuid']),
        post: $post,
        user: $user,
      );
    }

    return $postLikes;
  }

  /**
   * @throws LikeAlreadyExistException
   */
  public function checkUserLikeForPostExist(UUID $postUUID, UUID$userUUID): void
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM postLikes WHERE post_uuid = :postUUID AND user_uuid = :userUUID'
    );

    $statement->execute([
      ':postUUID' => (string)$postUUID,
      ':userUUID' => (string)$userUUID,
    ]);

    $isExisted = $statement->fetch();

    if ($isExisted) {
      throw new LikeAlreadyExistException(
        'Like from user for this post already exist'
      );
    }
  }


}