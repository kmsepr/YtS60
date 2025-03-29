<?php
$idstream = $_GET["id"] ?? '';

// Validate Video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("Invalid video ID");
}

$safe_idstream = escapeshellcmd($idstream);

// Kill Old Stream if Running
$existpid = shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'");
if (!empty($existpid)) {
    exec("kill $existpid");
}

// Start RTSP Streaming Process
$command = "/usr/bin/nohup yt-dlp -f 'best[ext=mp4]' https://www.youtube.com/watch?v=$safe_idstream -o - | " .
    "ffmpeg -re -i pipe:0 -c:v libx264 -preset ultrafast -crf 18 -c:a aac -b:a 128k " .
    "-f rtsp rtsp://tv.tg-gw.com/$safe_idstream " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Wait for Stream
sleep(5);
$newpid = shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'");
if (!$newpid) {
    die("Failed to start stream. Check logs: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
}

// Display RTSP Stream Links
echo "<h3>Stream Started</h3>";
echo "<a href='rtsp://tv.tg-gw.com:554/$safe_idstream'>RTSP Link 1 (Port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$safe_idstream'>RTSP Link 2 (Port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$safe_idstream'>RTSP Link 3 (Port 8080)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$safe_idstream'>RTSP Link 4 (Port 8554)</a><br>";
?>