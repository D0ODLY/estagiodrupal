<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$iutFileName = 'input.xlsx';


$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($inputFileName);


$sheet = $spreadsheet->getActiveSheet();


$data = $sheet->toArray();


$transformedData = array_map(null, ...$data);


$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();


$newSheet->fromArray($transformedData);


$outputFileName = 'output.xlsx';
$writer = new Xlsx($newSpreadsheet);
$writer->save($outputFileName);

echo "Ficheiro transformado guardado como $outputFileName";
?>