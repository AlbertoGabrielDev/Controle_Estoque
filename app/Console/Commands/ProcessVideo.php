<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class ProcessVideo extends Command
{
    protected $signature = 'video:process {path}';
    protected $description = 'Processa vídeo usando Whisper e MoviePy';

    public function handle()
    {
        $fileName = $this->argument('path');

        $videoPath = storage_path("app/public/uploads/{$fileName}");
        $outputFolder = storage_path("app/public/processed");

        // Validação reforçada
        if (!file_exists($videoPath)) {
            $this->error("Arquivo não encontrado: {$videoPath}");
            return 1;
        }

        // Garante diretório de saída
        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0755, true);
        }

        $pythonPath = env('PYTHON_PATH') ?: 'C:\Users\alber\AppData\Local\Programs\Python\Python312\python.exe';

        $process = new Process([
            $pythonPath,
            base_path('python/video.py'),
            $videoPath,
            $outputFolder
        ]);

        $process->run();

        // Configurações essenciais
        $process->setTimeout(3600);
        $process->setEnv([
            'PATH' => getenv('PATH') . ';' . env('FFMPEG_PATH')
        ]);
        \Log::info("Iniciando processamento para: " . $this->argument('path'));
        try {
            $process->mustRun();
            \Log::info("Processamento concluído: " . $process->getOutput());
            return 0;
        } catch (ProcessFailedException $e) {
            $this->error("Erro técnico completo:");
            $this->line("Comando executado: " . $process->getCommandLine());
            $this->line("Saída do sistema: " . $process->getErrorOutput());
            return 1;
        }
    }
}
