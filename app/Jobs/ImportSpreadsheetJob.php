<?php

namespace App\Jobs;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportSpreadsheetJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $filePath;
    public $jobType; // Indica se é a primeira ou segunda planilha

    // Construtor para passar o caminho do arquivo e o tipo de job
    public function __construct($filePath, $jobType)
    {
        $this->filePath = $filePath;
        $this->jobType = $jobType;
    }

    public function handle()
    {
        // Importe a planilha de acordo com o tipo
        Excel::import(new class($this->filePath, $this->jobType) implements WithChunkReading {
            public $filePath;
            public $jobType;

            public function __construct($filePath, $jobType)
            {
                $this->filePath = $filePath;
                $this->jobType = $jobType;
            }

            // Define o tamanho do chunk
            public function chunkSize(): int
            {
                return 1000; // Tamanho do chunk de dados
            }

            // O que fazer com os dados após importados
            public function model(array $rows)
            {
                // Aqui, podemos manipular os dados ou salvar no banco
                // Exemplo: \App\Models\Planilha::insert($rows);
            }
        }, $this->filePath);
    }
}
