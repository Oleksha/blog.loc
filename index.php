<?php

use Blog\PostMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader('templates');
$view = new Environment($loader);

$config = include 'config/database.php';
$dsn = $config['dsn'];
$user = $config['user'];
$pass = $config['pass'];

try {
  $connection = new PDO($dsn, $user, $pass);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
  echo 'Ошибка подключения к БД: ' . $exception->getMessage();
  die;
}

$app = AppFactory::create();

$postMapper = new PostMapper($connection);

$app->get('/', function (Request $request, Response $response, $args) use ($view) {
  $body = $view->render('index.twig');
  $response->getBody()->write($body);
  return $response;
});
$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
  $body = $view->render('about.twig', [
    'name' => 'Олекша'
  ]);
  $response->getBody()->write($body);
  return $response;
});
$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view) {
  global $postMapper;
  $post = $postMapper->getByUrlKey((string) $args['url_key']);
  if (empty($post)) {
    $body = $view->render('not-found.twig');
  } else {
    $body = $view->render('post.twig', [
      'post' => $post
    ]);
  }

  $response->getBody()->write($body);
  return $response;
});

$app->run();
