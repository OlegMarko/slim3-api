<?php

require __DIR__ . '/vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);

$app = new Slim\App($c);

ORM::configure([
    'connection_string' => 'mysql:host=127.0.0.1:3306;dbname=lumen',
    'username' => 'mysql',
    'password' => 'mysql'
]);

$app->get('/', function () {

    echo 'Slim 3';
});

$app->get('/employers', function () {

    header('Content-Type: application/json;charset=utf-8');

    $employers = ORM::for_table('films')->findArray();

    echo json_encode($employers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

$app->get('/employers/{id}', function (\Slim\Http\Request $request) use ($app) {

    header('Content-Type: application/json;charset=utf-8');

    $id = $request->getAttribute('id');
    $employer = ORM::for_table('films')->findOne($id);

    if (!$employer) {
        echo json_encode([
            'data' => [],
            'code' => 404,
            'message' => 'Item not found.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        $employer = [
            'data' => [
                'name' => $employer->get('name'),
                'description' => $employer->get('description')
            ],
            'code' => 200
        ];

        echo json_encode($employer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
});

$app->post('/employers', function (\Slim\Http\Request $request) use ($app) {

    header('Content-Type: application/json;charset=utf-8');

    $request_data = $request->getParsedBody();
    $request_data['created_at'] = date('Y-m-d H:i:m');
    $request_data['updated_at'] = date('Y-m-d H:i:m');

    $employer = ORM::for_table('films')->create($request_data);

    if ($employer->save()) {
        echo json_encode([
            'data' => [],
            'code' => 200,
            'message' => 'Item created.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'data' => [],
            'code' => 422,
            'message' => 'Error created.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
});

$app->put('/employers/{id}', function (\Slim\Http\Request $request) use ($app) {

    header('Content-Type: application/json;charset=utf-8');

    $request_data = $request->getParsedBody();

    $employer = ORM::for_table('films')->find_one($request_data['id']);
    $employer->set('name', $request_data['name']);
    $employer->set('description', $request_data['description']);
    $employer->set('updated_at', date('Y-m-d H:i:s'));

    if ($employer->save()) {
        echo json_encode([
            'data' => [],
            'code' => 200,
            'message' => 'Item updated.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'data' => [],
            'code' => 422,
            'message' => 'Error updated.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
});

$app->delete('/employers/{id}', function (\Slim\Http\Request $request) use ($app) {

    header('Content-Type: application/json;charset=utf-8');

    $id = $request->getAttribute('id');
    $employer = ORM::for_table('films')->find_one($id);

    if ($employer->delete()) {
        echo json_encode([
            'data' => [],
            'code' => 200,
            'message' => 'Item deleted.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'data' => [],
            'code' => 422,
            'message' => 'Error deleted.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
});

$app->run();