import os
import logging
import sys
from moviepy.editor import VideoFileClip
import whisper

# Configuração de logs
logging.basicConfig(
    filename="C:\\Projetos\\controle_estoque\\python\\video_processing.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

if 'FFMPEG_PATH' in os.environ:
    os.environ["PATH"] += os.pathsep + os.environ['FFMPEG_PATH']


def process_video(video_path, output_folder):
    video_path = os.path.normpath(video_path)
    output_folder = os.path.normpath(output_folder)
    
    logging.info(f"Iniciando processamento do vídeo: {video_path}")
    
    if not os.path.exists(video_path):
        logging.error(f"Arquivo não encontrado: {video_path}")
        raise FileNotFoundError(f"Arquivo não encontrado: {video_path}")
    
    try:
        model = whisper.load_model("base")
        logging.info("Modelo Whisper carregado com sucesso.")
    except Exception as e:
        logging.error(f"Erro ao carregar modelo Whisper: {str(e)}")
        raise
    
    try:
        clip = VideoFileClip(video_path)
        duration = clip.duration
        logging.info(f"Duração do vídeo: {duration} segundos")
    except Exception as e:
        logging.error(f"Erro ao carregar vídeo: {str(e)}")
        raise
    
    segment_length = 30  # segundos
    video_name = os.path.basename(video_path).split('.')[0]
    
    for i in range(0, int(duration), segment_length):
        start = i
        end = min(i + segment_length, duration)
        
        logging.info(f"Cortando segmento de {start} a {end} segundos.")
        
        try:
            segment = clip.subclip(start, end)
            segment_path = f"{output_folder}/{video_name}_segment_{i // segment_length}.mp4"
            segment.write_videofile(segment_path, codec="libx264")
            logging.info(f"Segmento salvo: {segment_path}")
        except Exception as e:
            logging.error(f"Erro ao salvar segmento {i // segment_length}: {str(e)}")
            continue
        
        try:
            logging.info(f"Gerando legendas para {segment_path}")
            logging.info(f"Entrada, Saida {video_path}{output_folder}")
            audio_text = model.transcribe(segment_path)["text"]
            
            subtitle_path = f"{output_folder}/{video_name}_segment_{i // segment_length}.srt"
            with open(subtitle_path, "w") as subtitle_file:
                subtitle_file.write(audio_text)
            
            logging.info(f"Legenda salva: {subtitle_path}")
        except Exception as e:
            logging.error(f"Erro ao gerar legendas para {segment_path}: {str(e)}")
            continue

    logging.info("Processamento finalizado com sucesso.",video_path, output_folder)


if __name__ == "__main__":
    video_path = sys.argv[1]
    output_folder = sys.argv[2]
    
    os.makedirs(output_folder, exist_ok=True)
    logging.info(f"Criando pasta de saída: {output_folder}")
    
    process_video(video_path, output_folder)
