<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\model\User;
use Slim\Factory\AppFactory;

$app = AppFactory::create();


$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

try {

    //verificar status do servidor
    $app->get('/api/dev/status', function (Request $request, Response $response, $args) {
        $response->getBody()->write(json_encode(["status" => "servidor online!"]));
        return $response->withHeader('Content-type', 'application/json');
    });

    // lista de usuarios casdastrados
    $app->get('/api/dev/list/user', function (Request $request, Response $response, $args) {

        $userModel = new User();
       
        $response->getBody()->write(json_encode($userModel->list($request->getParsedBody() ?? [])));

        return $response->withHeader('Content-type', 'application/json');
    });

    //login
    $app->post('/api/dev/login/user', function (Request $request, Response $response, $args) {

        $userModel = new User();
        $response->getBody()->write(json_encode($userModel->login($request->getParsedBody() ?? [])));

        return $response->withHeader('Content-type', 'application/json');
    });


    //detalhes do user 
    $app->get('/api/dev/datalhes/user/{id}', function (Request $request, Response $response, $args) {

        $userModel = new User();
        $id = intval($args['id']);
        $response->getBody()->write(json_encode($userModel->detalhes($id)));

        return $response->withHeader('Content-type', 'application/json');
    });

    //atualizar user
    $app->put('/api/dev/atualizar/user/{id}', function (Request $request, Response $response, $args) {
        $userModel = new User();
        $id = intval($args['id']);
        $response->getBody()->write(json_encode($userModel->update($id,$request->getParsedBody() ?? [])));
        return $response->withHeader('Content-type', 'application/json');
    });

    //deletar user
    $app->delete('/api/dev/deletar/user/{id}', function (Request $request, Response $response, $args) {
        $userModel = new User();
        $id = intval($args['id']);
        $response->getBody()->write(json_encode($userModel->delete($id)));
        return $response->withHeader('Content-type', 'application/json');
    });

    // criar user 
    $app->post('/api/dev/create/user', function (Request $request, Response $response, $args) {
        $userModel = new User();
        $response->getBody()->write(json_encode($userModel->create($request->getParsedBody() ?? [])));
        return $response->withHeader('Content-type', 'application/json');
    });
    $app->run();
} catch (\Exception $e) {

    echo   json_encode(["fail" => "FORAM ENCONTRADOS ERROS!", "type_error" => $e->getMessage()]);
}
