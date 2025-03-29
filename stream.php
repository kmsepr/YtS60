<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube Video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("Invalid video ID.");
}

// Check if yt-dlp and ffmpeg exist
if (!file_exists("/usr/local/bin/yt-dlp") || !file_exists("/usr/bin/ffmpeg")) {
    die("Error: yt-dlp or ffmpeg not found.");
}

// Kill existing stream process
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$idstream'");
}

// Start RTSP Streaming Process (Low Bitrate)
$command = "/usr/bin/nohup yt-dlp -f 'best[ext=mp4]' https://www.youtube.com/watch?v=$idstream -o - | " .
    "ffmpeg -re -i pipe:0 -c:v libx264 -preset ultrafast -b:v 300k -maxrate 300k -bufsize 600k " .
    "-c:a aac -b:a 48k -ar 32000 -ac 1 " .
    "-f rtsp rtsp://tv.tg-gw.com/$idstream " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Check if streaming started
sleep(5);
$checkstream = shell_exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com/$idstream");
if (empty($checkstream)) {
    die("Failed to start stream. Check logs: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
}

// Output RTSP Links
echo "<h3>RTSP Stream Links</h3>";
echo "<a href='rtsp://tv.tg-gw.com:554/$idstream'>Watch (Port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$idstream'>Watch (Port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$idstream'>Watch (Port 8080)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$idstream'>Watch (Port 8554)</a><br>";
echo "<a href='index.php'>Back</a>";
?>