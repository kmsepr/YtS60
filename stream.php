<?php
$id = $_GET["id"] ?? '';

if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $id)) {
    http_response_code(404);
    die("Invalid stream ID");
}

$safe_id = escapeshellcmd($id);

// Kill old worker
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_id'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_id'");
}

// Convert video to HLS format
$command = "/usr/bin/nohup /usr/bin/yt-dlp -f best -o - 'https://www.youtube.com/watch?v=$safe_id' " .
    "| ffmpeg -re -i - -c:v libx264 -preset ultrafast -crf 18 -c:a aac -b:a 128k " .
    "-f hls -hls_time 5 -hls_list_size 10 /var/www/html/streams/$safe_id.m3u8 " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Wait for process to start
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_id'"));
if (!$newpid) {
    die("Failed to start streaming.");
}

echo "<a href='streams/$safe_id.m3u8'>Watch Stream (HLS)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>