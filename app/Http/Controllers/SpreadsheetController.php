<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class SpreadsheetController extends Controller
{

    public function index()
    {
        return view('spreadsheets.index');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file1' => 'required|mimes:xlsx,csv',
            'file2' => 'required|mimes:xlsx,csv',
        ]);

        $path1 = $request->file('file1')->store('spreadsheets');
        $path2 = $request->file('file2')->store('spreadsheets');

        return response()->json([
            'message' => 'Arquivos enviados com sucesso!',
            'file1' => $path1,
            'file2' => $path2
        ]);
    }

    public function readFile($filename)
    {
        $path = storage_path("app/spreadsheets/{$filename}");
    
        if (!file_exists($path)) {
            return response()->json(['error' => 'Arquivo não encontrado!'], 404);
        }
    
        // Define o número máximo de linhas a serem processadas
        HeadingRowFormatter::default('none'); // Mantém os cabeçalhos originais
        $rows = [];
    
        Excel::import(new class ($rows) implements \Maatwebsite\Excel\Concerns\ToCollection {
            public $data;
            public function __construct(&$data) { $this->data = &$data; }
            public function collection(\Illuminate\Support\Collection $rows) {
                $this->data = $rows->take(10000)->toArray(); // Limita para 5000 linhas
            }
        }, $path);
    
        return response()->json($rows);
    }

    public function compare(Request $request)
{
    $request->validate([
        'file1' => 'required|string',
        'file2' => 'required|string',
        'coluna1' => 'required|string',
        'coluna2' => 'required|string',
        'operador' => 'required|string'
    ]);

    $data1 = \Maatwebsite\Excel\Facades\Excel::toArray([], storage_path("app/spreadsheets/{$request->file1}"))[0];
    $data2 = \Maatwebsite\Excel\Facades\Excel::toArray([], storage_path("app/spreadsheets/{$request->file2}"))[0];

    $resultados = [];
    foreach ($data1 as $row1) {
        foreach ($data2 as $row2) {
            $valor1 = isset($row1[$request->coluna1]) ? trim($row1[$request->coluna1]) : null;
            $valor2 = isset($row2[$request->coluna2]) ? trim($row2[$request->coluna2]) : null;

            // Converte valores para número (se possível)
            $num1 = is_numeric($valor1) ? floatval($valor1) : null;
            $num2 = is_numeric($valor2) ? floatval($valor2) : null;

            // Evita erros ao tentar comparar valores não numéricos
            if ($num1 === null || $num2 === null) {
                continue; // Pula esta comparação
            }

            switch ($request->operador) {
                case 'igual':
                    if ($num1 == $num2) $resultados[] = [$num1, $num2];
                    break;
                case 'maior':
                    if ($num1 > $num2) $resultados[] = [$num1, $num2];
                    break;
                case 'menor':
                    if ($num1 < $num2) $resultados[] = [$num1, $num2];
                    break;
                case 'diferenca':
                    $resultados[] = [$num1, $num2, abs($num1 - $num2)];
                    break;
            }
        }
    }

    return response()->json($resultados);
}
}
