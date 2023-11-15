<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Psr\Http\Server\RequestHandlerInterface;

require __DIR__ . '/../vendor/autoload.php';

require_once './Middleware/SetTimeMiddleware.php';
require_once './Middleware/ValidarMiddleware.php';
require_once './Middleware/AuthMiddleware.php';
require_once './BaseDatos/AccesoDatos.php';
require_once './Controller/UsuarioController.php';
require_once './Controller/ProductoController.php';
require_once './Controller/MesaController.php';
require_once './Controller/PedidoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
// $app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//Add zona horaria
$app->add(new SetTimeMiddleware());

$app->add(\ValidarMiddleware::class. ':ReturnContentJson');

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(\ValidarMiddleware::class. ':IssetParametrosUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->post('[/]', \ProductoController::class . ':CargarUno')
    ->add(\ValidarMiddleware::class. ':IssetParametrosProducto');;
});

$app->group('/mesas', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->post('[/]', \MesaController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/listarPendientes', \PedidoController::class . ':ListarPedidosPendientes')
    ->add(\AuthMiddleware::class. ':VerificarSectorPreparacion');//1
    
    $group->post('[/]', \PedidoController::class . ':CargarUno')
    ->add(\ValidarMiddleware::class. ':VerificarParametrosPedido') //3
    ->add(\ValidarMiddleware::class. ':IssetParametrosPedido') //2
    ->add(\AuthMiddleware::class. ':VerificarMozo'); //1
    
    $group->post('/agregarFoto', \PedidoController::class . ':AgregarUnaFoto')
    ->add(\AuthMiddleware::class. ':VerificarMozo');

    $group->put('/cambiarEstado', \PedidoController::class . ':CambiarEstadoDetalle')
    ->add(\ValidarMiddleware::class. ':ValidarEstadoPedido') //3
    ->add(\ValidarMiddleware::class. ':ValidarUpdateDetalles') //2
    ->add(\AuthMiddleware::class. ':VerificarSectorPreparacion');//1

    $group->put('/agregarDuracion', \PedidoController::class . ':AsignarDuracion')
    ->add(\ValidarMiddleware::class. ':ValidarDuracion')//3
    ->add(\ValidarMiddleware::class. ':ValidarUpdateDetalles') //2
    ->add(\AuthMiddleware::class. ':VerificarSectorPreparacion');//1
});

// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) 
{
    $group->post('/login', \UsuarioController::class . ':CrearToken')
    ->add(\AuthMiddleware::class. ':VerificarLogin');

});

$app->run();
