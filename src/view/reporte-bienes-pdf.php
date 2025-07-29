<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once './vendor/autoload.php';
require_once './vendor/tecnickcom/tcpdf/tcpdf.php';

// Verifica sesión
if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['sesion_token'])) {
    die("Sesión no iniciada.");
}

// Obtener datos desde el backend
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=buscar_bienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "GET",
));
$response = curl_exec($curl);
curl_close($curl);

$respuesta = json_decode($response);

// Validar respuesta
if (!isset($respuesta->bienes) || !is_array($respuesta->bienes)) {
    echo "Error al obtener los bienes.";
    echo "<pre>"; print_r($respuesta); echo "</pre>";
    exit;
}

// Crear PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('SIGI');
$pdf->SetAuthor('SIGI');
$pdf->SetTitle('Reporte de Bienes');
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage();

// Título
$contenido_pdf = '<h2 style="text-align:center;">Reporte de Bienes</h2>';

// Tabla
$contenido_pdf = '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Bienes</title>
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

<table style="width:100%; border: none;">
  <tr>
    <td style="width:20%; text-align:left; border:none;">
      <img src="./src/view/pp/assets/images/gob.jpg" width="60">
    </td>
    <td style="width:60%; text-align:center; border:none;">
      <h4 style="margin:0;">GOBIERNO REGIONAL DE AYACUCHO</h4>
      <h2 style="margin:0;">DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO</h2>
      <h4 style="margin:0;">DIRECCIÓN DE ADMINISTRACIÓN</h4>
    </td>
    <td style="width:20%; text-align:right; border:none;">
      <img src="./src/view/pp/assets/images/drea.jpg" width="60">
    </td>
  </tr>
</table>

<br>
<br>
<h2 style="margin:0;">Reporte de Bienes</h2>
<br>
<br>

<p>ENTIDAD <span class="subrayado">: DIRECCION REGIONAL DE EDUCACION - AYACUCHO</span></p>
  <p>ÁREA <span class="subrayado">: OFICINA DE ADMINISTRACIÓN</span></p>

  <table border="1">
    <thead>
    <tr>
      <th><b>N°</b></th>
      <th><b>CODIGO PATROMONIAL</b></th>
      <th><b>DENOMINACION</b></th>
      <th><b>MARCA</b></th>
      <th><b>MODELO</b></th>
      <th><b>COLOR</b></th>
      <th><b>ESTADO</b></th>
      <th><b>AMBIENTE</b></th>
    </tr>
  </thead>
    <tbody>';

$contador = 1;
foreach ($respuesta->bienes as $bien) {
    $contenido_pdf .= '<tr>';
    $contenido_pdf .= '<td>'.$contador++.'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->cod_patrimonial).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->denominacion).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->marca).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->modelo).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->color).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->estado_conservacion).'</td>';
    $contenido_pdf .= '<td>'.htmlspecialchars($bien->ambiente_detalle).'</td>';
    $contenido_pdf .= '</tr>';
}

$contenido_pdf .= '
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
  </tbody>
</table>';

// Renderizar en PDF
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

ob_clean(); // Limpia la salida previa
$pdf->Output('Reporte_Bienes.pdf', 'I');
