<?php
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once './vendor/autoload.php';
require_once './vendor/tecnickcom/tcpdf/tcpdf.php';

// Verifica que la sesión esté activa
if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['sesion_token'])) {
    die("Sesión no iniciada");
}

// Llama al backend para obtener los datos de instituciones
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Institucion.php?tipo=buscar_instituciones&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "GET",
));
$response = curl_exec($curl);
curl_close($curl);

$respuesta = json_decode($response);

// Validar respuesta
if (!isset($respuesta->instituciones) || !is_array($respuesta->instituciones)) {
    ob_end_clean();
    echo "Error al obtener las instituciones.";
    echo "<pre>"; print_r($respuesta); echo "</pre>";
    exit;
}


// Configuración del PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('SIGI');
$pdf->SetAuthor('SIGI');
$pdf->SetTitle('Reporte de Instituciones');
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage();


// Título
$contenido_pdf = '<h2 style="text-align:center;">Reporte de Instituciones</h2>';

// Tabla
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Instituciones</title>
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

  <h2><i>Reporte de instituciones</i></h2>
<br>
<br>

  <table border="1">
    <thead>
    <tr>
      <th><b>N°</b></th>
      <th><b>CODIGO MODULAR</b></th>
      <th><b>ENCARGADO</b></th>
      <th><b>CÓDIGO</b></th>
      <th><b>BENEFICIARIO</b></th>
    </tr>
  </thead>
    <tbody>';

$contador = 1;
foreach ($respuesta->instituciones as $institucion) {
    $html .= '<tr>';
    $html .= '<td>'.$contador++.'</td>';
    $html .= '<td>'.htmlspecialchars($institucion->cod_modular).'</td>';
    $html .= '<td>'.htmlspecialchars($institucion->ruc).'</td>';
    $html .= '<td>'.htmlspecialchars($institucion->nombre).'</td>';
    $html .= '<td>'.htmlspecialchars($institucion->beneficiario_nombre).'</td>';
    $html .= '</tr>';
}
$html .= '
  </tbody>
</table>';

$contenido_pdf .= '</tbody></table>';

// Imprimir
$pdf->writeHTML($html, true, false, true, false, '');

ob_clean(); // Limpia la salida previa
$pdf->Output('Reporte_Instituciones.pdf', 'I');
