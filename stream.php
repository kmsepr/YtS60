<?php
$idstream = $_GET["id"] ?? '';

// Validate YouTube video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid stream ID");
}

$safe_idstream = escapeshellarg($idstream);

// Kill old worker
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Start new stream
$command = "/usr/bin/nohup /var/www/html/yt-dlp_linux https://www.youtube.com/watch?v=$safe_idstream -o - " .
    "| ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 104k -r 15 -vf scale=320:240 " .
    "-f rtsp rtsp://127.0.0.1:8080/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &";
exec($command);

// Wait and check if process started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start streaming.");
}

// Output stream link (only port 8000)
echo "<a href='rtsp://enthusiastic-edeline-kmsepr-0cbdd0dd.koyeb.app:8000/$idstream'>Watch</a> *Port 8000<br>";
echo "<br><a href='index.php'>Back</a>";
?>
