<?php

#index.php c расширением подключением шаблонизатора-контейнера
    // установка шаблонизатора-контейнера
    // composer require slim/php-view php-di/php-di
    // приложение выведет в stdout по методу $app->run();
    // аналогично работает в шаблоне $_GET['name'], но такой код не рекомендуется

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use App\Validator;
use App\ValidatorForUpdate;
use Slim\Middleware\MethodOverrideMiddleware;

session_start();

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
    // включение для переопределение метода POST в методы PATCH PUT DELETE

#Теория 6. Подключение шаблонизатора-контейнера
// $app->get('/users/{id}', function ($request, $response, $args) {
//     $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];

//     return $this->get('renderer')->render($response, 'users/show.phtml', $params);
//         // Указанный путь считается относительно базовой директории для шаблонов, заданной на этапе конфигурации
//         // $this - это Closure binding
//             // $this доступен внутри анонимной функции благодаря https://php.net/manual/ru/closure.bindto.php
//             // $this в Slim это контейнер зависимостей
//             // https://www.slimframework.com/docs/v4/objects/routing.html#closure-binding
// });

#Теория 7. Список пользователей

$users = [
    ['id' => 1, 'firstName' => 'one', 'lastName' => 'lastName-one', 'email' => 'email-one'],
    ['id' => 2, 'firstName' => 'two', 'lastName' => 'lastName-two', 'email' => 'email-two'],
    ['id' => 3, 'firstName' => 'three', 'lastName' => 'lastName-three', 'email' => 'email-three'],
    ['id' => 4, 'firstName' => 'four', 'lastName' => 'lastName-four', 'email' => 'email-four'],
];

// $app->get('/users', function ($request, $response) use ($users) {
//     $params = ['users' => $users];
    
//     return $this->get('renderer')->render($response, 'users/index.phtml', $params);
// });

// $app->get('/users/{id}', function ($request, $response, $args) use ($users) {
//     $id = (int) $args['id'];

//     // if (!is_int($id)) {
//     //     $isInt = true;
//     //     for($i = 0, $length=strlen($id); $i < $length - 1; $i++) {
//     //         $isInt = strpbrk($id[$i], '0123456789');
            
//     //         if ($isInt === false) break;
//     //     }
//     // }

//     $user = array_values(array_filter($users, function ($user) use ($id) {
//         return $user['id'] === $id;
//     }));

//     $user = $user[0] ?? null;

//     if (!$user) {
//         return $response->withStatus(404)->write('Page not found');
//         // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
//     }

//     $params = ['user' => $user];

//     return $this->get('renderer')->render($response, 'users/show.phtml', $params);
// });

# Теория 8. Пользователи и форма поиска users 

// мое решение
// $app->get('/users', function ($request, $response) use ($users) {
//     $term = $request->getQueryParam('term');
//     $params = ['users' => $users, 'term' => $term];

//     if ($term) {
//         $params['users'] = array_values(array_filter($users, function ($user) use ($term) {
//             return stripos($user['firstName'], $term) === 0;
//         }));
//     }

//     return $this->get('renderer')->render($response, 'users/index.phtml', $params);
// });

// учителя решение
// $app->get('/users', function ($request, $response) use ($users) {
//     $term = $request->getQueryParam('term');
//     $result = collect($users)->filter(
//         fn($user) => empty($term) ? true : s($user['firstName'])->ignoreCase()->startsWith($term)
//     );
//     $params = [
//         'users' => $result,
//         'term' => $term
//     ];
//     return $this->get('renderer')->render($response, 'users/index.phtml', $params);
// });

