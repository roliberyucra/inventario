<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once './vendor/autoload.php';
require_once './vendor/tecnickcom/tcpdf/tcpdf.php';

// Verifica que la sesión esté activa
if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['sesion_token'])) {
    die("Sesión no iniciada");
}

// Hacer la solicitud cURL al backend
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER."src/control/Ambiente.php?tipo=buscar_ambientes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "GET",
));
$response = curl_exec($curl);
curl_close($curl);

$respuesta = json_decode($response);

// Validar respuesta
if (!isset($respuesta->ambientes) || !is_array($respuesta->ambientes)) {
    ob_clean();
    die('No se pudieron obtener los ambientes.');
}


// Crear PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('SIGI');
$pdf->SetAuthor('SIGI');
$pdf->SetTitle('Reporte de Ambientes');
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage();

// Título
$html = '<h2 style="text-align:center;">Reporte de Ambientes</h2>';

// Tabla
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Ambientes</title>
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

  <h2><i>Reporte de ambientes</i></h2>
<br>
<br>

  <table border="1">
    <thead>
    <tr>
      <th><b>N°</b></th>
      <th><b>INSTITUCIÓN</b></th>
      <th><b>ENCARGADO</b></th>
      <th><b>CÓDIGO</b></th>
      <th><b>DETALLE</b></th>
      <th><b>OTROS DETALLES</b></th>
    </tr>
  </thead>
    <tbody>';


$contador = 1;
foreach ($respuesta->ambientes as $ambiente) {
    $html .= '<tr>';
    $html .= '<td>'.$contador++.'</td>';
    $html .= '<td>'.htmlspecialchars($ambiente->nombre_institucion).'</td>';
    $html .= '<td>'.htmlspecialchars($ambiente->encargado).'</td>';
    $html .= '<td>'.htmlspecialchars($ambiente->codigo).'</td>';
    $html .= '<td>'.htmlspecialchars($ambiente->detalle).'</td>';
    $html .= '<td>'.htmlspecialchars($ambiente->otros_detalle).'</td>';
    $html .= '</tr>';
}
$html .= '
  </tbody>
</table>';


$html .= '</tbody></table>';

// Imprimir contenido
$pdf->writeHTML($html, true, false, true, false, '');

ob_clean(); // Limpia la salida previa
// Descargar
$pdf->Output('Reporte_Ambientes.pdf', 'I'); // 'I' muestra en navegador, 'D' descarga directamente
