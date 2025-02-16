import os
if 'FFMPEG_PATH' in os.environ:
    os.environ["PATH"] += os.pathsep + os.environ['FFMPEG_PATH']
from moviepy.editor import VideoFileClip
import whisper
import sys

def process_video(video_path, output_folder):
    video_path = os.path.normpath(video_path)
    output_folder = os.path.normpath(output_folder)

    print(f"Processando vídeo: {video_path}")
    if not os.path.exists(video_path):
        raise FileNotFoundError(f"Arquivo não encontrado: {video_path}")
    model = whisper.load_model("base")
    
    # Carrega o vídeo
    clip = VideoFileClip(video_path)
    duration = clip.duration
    print(f"Duração do vídeo: {duration} segundos")

    segment_length = 30  # segundos
    video_name = os.path.basename(video_path).split('.')[0]
    
    for i in range(0, int(duration), segment_length):
        start = i
        end = min(i + segment_length, duration)
        
        print(f"Cortando segmento de {start} a {end} segundos.")
        segment = clip.subclip(start, end)
        segment_path = f"{output_folder}/{video_name}_segment_{i // segment_length}.mp4"
        segment.write_videofile(segment_path, codec="libx264")
        
        # Gera legendas com Whisper
        print(f"Gerando legendas para {segment_path}")
        audio_text = model.transcribe(segment_path)["text"]
        
        # Salva a legenda em um arquivo de texto
        subtitle_path = f"{output_folder}/{video_name}_segment_{i // segment_length}.srt"
        with open(subtitle_path, "w") as subtitle_file:
            subtitle_file.write(audio_text)
        
        print(f"Segmento processado: {segment_path}")

if __name__ == "__main__":
    video_path = sys.argv[1]
    output_folder = sys.argv[2]
    os.makedirs(output_folder, exist_ok=True)
    process_video(video_path, output_folder)