<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Caminho para o ficheiro Excel de entrada
$inputFileName = 'input.xlsx';

// Carregar o ficheiro Excel de entrada
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($inputFileName);

// Selecionar a primeira folha
$sheet = $spreadsheet->getActiveSheet();

// Ler os dados da folha
$data = $sheet->toArray();

// Transformar os dados (Exemplo: inverter linhas e colunas)
$transformedData = array_map(null, ...$data);

// Criar um novo Spreadsheet
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();

// Escrever os dados transformados na nova folha
$newSheet->fromArray($transformedData);

// Guardar o novo ficheiro Excel
$outputFileName = 'output.xlsx';
$writer = new Xlsx($newSpreadsheet);
$writer->save($outputFileName);

echo "Ficheiro transformado guardado como $outputFileName";
?>