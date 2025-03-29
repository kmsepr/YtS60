<?php
$idstream = $_GET["id"] ?? "";
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { 
    die("Invalid video ID"); 
}

// Kill old FFmpeg process (if any)
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$idstream'");
}

// Start new RTSP stream
$command = "/usr/bin/nohup yt-dlp -f 'best' https://www.youtube.com/watch?v=$idstream -o - " .
    "| ffmpeg -re -i - -t 18000 -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 " .
    "-f rtsp rtsp://tv.tg-gw.com/$idstream " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Wait for stream to start
sleep(5);
$checkstream = exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com/$idstream");
if (empty($checkstream)) {
    sleep(3);
    $checkstream = exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com/$idstream");
    if (empty($checkstream)) {
        die("Failed to start stream. Check logs: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
    }
}

// Output RTSP links
echo "<h3>Stream Started</h3>";
echo "<a href='rtsp://tv.tg-gw.com:554/$idstream'>Watch (Port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$idstream'>Watch (Port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$idstream'>Watch (Port 8080)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$idstream'>Watch (Port 8554)</a><br>";
?>