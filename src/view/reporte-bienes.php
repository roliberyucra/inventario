<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("yp")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle("hoja 1");
$activeWorksheet->setCellValue('A1', 'Hola mundo');
$activeWorksheet->setCellValue('A2', 'DNI');
$activeWorksheet->setCellValue('B2', '74404980');
for ($i = 1; $i <= 10; $i++) {
    // Crear el texto de la operación: "1 x i = resultado"
    $texto = "1 x $i = " . (1 * $i);

    // Escribir en la columna A, fila $i (A1, A2, ..., A10)
    $activeWorksheet->setCellValue('A' . $i, $texto);
}

for ($i = 1; $i <= 30; $i++) {
    // Convertir número de columna (1-10) a letra (A-J)
    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $activeWorksheet->setCellValue($columna . '1', $i);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Bienes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;