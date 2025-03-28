<?php
$idstream = $_GET["id"] ?? '';

// Validate YouTube video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid stream ID");
}

$safe_idstream = escapeshellcmd($idstream);
$streams_dir = "/var/www/html/streams";
$stream_path = "$streams_dir/$safe_idstream.m3u8";

// Ensure the streams directory exists
if (!is_dir($streams_dir)) {
    mkdir($streams_dir, 0777, true);
}

// Kill old worker
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Debug yt-dlp existence
if (!file_exists("/usr/local/bin/yt-dlp")) {
    die("yt-dlp is missing at /usr/local/bin/yt-dlp");
}

// Start new stream
$command = "/usr/bin/nohup /usr/local/bin/yt-dlp -f 'best' https://www.youtube.com/watch?v=$safe_idstream -o - " .
    "| ffmpeg -re -i - -c:v libx264 -preset ultrafast -crf 18 -c:a aac -b:a 128k " .
    "-f hls -hls_time 5 -hls_list_size 10 $stream_path " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Check if process started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start streaming. Debug: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
}

// Output stream link
echo "<h3>Stream Started</h3>";
echo "<a href='streams/$safe_idstream.m3u8'>Watch Stream (HLS)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>