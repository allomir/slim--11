<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;

require '/composer/vendor/autoload.php';

$container = new Container();
$container->set('renderer', function () {
    return new Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$repo = new App\PostRepository();
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

$app->get('/posts', function ($request, $response) use ($repo) {
    $flash = $this->get('flash')->getMessages();

    $params = [
        'flash' => $flash,
        'posts' => $repo->all()
    ];
    return $this->get('renderer')->render($response, 'posts/index.phtml', $params);
})->setName('posts');

$app->get('/posts/new', function ($request, $response) use ($repo) {
    $params = [
        'postData' => [],
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'posts/new.phtml', $params);
});

$app->post('/posts', function ($request, $response) use ($repo, $router) {
    $postData = $request->getParsedBodyParam('post');

    $validator = new App\Validator();
    $errors = $validator->validate($postData);

    if (count($errors) === 0) {
        $id = $repo->save($postData);
        $this->get('flash')->addMessage('success', 'Post has been created');
        return $response->withHeader('X-ID', $id)
                        ->withRedirect($router->urlFor('posts'));
    }

    $params = [
        'postData' => $postData,
        'errors' => $errors
    ];

    return $this->get('renderer')->render($response->withStatus(422), 'posts/new.phtml', $params);
});

// BEGIN (write your solution here)
$app->get('/posts/{id}/edit', function ($request, $response, $args) use ($repo) {
    # Flash
    // $flash = $this->get('flash')->getMessages();
    $id = $args['id'];
    $post = $repo->find($id);
    if (!$post) {
        return $response->withStatus(404)->write('Page not found');
    }

    $params = [
        // 'flash' => $flash,
        'postData' => $post,
        'errors' => [],
    ];
    return $this->get('renderer')->render($response, 'posts/edit.phtml', $params);
})->setName('editPost');

$app->patch('/posts/{id}', function ($request, $response, $args) use ($repo, $router) {

    $id = $args['id'];
    $post = $repo->find($id);
    if (!$post) {
        return $response->withStatus(404)->write('Page not found');
    }

    $postData = $request->getParsedBodyParam('post');

    $validator = new App\Validator();
    $errors = $validator->validate($postData);

    if (count($errors) === 0) {
        $post['name'] = $postData['name'];
        $post['body'] = $postData['body'];

        $this->get('flash')->addMessage('success', 'Post has been updated');
        $repo->save($post);

        // $url = $router->urlFor('editPost', ['id' => post['id']]);
        $url = $router->urlFor('posts');
        return $response->withRedirect($url);
    }

    $params = [
        'postData' => $post,
        'errors' => $errors,
    ];

    return $this->get('renderer')->render($response->withStatus(422), 'posts/edit.phtml', $params);
});
// END

$app->delete('/posts/{id}', function ($request, $response, array $args) use ($repo, $router) {
    $repo->destroy($args['id']);
    $this->get('flash')->addMessage('success', 'Post has been deleted');
    return $response->withRedirect($router->urlFor('posts'));
});

$app->run();