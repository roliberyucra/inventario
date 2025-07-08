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
?>
<!--
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
    .info {
      margin-bottom: 10px;
    }
    .info span {
      font-weight: bold;
    }
    .motivo {
      margin: 20px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 8px;
      text-align: center;
    }
    .firmas {
      display: flex;
      justify-content: space-between;
      margin-top: 60px;
    }
    .firmas div {
      text-align: center;
      width: 45%;
    }
    .fecha {
      text-align: right;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <h2>Papeleta de Rotación de Bienes</h2>

  <div class="info"><span>ENTIDAD : </span>DIRECCION REGIONAL DE EDUCACION - AYACUCHO</div>
  <div class="info"><span>AREA : </span>OFICINA DE ADMINISTRACIÓN</div>
  <div class="info"><span>ORIGEN : </span><?php echo $respuesta->amb_origen->codigo. " - " . $respuesta->amb_origen->detalle; ?></div>
  <div class="info"><span>DESTINO : </span><?php echo $respuesta->amb_destino->codigo. " - " .$respuesta->amb_destino->detalle; ?></div>
  <div class="motivo"><strong>MOTIVO (*) : </strong><?php echo $respuesta->movimiento->descripcion?></div>

  <table>
    <thead>
      <tr>
        <th>ITEM</th>
        <th>CODIGO PATRIMONIAL</th>
        <th>NOMBRE DEL BIEN</th>
        <th>MARCA</th>
        <th>COLOR</th>
        <th>MODELO</th>
        <th>ESTADO</th>
      </tr>
    </thead>
    <tbody>
        <?php
        $contador = 1;
        foreach ($respuesta->detalle as $bien) {
            echo "<tr>";
            echo "<td>". $contador ."</td>";
            echo "<td>". $bien->cod_patrimonial ."</td>";
            echo "<td>". $bien->denominacion ."</td>";
            echo "<td>". $bien->marca ."</td>";
            echo "<td>". $bien->color ."</td>";
            echo "<td>". $bien->modelo ."</td>";
            echo "<td>". $bien->estado ."</td>";
            echo"</tr>";
            $contador +=1;
        }
        ?>
    </tbody>
  </table>

  <?php
    $fechaMovimiento = new DateTime($respuesta->movimiento->fecha_registro);
    $formatter = new IntlDateFormatter(
      'es_ES',
      IntlDateFormatter::LONG,
      IntlDateFormatter::NONE,
      'America/Lima',
      IntlDateFormatter::GREGORIAN
    );
    $fechaFormateada = $formatter->format($fechaMovimiento);
  ?>

  <div class="fecha">
    Ayacucho, <?php echo $fechaFormateada; ?>
  </div>

  <div class="firmas">
    <div>
      -----------------------------------<br>
      ENTREGUÉ CONFORME
    </div>
    <div>
      ------------------------------<br>
      RECIBÍ CONFORME
    </div>
  </div>

</body>
</html>
-->
    <?php

    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    $pdf = new TCPDF();
}