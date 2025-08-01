<?php
require './vendor/autoload.php';

    $curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=buscar_bienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
        CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
        CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
        CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
        CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
        CURLOPT_CUSTOMREQUEST => "GET",// el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
    )); //curl_setopt_array configura las opciones para una transferencia cURL

    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
        $respuesta = json_decode($response);
    }
    if (!isset($respuesta->bienes) || !is_array($respuesta->bienes)) {
    echo "Error al obtener los bienes.";
    echo "<pre>"; print_r($respuesta); echo "</pre>";
    exit;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet ->getProperties()->setCreator("yo")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle("hoja 1");

$fila = 1;
$headers = ['N°','Descripción', 'Ambiente', 'Código patrimonial', 'Denominación', 'Marca', 'Modelo', 'Tipo', 'Color', 'Serie', 'Dimensiones', 'Valor', 'Situación', 'Estado de conservación', 'Observaciones', 'Fecha de registro', 'Usuario registrante'];

$colIndex = 'A';
foreach ($headers as $header) {
    $activeWorksheet->setCellValue($colIndex.$fila, $header);
    $colIndex++;
}
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Rango de los encabezados, por ejemplo A1:H1 (ajústalo según la cantidad de columnas que tengas)
$encabezadoRange = 'A1:Q1';

// Aplicar estilos al encabezado
$styleArray = [
    'font' => [
        'bold' => true,
        'size' => 12,
        'name' => 'Arial'
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'argb' => 'FFD9E1F2', // color de fondo (opcional)
        ],
    ],
];

$activeWorksheet->getStyle($encabezadoRange)->applyFromArray($styleArray);

// También puedes hacer que las columnas se ajusten automáticamente:
foreach (range('A', 'Q') as $col) {
    $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
}

$fila = 2;
$contador = 1;
foreach ($respuesta->bienes as $bien) {
    $activeWorksheet->setCellValue('A'.$fila, $contador);
    $activeWorksheet->setCellValue('B'.$fila, $bien->ingreso_detalle);
    $activeWorksheet->setCellValue('C'.$fila, $bien->ambiente_detalle);
    $activeWorksheet->setCellValue('D'.$fila, $bien->cod_patrimonial);
    $activeWorksheet->setCellValue('E'.$fila, $bien->denominacion);
    $activeWorksheet->setCellValue('F'.$fila, $bien->marca);
    $activeWorksheet->setCellValue('G'.$fila, $bien->modelo);
    $activeWorksheet->setCellValue('H'.$fila, $bien->tipo);
    $activeWorksheet->setCellValue('I'.$fila, $bien->color);
    $activeWorksheet->setCellValue('J'.$fila, $bien->serie);
    $activeWorksheet->setCellValue('K'.$fila, $bien->dimensiones);
    $activeWorksheet->setCellValue('L'.$fila, $bien->valor);
    $activeWorksheet->setCellValue('M'.$fila, $bien->situacion);
    $activeWorksheet->setCellValue('N'.$fila, $bien->estado_conservacion);
    $activeWorksheet->setCellValue('O'.$fila, $bien->observaciones);
    $activeWorksheet->setCellValue('P'.$fila, $bien->fecha_registro);
    $activeWorksheet->setCellValue('Q'.$fila, $bien->usuario_registrante);
    $contador++;
    $fila++;
}
foreach (range('A', 'Q') as $col) {
    $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
}

ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Bienes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
