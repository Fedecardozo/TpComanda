<?php

use Illuminate\Support\Arr;

    class Cuenta
    {
        public $nombre;
        public $precio;
        public $cantidad;
        public $precioTotal;

        public static function TraerCuentas($id_pedido)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT productos.nombre, 
            productos.precio,
            detalles.cantidad,
            (productos.precio*detalles.cantidad) AS 'precioTotal'
            FROM
            detalles
            JOIN
            productos ON detalles.id_producto = productos.id
            WHERE
            detalles.id_pedido = '$id_pedido';");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Cuenta");
        }

        public static function GetCuentaFinal($arrayCuentas)
        {
            $acu = 0;
            foreach ($arrayCuentas as $value) 
            {
                $acu += $value->precioTotal;
            }

            return $acu;
        }

    }

?>