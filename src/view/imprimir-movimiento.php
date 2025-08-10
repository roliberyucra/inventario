<?php

$ruta = explode ("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta == "") {
    header("location:".BASE_URL."movimientos");
}

$curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'] . "&data=". $ruta[1], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
        CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
        CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
        CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
        CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
        CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ), //configura las cabeceras enviadas al servicio
    )); //curl_setopt_array configura las opciones para una transferencia cURL

    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
        $respuesta = json_decode($response);
        //print_r($respuesta);

        $contenido_pdf = '';
        $contenido_pdf .= '
        
        <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Papeleta de Rotación de Bienes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }


    h2 {
      text-align: center;
      text-transform: uppercase;
    }


    p {
      margin: 8px 0;
    }


    .subrayado {
      display: inline-block;
      min-width: 200px;
    }


    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }


    table, th, td {
      border: 1px solid black;
    }


    th, td {
      padding: 8px;
      text-align: center;
    }


    .firmas {
      margin-top: 60px;
      display: flex;
      justify-content: space-between;
      padding: 0 50px;
    }


    .firma {
      text-align: center;
    }


    .firma-linea {
      margin-bottom: 5px;
    }


    .footer-fecha {
      margin-top: 40px;
      text-align: right;
      padding-right: 40px;
    }


    .motivo {
      font-weight: bold;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<br>
<br>

  <h2><i>Papeleta de Rotación de Bienes</i></h2>
<br>
<br>

  <p>ENTIDAD <span class="subrayado">: DIRECCION REGIONAL DE EDUCACION - AYACUCHO</span></p>
  <p>ÁREA <span class="subrayado">: OFICINA DE ADMINISTRACIÓN</span></p>
  <p>ORIGEN <span class="subrayado">: '. $respuesta->amb_origen->codigo.' - '.$respuesta->amb_origen->detalle.'</span></p>
  <p>DESTINO <span class="subrayado">: '. $respuesta->amb_destino->codigo. ' - '. $respuesta->amb_destino->detalle .'</span></p>


  <p class="motivo">MOTIVO (*): <span class="subrayado">'. $respuesta->movimiento->descripcion.'</span></p>


  <table border="1">
    <thead>
      <tr>
        <th><b>ITEM</b></th>
        <th><b>CÓDIGO PATRIMONIAL</b></th>
        <th><b>NOMBRE DEL BIEN</b></th>
        <th><b>MARCA</b></th>
        <th><b>COLOR</b></th>
        <th><b>MODELO</b></th>
        <th><b>ESTADO</b></th>
      </tr>
    </thead>
    <tbody>
        
        ';
     
        $contador = 1;
        foreach ($respuesta->detalle as $bien) {
            $contenido_pdf .= "<tr>";
            $contenido_pdf .= "<td>".$contador."</td>";
            $contenido_pdf .= "<td>".$bien->cod_patrimonial."</td>";
            $contenido_pdf .= "<td>".$bien->denominacion."</td>";
            $contenido_pdf .= "<td>".$bien->marca."</td>";
            $contenido_pdf .= "<td>".$bien->color."</td>";
            $contenido_pdf .= "<td>".$bien->modelo."</td>";
            $contenido_pdf .= "<td>".$bien->estado_conservacion."</td>";
            $contenido_pdf .= "</tr>";
            $contador +=1;
        }
$contenido_pdf .= '


    </tbody>
  </table>


  <p style="text-align: right; padding-right: 40px;">
  Ayacucho,_____de____2025
</p>


  <div class="firmas">
    <div class="firma">
      <div class="firma-linea">------------------------------</div>
      <div>ENTREGUE CONFORME</div>
    </div>
    <div class="firma">
      <div class="firma-linea">------------------------------</div>
      <div>RECIBÍ CONFORME</div>
    </div>
  </div>


</body>
</html>


';

    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    class MYPDF extends TCPDF {

      //Page header
      public function Header() {
        // Logo
        
        $this->Image('./src/view/pp/assets/images/gob.jpg', 10, 10, 22, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Image('./src/view/pp/assets/images/drea.jpg', 180, 10, 22, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 10);
        // Title
        $this->Cell(0, 15, 'GOBIERNO REGIONAL DE AYACUCHO', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);
        $this->SetFont('helvetica', 'B', 13);
        $this->Cell(0, 15, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 15, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      }
    
      // Page footer
      public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Pág. '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
      }
    }
    
    // create new PDF document
    $pdf = new MYPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Roliber Yucra');
    $pdf->SetTitle('Reporte de movimientos');


    //asignar márgenes
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //Salto de página automático
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


    //Va por secciones si quieres utilizar distintas fuentes(tipo de fuente, grosor, tamaño de la letra)
    $pdf->SetFont('courier', '', 9);
    // add a page
    //Añadir nueva página (tiene muchas propiedades, pero le dejamos en vacío para que estén por defecto)
    $pdf->AddPage();


    // output the HTML content
    //Generar el contenido html
    $pdf->writeHTML($contenido_pdf);


    //Close and output PDF document
    $pdf->Output('reporte de movimientos.pdf', 'I');
}