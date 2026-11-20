<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/database.php';

$app = AppFactory::create();

//Permite reibir cuerpos en formato JSON desde fetch()
$app->addBodyParsingMiddleware();

//Middleware permite el enrutamiento de Slim
$app->addRoutingMiddleware();

//Manejo de errores en formato JSON
$app->addErrorMiddleware(true, true, true);

//Habilitar respuestas a peticiones preflight OPTIONS (CORS)
$app->options('/{routes:.+}', function(Request $request, Response $response){
return $response;
});

//Middleware global CORS (en Slim 4 el último add() se ejecuta primero)
$app->add(function (Request $request, RequestHandler $handler): Response {
    $response = $handler->handle($request);
    return $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

//Ruta de prueba del microservicio
$app->get('/ping', function (Request $request, Response $response) {
    $payload = json_encode([
        'status' => 'ok',
        'mensaje' => 'ms-auth activo en puerto 8001'
    ]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
