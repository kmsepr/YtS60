<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube Video ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("Invalid video ID");
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// yt-dlp command to fetch the video URL
$yt_dl_command = "/opt/venv/bin/yt-dlp --cookies /mnt/data/cookies.txt -f best -g " . escapeshellarg("https://www.youtube.com/watch?v=$idstream");

$retries = 5;
$attempt = 0;
$video_url = "";

// Retry yt-dlp download if it fails
while ($attempt < $retries) {
    $video_url = shell_exec($yt_dl_command);
    if (!empty($video_url)) break;
    $attempt++;
    sleep(10);
}

// If yt-dlp failed after retries
if (empty($video_url)) {
    file_put_contents('/tmp/yt_dlp_error.log', date("Y-m-d H:i:s") . " - Failed to fetch video URL for ID: $idstream\n", FILE_APPEND);
    die("<p>Failed to get video URL. <a href='index.php'>Try Again</a></p>");
}

$video_url = trim($video_url); // Clean up the URL

// Construct FFmpeg command for RTSP streaming
$ffmpeg_command = "nohup ffmpeg -re -i " . escapeshellarg($video_url) . 
    " -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
    "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " . escapeshellarg($rtsp_url) .
    " > /tmp/ffmpeg_stream_$idstream.log 2>&1 &";

// Execute FFmpeg streaming command
exec($ffmpeg_command);
sleep(3); // Give it time to start

// Check if the stream is running
exec("ffprobe -show_streams -v quiet " . escapeshellarg($rtsp_url) . " 2>&1", $output, $status);

if ($status != 0) {
    file_put_contents('/tmp/rtsp_check_error.log', date("Y-m-d H:i:s") . " - Stream failed for ID: $idstream\n" . implode("\n", $output) . "\n", FILE_APPEND);
    echo "<p>Stream failed. <a href='index.php'>Try Again</a></p>";
} else {
    echo "<p>Stream started! <a href='$rtsp_url'>Watch Stream</a></p>";
    echo "<a href='index.php'>Back</a>";
}
?>
