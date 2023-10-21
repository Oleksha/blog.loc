<?php declare(strict_types=1);

namespace Blog;

use PDO;
use PDOException;

class Database
{
  /**
   * @var PDO
   */
  private PDO $connection;

  /**
   * Конструктор класса Database
   * @param string $dsn
   * @param string|null $username
   * @param string|null $password
   */
  public function __construct(PDO $connection)
  {
    //echo $dsn; die;
    try {
      $this->connection = $connection;
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
      echo 'Ошибка подключения к БД: ' . $exception->getMessage();
      die;
    }
  }

  /**
   * Получение актуального подключения к БД
   * @return PDO
   */
  public function getConnection(): PDO
  {
    return $this->connection;
  }
}