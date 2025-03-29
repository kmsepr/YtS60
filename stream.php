<?php
set_time_limit(90);
$idstream = $_GET["id"] ?? "";

// Validate video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("<p>Error: Invalid video ID. <a href='index.php'>Try Again</a></p>");
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// Kill old FFmpeg process
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
if ($existpid) {
    exec("kill $existpid");
}

// Run yt-dlp to get video URL with cookies
$yt_dl_command = "/opt/venv/bin/yt-dlp --cookies /mnt/data/cookies.txt -f best -g https://www.youtube.com/watch?v=$idstream";
$video_url = shell_exec($yt_dl_command);

if (empty($video_url)) {
    die("<p>Error: Could not fetch video. <a href='index.php'>Try Again</a></p>");
}

// Start FFmpeg stream
$ffmpeg_command = "ffmpeg -re -i " . escapeshellarg(trim($video_url)) .
    " -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
    "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " . escapeshellarg($rtsp_url) .
    " > /tmp/ffmpeg_stream_$idstream.log 2>&1 &";

exec($ffmpeg_command);

// Check if the stream is running
for ($i = 0; $i < 30; $i++) {
    $checkstream = exec("ffprobe -show_streams -v quiet " . escapeshellarg($rtsp_url));
    if (!empty($checkstream)) {
        echo "<h2>Stream Ready!</h2>";
        echo "<a href='$rtsp_url'>Watch Stream</a><br>";
        echo "<a href='index.php'>Back</a>";
        exit;
    }
    sleep(3);
}

// If stream is not available
echo "<h2>Stream not available after 90 seconds.</h2>";
?>
