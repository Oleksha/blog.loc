<?php

namespace Blog;

use Exception;

class PostMapper
{
  /**
   * @var Database
   */
  private Database $database;

  /**
   * PostMapper конструктор
   * @param Database $connection
   */
  public function __construct(Database $database)
  {
    $this->database = $database;
  }

  /**
   * @param string $urlKey
   * @return array|null
   */
  public function getByUrlKey(string $urlKey) : ?array
  {
    $statement = $this->database->getConnection()->prepare('SELECT * FROM post WHERE url_key = :url_key');
    $statement->execute([
      'url_key' => $urlKey
    ]);

    $result = $statement->fetchAll();

    return array_shift($result);
  }

  /**
   * @param int $page
   * @param int $limit
   * @param string $direction
   * @return array|null
   * @throws Exception
   */
  public function getList(int $page = 1, int $limit = 2, string $direction = 'ASC'): ?array
  {
    if (!in_array($direction, ['DESC', 'ASC'])) {
      throw new Exception('Указанная сортировка не поддерживается');
    }
    $start = ($page - 1) * $limit;
    $statement = $this->database->getConnection()->prepare("SELECT * FROM post ORDER BY published_date $direction LIMIT $start,$limit");
    $statement->execute();
    return $statement->fetchAll();
  }

  /**
   * @return int
   */
  public function getTotalCount(): int
  {
    $statement = $this->database->getConnection()->prepare("SELECT count(post_id) as total FROM post");
    $statement->execute();
    return (int) ($statement->fetchColumn() ?? 0);
  }
}