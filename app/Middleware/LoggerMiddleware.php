<?php
use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;
    include_once "./BaseDatos/accesoDatos.php";


    class LoggerMiddleware
    {
        public function __invoke(Request $request, RequestHandler $handler)
        {
            $fecha = date('Y-m-d H:i:s');
            $idUsuario = $request->getAttribute('idUsuario');
            $metodo = $request->getMethod();
            $accion = $request->getUri()->getPath();

            
            // Continua al controller
            $response = $handler->handle($request);           

            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into logs (idUsuario,fecha,metodo,accion) values(:idUsuario,:fecha,:metodo,:accion)");
            $consulta->bindValue(":idUsuario",intval($idUsuario),PDO::PARAM_INT);
            $consulta->bindValue(":fecha",$fecha,PDO::PARAM_STR);
            $consulta->bindValue(":metodo",$metodo,PDO::PARAM_STR);
            $consulta->bindValue(":accion",$accion,PDO::PARAM_STR);
            $consulta->execute();

            return $response;

        }
    }


?>