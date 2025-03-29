<?php
$idstream = $_GET["id"] ?? '';
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { echo "Invalid ID"; die(); }

// Kill old FFmpeg process
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
exec("kill $existpid");

// Start new RTSP stream publishing to your RTSP server
exec("/usr/bin/nohup /var/www/html/yt-dlp_linux https://www.youtube.com/watch?v=$idstream -o - | \
    ffmpeg -re -i - -t 18000 -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 128k -r 15 \
    -vf scale=320:240 -f rtsp rtsp://tv.tg-gw.com/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &");

sleep(3); // Give FFmpeg time to start

echo "<a href='rtsp://tv.tg-gw.com/$idstream'>Watch (RTSP)</a><br>";
?>