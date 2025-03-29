<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube Video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("Invalid video ID.");
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// yt-dlp command to get the video URL (uses cookies)
$yt_dl_command = "/opt/venv/bin/yt-dlp --cookies /mnt/data/cookies.txt -f best -g " . escapeshellarg("https://www.youtube.com/watch?v=$idstream");

$retries = 5;
$video_url = "";

// Retry fetching video URL
for ($i = 0; $i < $retries; $i++) {
    $video_url = trim(shell_exec($yt_dl_command));
    if (!empty($video_url)) break;
    sleep(10);
}

// If yt-dlp fails
if (empty($video_url)) {
    file_put_contents('/tmp/yt_dlp_error.log', "Failed to fetch video URL for ID: $idstream\n", FILE_APPEND);
    die("<p>Failed to get video URL. <a href='index.php'>Try Again</a></p>");
}

// Kill old ffmpeg process if running
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
if ($existpid) {
    exec("kill $existpid", $output, $return_var);
}

// Construct FFmpeg command for RTSP streaming
$ffmpeg_command = "ffmpeg -re -i " . escapeshellarg($video_url) . 
    " -acodec amr_wb -ar 16000 -ac 1 -ab 24k " . 
    "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " . escapeshellarg($rtsp_url) . 
    " > /tmp/yt_dlpdebug.txt 2>&1 &";

// Start FFmpeg streaming
exec($ffmpeg_command);
sleep(3);

// Check if stream is available
exec("ffprobe -show_streams -v quiet " . escapeshellarg($rtsp_url), $output, $status);
if ($status != 0) {
    echo "<p>Stream failed. <a href='index.php'>Try Again</a></p>";
} else {
    echo "<p>Stream started! <a href='$rtsp_url'>Watch Stream</a></p>";
}
?>
