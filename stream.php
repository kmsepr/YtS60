<?php
// Check if a video ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No video ID provided.");
}

$idstream = $_GET['id'];

// Validate YouTube video ID (11 characters, alphanumeric + _-)
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(400);
    die("Invalid video ID.");
}

$safe_idstream = escapeshellcmd($idstream);

// Kill any old processes streaming this video
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Start RTSP streaming with lower quality for Symbian support
$command = "/usr/bin/nohup yt-dlp -f 'best[ext=mp4]' https://www.youtube.com/watch?v=$safe_idstream -o - | " .
    "ffmpeg -re -i pipe:0 -c:v libx264 -preset ultrafast -crf 28 -b:v 256k -r 15 -s 320x240 " .
    "-c:a aac -b:a 32k -ar 22050 -ac 1 " .
    "-f rtsp rtsp://tv.tg-gw.com/$safe_idstream " .
    ">/tmp/yt_dlpdebug_$safe_idstream.txt 2>&1 &";

exec($command);

// Wait 5 seconds to check if stream started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start stream. Check logs: <pre>" . file_get_contents("/tmp/yt_dlpdebug_$safe_idstream.txt") . "</pre>");
}

// Output RTSP stream links
echo "<h3>Stream Started</h3>";
echo "<a href='rtsp://tv.tg-gw.com:554/$safe_idstream'>Watch (Port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$safe_idstream'>Watch (Port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$safe_idstream'>Watch (Port 8080)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$safe_idstream'>Watch (Port 8554)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>