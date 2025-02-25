<?php

namespace App\Http\Controllers;

use App\Jobs\ImportSpreadsheetJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\Storage;

class SpreadsheetController extends Controller
{
    public function index()
    {
        return view('spreadsheets.index');
    }

    // Função para upload dos arquivos
    public function upload(Request $request)
    {
        $request->validate([
            'file1' => 'required|mimes:xlsx,csv',
            'file2' => 'required|mimes:xlsx,csv',
        ]);

        // Salva os arquivos no storage
        $path1 = $request->file('file1')->store('spreadsheets');
        $path2 = $request->file('file2')->store('spreadsheets');

        ImportSpreadsheetJob::dispatch($path1, 'file1');
        ImportSpreadsheetJob::dispatch($path2, 'file2');

        return response()->json([
            'message' => 'Arquivos enviados e processamento iniciado!',
            'file1' => $path1,
            'file2' => $path2
        ]);
    }

    // Função para comparar as planilhas
    public function compare(Request $request)
    {
        $request->validate([
            'file1' => 'required|string',
            'file2' => 'required|string',
            'coluna1' => 'required|string',
            'coluna2' => 'required|string',
            'operador' => 'required|string'
        ]);

        // Carrega os dados das planilhas
        $data1 = Excel::toArray([], storage_path("app/spreadsheets/{$request->file1}"))[0];
        $data2 = Excel::toArray([], storage_path("app/spreadsheets/{$request->file2}"))[0];

        // Faz a comparação dos dados
        $resultados = $this->compareData($data1, $data2, $request->coluna1, $request->coluna2, $request->operador);

        return response()->json($resultados);
    }

    // Função para comparar os dados das planilhas
    private function compareData($data1, $data2, $coluna1, $coluna2, $operador)
    {
        $resultados = [];

        foreach ($data1 as $row1) {
            foreach ($data2 as $row2) {
                $valor1 = isset($row1[$coluna1]) ? trim($row1[$coluna1]) : null;
                $valor2 = isset($row2[$coluna2]) ? trim($row2[$coluna2]) : null;

                $num1 = is_numeric($valor1) ? floatval($valor1) : null;
                $num2 = is_numeric($valor2) ? floatval($valor2) : null;

                if ($num1 === null || $num2 === null) {
                    continue;
                }

                switch ($operador) {
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

        return $resultados;
    }

    // Função para ler e retornar dados de um arquivo
    public function readFile($filename)
    {
        $path = storage_path("app/spreadsheets/{$filename}");
    
        if (!file_exists($path)) {
            return response()->json(['error' => 'Arquivo não encontrado!'], 404);
        }
    
        HeadingRowFormatter::default('none');
        $rows = [];
    
        Excel::import(new class ($rows) implements \Maatwebsite\Excel\Concerns\ToCollection {
            public $data;
            public function __construct(&$data) { $this->data = &$data; }
            public function collection(\Illuminate\Support\Collection $rows) {
                $this->data = $rows->take(10000)->toArray(); // Limita para 10000 linhas
            }
        }, $path);
    
        return response()->json($rows);
    }
}
