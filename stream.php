<?php
$idstream = $_GET["id"] ?? '';
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { echo "Invalid ID"; die(); }

// Kill old FFmpeg process
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
exec("kill $existpid");

// Start new RTSP stream (Forcing TCP for better reliability)
exec("/usr/bin/nohup /var/www/html/yt-dlp_linux https://www.youtube.com/watch?v=$idstream -o - | \
    ffmpeg -rtsp_transport tcp -re -i - -t 18000 -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 128k -r 15 \
    -vf scale=320:240 -f rtsp rtsp://0.0.0.0:8554/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &");

streamfound:
$checkstream = exec("ffprobe -rtsp_transport tcp -show_streams -v quiet rtsp://127.0.0.1:8554/$idstream");
if (empty($checkstream)) {
    sleep(3);
    goto streamfound;
}

// Generate RTSP watch links
$koyeb_ip = "enthusiastic-edeline-kmsepr-0cbdd0dd.koyeb.app";
echo "<a href='rtsp://$koyeb_ip:8554/$idstream'>Watch (RTSP over TCP)</a><br>";
?>