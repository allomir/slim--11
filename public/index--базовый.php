<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);


$arr = collect([1 => 'one', 2 => 'two', 3 => 'three'])->all();

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Welcome to Slim!');
    return $response;
    // Благодаря пакету slim/http этот же код можно записать короче
    // return $response->write('Welcome to Slim!');
});

$app->get('/users', function ($request, $response) {
    return $response->write('GET /users');
});

// withStatus()
$app->post('/news', function ($request, $response) {
    return $response->withStatus(302);
    // редирект только при методе POST
});

$phones = ['93484398', '29304920', '982394029'];
$app->get('/phones', function ($request, $response) use ($phones) {
    return $response->write(json_encode($phones));
});

$companies = ['company1', 'company2', 'company3'];
// getQueryParam
$app->get('/companies', function ($request, $response) use ($companies) {
    $page = $request->getQueryParam('page', 1);
    $per = $request->getQueryParam('per', 5);
    $offset = ($page - 1) * $per;
        // In real life this request is bad, because it pull all data from the database
        // It would be better to do sql query with LIMIT and OFFSET
        
    $sliceOfCompanies = array_slice($companies, $offset, $per);
    return $response->write(json_encode($sliceOfCompanies));
});

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

    // динамический маршрут
        // slug - например {name-cource}, плейсхолдер
        // {id} - placeholder плайсхолдер-индетификатор 
        // array $args  -  доступ к плейсхолдерам, третий параметр в функции-обработчик
    // статический маршрут
        // соответствует только одному адресу, без slug и плейсхолдеров

# Задание 5
$companies = [
    ['id' => 4, /* другие свойства */],
    ['id' => 2, /* другие свойства */],
    ['id' => 8, /* другие свойства */],
    ['id' => 1, /* другие свойства */],
];

$app->get('/company/{id}', function ($request, $response, $args) use ($companies) {
    $id = (int) ($args['id']);
        // Также подходит intval()

    $company = array_filter($companies, function(array $company) use($id) {
        return $company['id'] === $id;
    });

    if (!$company) {
        return $response->withStatus(404)->write('Page not found');
        // ->write('Page not found') - заменяет стандартную страницу с ошибкой 404
    }
    $company = array_shift($company);

    // [$company] = array_values($company);
        // решение 2 - не работает без array_values, тк !!!!! array_filter() возвращает ассоциативный массив с ключами как есть !!!!!
        // а массовое присваивание работает только если ключи идут последовательно и с 0
    
    return $response->write(json_encode($company));
});

$app->get('/companies-reg/{id:[0-9]+}', function ($request, $response, $args) use ($companies) {
    $id = $args['id'];
    $company = collect($companies)->firstWhere('id', $id);
    if (!$company) {
        return $response->withStatus(404)->write('Page not found');
    }
    return $response->write(json_encode($company));
});

$app->run();

// $request->getHeaders();
    // Возвращает все заголовки

// $response->getBody()
    // Тело ответа
    // !!!response никогда не изменяется. Любой его метод формирует новый $response, те необходимо присвоение или непосредственно return

// $response->write()
    // запись в тело ответа

// getQueryParams() 
    // извлекает все параметры запроса
    // getQueryParam($name, $defaultValue) 
        // извлекает значение конкретного параметра. Вторым параметром принимает значение по умолчанию