# Задание 8 CRUD-1 Форма регистрации 
# Задание 10 Именнованные маршруты 
# Задание 11 Flash сообщения для создания пользователя
# Задание 15 CRUD-3 обновление имени пользователя
# задание 16 CRUD-4 удаление
# задание 18 Аунтификация на основе сессий - вход пользователя
    // отключить обработчик маршрута /users/{id}, приложение ругается на похожие методы
    // форма делает action на POST users
    // добавлено use app\Validator;
    // переделка курсов под repo
    // на 10 задании добавлены именнованные маршруты к users и users/{id}
        // используется для формирования ссылок на основе альясов существующих маршрутов
        // именнованные маршруты - это альясы маршрутов, указанных в ->get первым параметром
        // ->setName('users') -- метод для установки именнованного маршрута (яльяса маршрута)
        // $router = $app->getRouteCollector()->getRouteParser(); -- получение всех маршрутов в массиве
        // $router->urlFor('users'); -- /users -- получение маршрута
        // в обработчике применяется при редиректе
    // на 11 задании добавляется session_start();
        // подключаем с помощью ->set('flash' ...)
        // создаем flash типа success и сохраняем его после успешного ->save($user) с помощью $this->get('flash')->addMessage('success', 'Успешная регистрация')
        // после переадресации сообщение выводится в маршруте /users (в месте перенаправлеиня) с помощью  $this->get('flash')->getMessages();
    // добавлен пейджинг
    // на 15 задании
        // добавляем форму изменения информации с полем Имя /template/users/edit.phtml
        // в форме отправляем скрытое поле переопределяющее POST во ФВерке
        // нужно включить поддержку переопределения метода в самом Slim
            // use Slim\Middleware\MethodOverrideMiddleware;
            // $app->add(MethodOverrideMiddleware::class); // включение поддержки методы PATCH PUT DELETE
            // метод UserReppository::save() не обновляет а сохраняет нового, требуется отладка
            // чтобы шаблоны форм пользователя при создании и обновленни имели одинаковые части нужно сделать общие части при выводе $postData или $userData
                // также это позволяет передавать пустые значения при первом показе форм, а не текущие данные пользователя
    // на 16 задании
        // добавлено в show форма с кнопкой удаления
        // в теории разбирается что такое авторизация и аунтификация
    // на 18 задании
        // аунтификация похожа на создание, только вместо ->save() сохраняем индефикатор в сессию
        //  простая форам входа - поле email, кнопка sign in

$usersRepo = new App\UserRepository();
// $usersRepo = new App\UserRepositoryCoo();

$router = $app->getRouteCollector()->getRouteParser();
    // Получаем роутер – объект отвечающий за хранение и обработку маршрутов

$container->set('flash', function () {
    return new Slim\Flash\Messages();
});
    // Задание 11 Flash сообщения для создания пользователя

$app->get('/', function ($request, $response) use ($router) {
    $response->getBody()->write('Welcome to Slim!');

    $response = $response->write('<br />' . $router->urlFor('users')); // /users
    $response = $response->write('<br />' . $router->urlFor('user', ['id' => 4])); // /users/4

    return $response;
});

$app->get('/users', function ($request, $response) use ($usersRepo) {

    # Flash
    $flashMessages = $this->get('flash')->getMessages();

    $users = $usersRepo->getAll();
    # поиск GET пользователи
    $term = $request->getQueryParam('term');
    if ($term) {
        $users = array_values(array_filter($users, function ($user) use ($term) {
            return stripos($user['name'], $term) === 0;
        }));
    }

    # Пейджинг
    $page = $request->getQueryParam('page', 1);
    $per = $request->getQueryParam('per', 5);
    $offset = ($page - 1) * $per;
        // In real life this request is bad, because it pull all data from the database
        // It would be better to do sql query with LIMIT and OFFSET

    # Массив пейджинга
    $pagesCount = ceil((count($users) / $per));
    $paging = array_combine(range(1, $pagesCount), range(1, $pagesCount));
    array_walk($paging, function (&$value, $key) {
        $value = '/users?page=' . $value;
    });
    $paging['назад' ] = ($page > 1) ? '/users?page=' . ($page - 1) : '';
    $paging['вперед' ] = ($page < $pagesCount) ? '/users?page=' . ($page + 1) : '';

    # Пользователи по пейджингу
    $users = array_slice($users, $offset, $per);

    $params = [
        'users' => $users, 
        'term' => $term,
        'flash' => $flashMessages,
        'paging' => $paging, 
    ];

    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
})->setName('users');

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['name' => '', 'email' => '', 'password' => '', 'passwordConfirmation' => '', 'city' => ''],
        'errors' => []
    ];

    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
});

