<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoController extends Controller
{
    public function index()
    {
        return view('videos.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|mimetypes:video/mp4|max:1002400'
        ]);
    
        try {
            $videoPath = $request->file('video')->store('uploads', 'public');
            $fileName = basename($videoPath);
       
            // Executa o command assíncrono
            \Artisan::call('video:process', [
                'path' => $fileName
            ]);
    
            return back()->with('success', 'Vídeo enviado. Processamento iniciado!');
    
        } catch (\Exception $e) {
            \Log::error('Upload falhou: ' . $e->getMessage());
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }
}