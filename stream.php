<?php
$idstream = $_GET["id"];
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { 
    echo "404"; 
    die(); 
}

// Kill old worker process
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
exec("kill $existpid");

// Start new stream
exec("/usr/bin/nohup /var/www/html/yt-dlp_linux https://www.youtube.com/watch?v=$idstream -o - | ffmpeg -re -i - -t 18000 -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp rtsp://127.0.0.1:8080/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &");

streamfound:

$checkstream = exec("ffprobe -show_streams -v quiet rtsp://127.0.0.1:8080/$idstream");
if (empty($checkstream)) {
    sleep(3);
    goto streamfound;
}

// Display RTSP links
echo "<a href='rtsp://tv.tg-gw.com:554/$idstream'>Watch (link 1)</a> *554 port<br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$idstream'>Watch (link 2)</a> *443 port, use if the first one doesn’t work<br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$idstream'>Watch (link 3)</a> *8080 port, use if the previous ones don’t work<br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$idstream'>Watch (link 4)</a> *8554, you can try this one too<br>";
echo "<a href='index.php'><font color=blue>Back</font></a>";
?>