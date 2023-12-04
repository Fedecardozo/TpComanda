<?php

    require_once "./Objetos/Producto.php";

    class FilesController
    {
        
        public static function CargarCsv($request, $response,$args)
        {
            $csv = $request->getAttribute('archivo');
            $destino = $request->getAttribute('destino');
            $csv->moveTo($destino);
            Producto::CargarCsv($destino);

            $payload = json_encode(array("mensaje" => 'Se cargo exitosamente'));
            $response->getBody()->write($payload);

            return $response;

        }

        public static function DescargarCsv($request,$response,$args)
        {
            $archivo_a_descargar = "./ArchivoCsv/productos.csv";
            Producto::ConvertirCsv($archivo_a_descargar);
            // Verificar si el archivo existe
            if (file_exists($archivo_a_descargar)) 
            {
                // Establecer los encabezados para la descarga
                header('Content-Description: File transfer');
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="' . basename($archivo_a_descargar) . '"');
                header('Content-Length: ' . filesize($archivo_a_descargar));

                // Lee el archivo y lo envía al navegador
                readfile($archivo_a_descargar);
                $msj = "Descargado...";
                exit;
            } 
            else 
            {
                // El archivo no existe
                $msj = 'El archivo no existe';
            }

            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);

            return $response;

        }

        public static function DescargarPdf($request,$response,$args)
        {
            $archivo_a_descargar = "./ArchivoPdf/logoComanda.pdf";
            // Verificar si el archivo existe
            if (file_exists($archivo_a_descargar)) 
            {
                // Establecer los encabezados para la descarga
                header('Content-Description: File transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($archivo_a_descargar) . '"');
                header('Content-Length: ' . filesize($archivo_a_descargar));

                // Lee el archivo y lo envía al navegador
                readfile($archivo_a_descargar);
                $msj = "Descargado...";
                exit;
            } 
            else 
            {
                // El archivo no existe
                $msj = 'El archivo no existe';
            }

            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);

            return $response;

        }

    }


?>