$app->post('/users', function ($request, $response) use ($usersRepo, $router) {
    $validator = new Validator();
    $user = $request->getParsedBodyParam('user');
    $errors = $validator->validate($user);
    
    if (count($errors) === 0) {
        $usersRepo->save($user);
        $this->get('flash')->addMessage('success', 'Успешная регистрация');
            // работает на основе сессий, необходимо session_start()

        return $response->withRedirect($router->urlFor('users'), 302);
            // Обратите внимание на использование именованного роутинга (альяса)
    }

    $params = [
        'user' => $user,
        'errors' => $errors
    ];

    // $response = $response->withStatus(422);
    return $this->get('renderer')->render($response->withStatus(422), "users/new.phtml", $params);
        // Если возникли ошибки (не пройдена валидация), то устанавливаем код ответа в 422 и рендерим форму с указанием ошибок
});

$app->get('/users/signin', function ($request, $response) {
    $params = [
        'user' => ['email' => '', 'password' => ''],
        'errors' => []
    ];

    return $this->get('renderer')->render($response, 'users/signin.phtml', $params);
});

$app->get('/users/exit', function ($request, $response) use ($usersRepo, $router) {
    unset($_SESSION['user']);

    return $response->withRedirect($router->urlFor('users'), 302);
});

$app->post('/users/signin', function ($request, $response) use ($usersRepo, $router) {

    $userData = $request->getParsedBodyParam('user');
    $user = $usersRepo->findByEmail($userData['email']);
     
    if ($user && $user['password'] === $userData['password']) {
        $_SESSION['user'] = ['id' => $user['id'], 'name' =>  $user['name']];

        $this->get('flash')->addMessage('success', 'Здравствуйте ' . $user['name']);
            // работает на основе сессий, необходимо session_start()

        return $response->withRedirect($router->urlFor('users'), 302);
            // Обратите внимание на использование именованного роутинга (альяса)
    }

    $validator = new App\ValidatorForSignin();
    $errors = $validator->validate($user);
    $errors['signin'] = 'Неверный пользователь или пароль';

    $params = [
        'user' => $user,
        'errors' => $errors
    ];

    // $response = $response->withStatus(422);
    return $this->get('renderer')->render($response->withStatus(422), "users/signin.phtml", $params);
        // Если возникли ошибки (не пройдена валидация), то устанавливаем код ответа в 422 и рендерим форму с указанием ошибок
});

$app->get('/users/{id}', function ($request, $response, $args) use ($usersRepo) {
    $users = $usersRepo->getAll();
    $id = preg_match('/(^[1-9][0-9]*)|^0$/', $args['id']) ? (int) $args['id'] : null;
        
    $user = array_values(array_filter($users, function ($user) use ($id) {
        return $user['id'] === $id;
    }));

    $user = $user[0] ?? null;

    if (!$user) {
        return $response->withStatus(404)->write('Page not found');
        // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
    }

    $params = ['user' => $user];

    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
})->setName('user');
    // динамический маршрут /users/{id} идет после статического /users/new, иначе slim вызывает ошибку о совпадении маршрутов

$app->get('/users/{id}/edit', function ($request, $response, $args) use ($usersRepo) {
    # Flash
    $flashMessages = $this->get('flash')->getMessages();

    $users = $usersRepo->getAll();
    $id = preg_match('/(^[1-9][0-9]*)|^0$/', $args['id']) ? (int) $args['id'] : null;
        
    $user = array_values(array_filter($users, function ($user) use ($id) {
        return $user['id'] === $id;
    }));

    $user = $user[0] ?? null;

    if (!$user) {
        return $response->withStatus(404)->write('Page not found');
        // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
    }

    $params = [
        'user' => $user,
        'userData' => [],
            // позволяет при первом показе показать пустую форму, а также использовать общую форму для создания и обновления
        'errors' => [],
        'flash' => $flashMessages,
    ];

    return $this->get('renderer')->render($response, 'users/edit.phtml', $params);
})->setName('user-update');

$app->patch('/users/{id}', function ($request, $response, $args) use ($usersRepo, $router) {
    $users = $usersRepo->getAll();
    $id = preg_match('/(^[1-9][0-9]*)|^0$/', $args['id']) ? (int) $args['id'] : null;

    $user = array_values(array_filter($users, function ($user) use ($id) {
        return $user['id'] === $id;
    }));

    $user = $user[0] ?? null;

    if (!$user) {
        return $response->withStatus(404)->write('Page not found');
        // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
    }

    $userData = $request->getParsedBodyParam('user');

    $validator = new ValidatorForUpdate();
    $errors = $validator->validate($userData);
    
    if (count($errors) === 0) {
        $user['name'] = $userData['name'];
        $usersRepo->save($user);
        $this->get('flash')->addMessage('success', 'Успешное обновление');
            // работает на основе сессий, необходимо session_start()

        $url = $router->urlFor('user-update', ['id' => $user['id']]);

        return $response->withRedirect($url, 302);
            // Обратите внимание на использование именованного роутинга (альяса)
    }

    $params = [
        'user' => $user,
        'userData' => $userData,
        'errors' => $errors,
        'flash' => [],
    ];

    $response = $response->withStatus(422);
        // 422 - есть ошибки валидация
    return $this->get('renderer')->render($response, 'users/edit.phtml', $params);
});

