<?php

namespace Veliafar\PhpBlog\Blog\Repositories\LikeRepository;

use PDO;
use Veliafar\PhpBlog\Blog\CommentLike;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeAlreadyExistException;
use Veliafar\PhpBlog\Blog\Exceptions\LikeNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\PostLike;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\UUID;

class SqliteCommentLikesRepository implements CommentLikeRepositoryInterface
{

  public function __construct(
    private PDO                   $connection,
    private SqliteCommentsRepository $commentsRepository,
    private SqliteUsersRepository $userRepository
  )
  {
  }

  public function save(CommentLike $like): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO commentLikes (uuid, comment_uuid, user_uuid) VALUES (:uuid, :comment_uuid, :user_uuid)'
    );

    $statement->execute([
      ':uuid' => (string)$like->uuid(),
      ':comment_uuid' => (string)$like->getComment()->uuid(),
      ':user_uuid' => (string)$like->getUser()->uuid(),
    ]);
  }

  /**
   * @throws UserNotFoundException
   * @throws LikeNotFoundException
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   * @throws CommentNotFoundException
   */
  public function get(UUID $uuid): CommentLike
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM commentLikes WHERE uuid = :uuid'
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

    $comment = $this->commentsRepository->get(new UUID($result['comment_uuid']));
    $user = $this->userRepository->get(new UUID($result['user_uuid']));

    return new CommentLike(
      new UUID($result['uuid']),
      $comment,
      $user,
    );
  }

  /**
   * @throws UserNotFoundException
   * @throws LikeNotFoundException
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   * @throws CommentNotFoundException
   */
  public function getByCommentUUID(UUID $comment_uuid): array
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM commentLikes WHERE comment_uuid = :$comment_uuid'
    );
    $statement->execute([
      ':comment_uuid' => (string)$comment_uuid,
    ]);

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new LikeNotFoundException(
        "Cannot get like: $comment_uuid"
      );
    }

    $commentLikes = [];

    foreach ($result as $commentLike) {
      $comment = $this->commentsRepository->get(new UUID($commentLike['comment_uuid']));
      $user = $this->userRepository->get(new UUID($commentLike['user_uuid']));

      $commentLikes[] = new CommentLike(
        uuid: new UUID($commentLike['uuid']),
        comment: $comment,
        user: $user,
      );
    }

    return $commentLikes;
  }

  /**
   * @throws LikeAlreadyExistException
   */
  public function checkUserLikeForCommentExist(UUID $commentUUID, UUID $userUUID): void
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM commentLikes WHERE comment_uuid = :$commentUUID AND user_uuid = :userUUID'
    );

    $statement->execute([
      ':$commentUUID' => (string)$commentUUID,
      ':userUUID' => (string)$userUUID,
    ]);

    $isExisted = $statement->fetch();

    if ($isExisted) {
      throw new LikeAlreadyExistException(
        'Like from user for this comment already exist'
      );
    }
  }
}