<?php
$idstream = $_GET["id"] ?? '';

// Validate YouTube video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid stream ID");
}

$safe_idstream = escapeshellcmd($idstream);

// Kill old worker
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Start new stream (Convert YouTube to HLS)
$yt_dlp_path = "/usr/local/bin/yt-dlp";
$ffmpeg_command = "/usr/bin/nohup $yt_dlp_path -f best -o - https://www.youtube.com/watch?v=$safe_idstream " .
    "| ffmpeg -re -i - -c:v libx264 -preset ultrafast -crf 18 -c:a aac -b:a 128k " .
    "-f hls -hls_time 5 -hls_list_size 10 /var/www/html/streams/$safe_idstream.m3u8 " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($ffmpeg_command);

// Wait and check if process started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start streaming. Check logs at /tmp/yt_dlpdebug.txt");
}

// Output stream link (HLS)
echo "<a href='streams/$idstream.m3u8'>Watch Stream (HLS)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>