$app->delete('/users/{id}', function ($request, $response, $args) use ($usersRepo, $router) {
    $users = $usersRepo->getAll();
    $id = preg_match('/(^[1-9][0-9]*)|^0$/', $args['id']) ? (int) $args['id'] : null;

    $user = array_values(array_filter($users, function ($user) use ($id) {
        return $user['id'] === $id;
    }));

    $user = $user[0] ?? null;

    if (!$user) {
        return $response->withStatus(404)->write('Page not found');
        // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
    }

    $usersRepo->destroy($id);
    $this->get('flash')->addMessage('success', 'User has been deleted');
    $url = $router->urlFor('users');

    return $response->withRedirect($url, 302);
});

# Задание 9. Форма создание новый курс + ЗАДАНИЕ 12. Установка флеш для курсов
    // формы в соответствующей папке slim public/courses
    // не создан репо (абстрация данных) для курсов, без них не будет работать /courses
    // валидатор класс Validator--courses.php при использовании переименовать
    // на 12 задании
        // '/' — выводит флеш сообщения в шаблон templates/index.phtml.
        // '/courses' — добавляет сообщение Course Added во Flash и делает редирект на /.

// $app->get('/', function ($request, $response) {
//     $flash = $this->get('flash')->getMessages();
//     $params = ['flash' => $flash];
//     return $this->get('renderer')->render($response, 'index.phtml', $params);
// });

// $app->get('/courses', function ($request, $response) use ($repo) {
//     $params = [
//         'courses' => $repo->all()
//     ];
//     return $this->get('renderer')->render($response, 'courses/index.phtml', $params);
// });

// $app->get('/courses/new', function ($request, $response) {
//     $params = [
//         'course' => ['title' => '', 'paid' => ''],
//         'errors' => []
//     ];

//     return $this->get('renderer')->render($response, 'courses/new.phtml', $params);
// });

// $app->post('/courses', function ($request, $response) use ($repo) {
//     $validator = new App\Validator();
//     $course = $request->getParsedBodyParam('course');
//     $errors = $validator->validate($course);
    
//     if (count($errors) === 0) {
//         $repo->save($course);
//         $this->get('flash')->addMessage('success', 'Course Added');

//         return $response->withRedirect('/courses');
//     }

//     $params = [
//         'course' => $course,
//         'errors' => $errors
//     ];
    
//     return $this->get('renderer')->render($response->withStatus(422), 'courses/new.phtml', $params);
// });


# ЗАДАНИЕ 13 CRUD-1 список и показ posts статей + ЗАДАНИЕ 14 CRUD-2 Создание поста
    // репо для пост не создавалась
    // на 14 задании
        // добавлено форма создание поста ->get('/posts/new')
        // добавлен обработчик создание поста ->post('/posts')
        // добавлен $router - именнованные маршруты
        // добавлен получение именнованного маршрута при редиректе после успешного сохранения
        // session_start(); -- необходимо в сценарии выполнить старт сессии для флеш
        // в /posts добавлен вывод флеш
        // флеш передается в шаблон в params

// $repo = new App\PostRepository();
// $router = $app->getRouteCollector()->getRouteParser();

// $app->get('/posts', function ($request, $response) use ($repo) {
//     # Flash
//     $flash = $this->get('flash')->getMessages();

//     $per = 5;
//     $page = $request->getQueryParam('page', 1);
//     $offset = ($page - 1) * $per;
//         // In real life this request is bad, because it pull all data from the database
//         // It would be better to do sql query with LIMIT and OFFSET
//     $posts = $repo->all();
//     $posts = array_slice($posts, $offset, $per);

