<?php


require __DIR__ . '/vendor/autoload.php';


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\model\User;
use Slim\Factory\AppFactory;

session_start();
$_SESSION['logged'] = true;
$app = AppFactory::create();

// Parse json, form data and xml
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
        if ($_SESSION['logged']) {
            $userModel = new User();
            $response->getBody()->write(json_encode($userModel->list()));
        } else {
            throw new Exception('Não autorizado');
        }
        return $response->withHeader('Content-type', 'application/json');
    });

    //realizar login  do user
    $app->post('/api/dev/login', function (Request $request, Response $response, $args) {
        if (!($_SESSION['logged'])) {
            $userModel = new User();
            if ($userModel->login($request->getParsedBody())) {
                $response->getBody()->write(json_encode(["success" => "logged"]));
            } else {
                $response->getBody()->write(json_encode(["fail" => "error ao logar"]));
            }
        } else {
            throw new Exception('Não autorizado');
        }
        return $response->withHeader('Content-type', 'application/json');
    });

    //detalhes do user 
    $app->get('/api/dev/datalhes/user/{id}', function (Request $request, Response $response, $args) {
        if ($_SESSION['logged']) {
            $userModel = new User();
            $id = intval($args['id']);
            $_SESSION['detalhes'] = $id;
            $response->getBody()->write(json_encode($userModel->detalhes($id)));
        } else {
            throw new Exception('Não autorizado');
        }
        return $response->withHeader('Content-type', 'application/json');
    });

    //atualizar user
    $app->put('/api/dev/atualizar/user', function (Request $request, Response $response, $args) {
        if ($_SESSION['logged'] && $_SESSION['detalhes']) {
            $userModel = new User();
            $id = $_SESSION['detalhes'];
            $response->getBody()->write(json_encode($userModel->update($id)));
        } else {
            throw new Exception('Não autorizado');
        }
        return $response->withHeader('Content-type', 'application/json');
    });

    // criar user 
    $app->post('/api/dev/create/user', function (Request $request, Response $response, $args) {
        if (!$_SESSION['logged']) {
            $userModel = new User();
            $response->getBody()->write(json_encode($userModel->create($request->getParsedBody())));
        } else {
            throw new Exception('Não autorizado');
        }
        return $response->withHeader('Content-type', 'application/json');
    });


    $app->run();
} catch (\Exception $e) {

    echo   json_encode(["fail" => "FORAM ENCONTRADOS ERROS!", "type_error" => $e->getMessage()]);
}