//     $params = [
//         'page' => $page,
//         'posts' => $posts,
//         'flash' => $flash,
//     ];

//     return $this->get('renderer')->render($response, 'posts/index.phtml', $params);
// })->setName('posts');
 
// $app->get('/posts/new', function ($request, $response) {
//     $params = [
//         'post' => ['name' => '', 'body' => ''],
//         'errors' => []
//     ];

//     return $this->get('renderer')->render($response, 'posts/new.phtml', $params);
// });

// $app->post('/posts', function ($request, $response) use ($repo, $router) {
//     $validator = new App\Validator();
//     $post = $request->getParsedBodyParam('post');
//     $errors = $validator->validate($post);
    
//     if (count($errors) === 0) {
//         $repo->save($post);
//         $this->get('flash')->addMessage('success', 'Post has been created');
//             // работает на основе сессий, необходимо session_start()

//         $url = $router->urlFor('posts');
//         return $response->withRedirect($url);
//     }

//     $params = [
//         'post' => $post,
//         'errors' => $errors
//     ];
    
//     $response = $response->withStatus(422);
//     return $this->get('renderer')->render($response->withStatus(422), 'posts/new.phtml', $params);
// });

// $app->get('/posts/{id}', function ($request, $response, array $args) use ($repo) {
//     $id = $args['id'];
//     $post = $repo->find($id);
//     if (!$post) {
//         return $response->withStatus(404)->write('Page not found');
//     }
//     $params = [
//         'post' => $post,
//     ];
//     return $this->get('renderer')->render($response, 'posts/show.phtml', $params);
// })->setName('post');


# Задание 16 CRUD-3 Обновление поста
    // добавлены файлы валидации ValidatorForPost
    // добавлены файла класс репозитория для поста PostRepository
    // при первом выводе используется пустой массив postData, также позволяет сделать общую форму для создания и обновления

// $app->get('/posts/{id}/edit', function ($request, $response, array $args) use ($repo) {
//     $post = $repo->find($args['id']);
//     $params = [
//         'post' => $post,
//         'errors' => [],
//         'postData' => $post
//     ];
//     return $this->get('renderer')->render($response, 'posts/edit.phtml', $params);
// });
 
// $app->patch('/posts/{id}', function ($request, $response, array $args) use ($repo, $router) {
//     $post = $repo->find($args['id']);
//     $postData = $request->getParsedBodyParam('post');
 
//     $validator = new App\Validator();
//     $errors = $validator->validate($postData);
 
//     if (count($errors) === 0) {
//         $post['name'] = $postData['name'];
//         $post['body'] = $postData['body'];
//         $repo->save($post);
//         $this->get('flash')->addMessage('success', 'Post has been updated');
//         return $response->withRedirect($router->urlFor('posts'));
//     }
 
//     $params = [
//         'post' => $post,
//         'postData' => $postData,
//         'errors' => $errors
//     ];
 
//     return $this->get('renderer')
//                 ->render($response->withStatus(422), 'posts/edit.phtml', $params);
// });

# Задание 17. Корзина с товарами на основе cookie c json_encode
    // пример с шаблоном формой корзины см. cart/index.php

    ## Мое решение
    // $app->post('/cart-items', function ($request, $response) {
    //     $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);
    //     $item = $request->getParsedBodyParam('item');
    
    //     $cartNew = array_map(function ($cartItem) use($item) {
    //         $count = $cartItem['count'] ?? 1;
            
    //         if ($cartItem['id'] === $item['id']) {
    //             $cartItem['count'] = $count + 1;
    //         }
    //         return $cartItem;
    //     }, $cart);
    
    //     if ($cart === $cartNew) {
    //         $cartNew[] = $item;
    //     }
    
    //     $cartNew = json_encode($cartNew);
        
    //     return $response->withHeader('Set-Cookie', "cart=$cartNew")
    //     ->withRedirect('/');
    // });
    
    // $app->delete('/cart-items', function ($request, $response) {
    //     $cart = json_encode([]);
        
    //     return $response->withHeader('Set-Cookie', "cart=$cart")
    //     ->withRedirect('/');
    // });

    # Решение учителя с индексами
    // $app->post('/cart-items', function ($request, $response) {
    //     $item = $request->getParsedBodyParam('item');
    //     $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);
     
    //     $id = $item['id'];
    //     if (!isset($cart[$id])) {
    //         $cart[$id] = ['name' => $item['name'], 'count' => 1];
    //     } else {
    //         $cart[$id]['count'] += 1;
    //     }
     
    //     $encodedCart = json_encode($cart);
    //     return $response->withHeader('Set-Cookie', "cart={$encodedCart}")
    //         ->withRedirect('/');
    // });
     
    // $app->delete('/cart-items', function ($request, $response) {
    //     $encodedCart = json_encode([]);
    //     return $response->withHeader('Set-Cookie', "cart={$encodedCart}")
    //         ->withRedirect('/');
    // });


# Задание 19. Сессия и аунтификация
    // Решение учителя
// $app->get('/', function ($request, $response) {
//     $flash = $this->get('flash')->getMessages();
//     $params = [
//         'currentUser' => $_SESSION['user'] ?? null,
//         'flash' => $flash
//     ];
//     return $this->get('renderer')->render($response, 'index.phtml', $params);
// });
 
// $app->post('/session', function ($request, $response) use ($users) {
//     $userData = $request->getParsedBodyParam('user');
 
//     $user = collect($users)->first(function ($user) use ($userData) {
//         return $user['name'] === $userData['name']
//             && hash('sha256', $userData['password']) === $user['passwordDigest'];
//     });
 
//     if ($user) {
//         $_SESSION['user'] = $user;
//     } else {
//         $this->get('flash')->addMessage('error', 'Wrong password or name');
//     }
//         return $response->withRedirect('/');
// });
 
// $app->delete('/session', function ($request, $response) {
//     $_SESSION = [];
//     session_destroy();
//     return $response->withRedirect('/');
// });

    // Продолжение Задание 19. template/users/signin--задание19.phtml
    /*
    <?php if ($currentUser): ?>
        <div><?= $currentUser['name'] ?></div>
        <form action="/session" method="post">
            <input type="hidden" name="_METHOD" value="DELETE">
            <input type="submit" value="Sign Out">
        </form>
    <?php else: ?>
        <form action="/session" method="post">
            <input type="text" required name="user[name]" value="">
            <input type="password" required name="user[password]" value="">
            <input type="submit" value="Sign In">
        </form>
    <?php endif; ?>
    */

$app->run();


// request 
    // соответствует PSR7 - интерфейс для работы с запросами и ответами

// $request->getHeaders();
    // Возвращает все заголовки как массив ключ => [значение1, значение2 ...]
    // пример. 
        // foreach ($request->getHeaders() as $name => $values) {
        //     echo $name . ': ' . implode(', ', $values);
        // }

// $request->getHeader('Host');
    // Возвращает значение заголовка Host
// $request->hasHeader('Accept');
    // Проверяет был ли указан заголовок

// $request->getQueryParams() 
    // извлекает все параметры запроса
    // getQueryParam($name, $defaultValue) 
        // извлекает значение конкретного параметра. Вторым параметром принимает значение по умолчанию


// response 
    // соответствует PSR7 - стандартизированный интерфейс для работы с запросами и ответами, эмуляции поведения HTTP
    // иммутабельный (неизменяемом) стиль и реализует текучий интерфейс (fluent interface)
    // одно исключение Единственная часть в Response, которую можно менять – тело ответа. 
        // Такое исключение Из-за особенности PHP https://www.php-fig.org/psr/psr-7/#13-streams
        // те при изменении тела не используется иммутабельный стиль, и можно изменять и возвращать тело $response
    // По умолчанию содержит код ответа 200.

// $response->getStatusCode();
    // Статус ответа. По умолчанию 200.

// $newResponse = $response->withStatus(302);
    // response не меняется!  иммутабельный (неизменяемом) стиль Те $newResponse === $response; // false

// $response->withHeader('Content-Type', 'text/html');

// $response->getBody()
    // Тело ответа, возвращает специальный объект-поток (stream). Этот объект можно изменять, записывая туда данные.

// $response->write()
    // запись в тело ответа
    // Эта функция write возвращает количество переданных байт

// $response->withStatus(404)
// $response->withRedirect('/users', 302);
    // по умолчанию 302, может быть опущено

// $request->getParsedBody() 
    // извлекает все данные
// $request->getParsedBodyParam($name, $defaultValue)
    // извлекает значение конкретного параметра. Вторым параметром принимает значение по умолчанию.
