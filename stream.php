<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    echo "Invalid video ID"; 
    die();
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// Ensure yt-dlp uses the correct cache directory
putenv('YTDLP_CACHE_DIR=/tmp/yt-dlp-cache');

// Retry mechanism for yt-dlp download
$retries = 5;
$attempt = 0;
$video_url = "";

while ($attempt < $retries) {
    $yt_dl_command = "/opt/venv/bin/yt-dlp --cache-dir /tmp/yt-dlp-cache --cookies /mnt/data/cookies.txt -f best -g https://www.youtube.com/watch?v=$idstream";
    $video_url = shell_exec($yt_dl_command);
    if ($video_url) {
        $video_url = trim($video_url);
        break;
    }
    $attempt++;
    sleep(10); // Retry delay
}

// If no video URL is found, exit
if (!$video_url) {
    echo "Failed to get video URL from YouTube.";
    die();
}

// Construct the FFmpeg command using the extracted URL
$command = "ffmpeg -re -i \"$video_url\" -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
           "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " .
           "$rtsp_url >/tmp/yt_dlpdebug.txt 2>&1 &";

// Execute FFmpeg streaming command
exec($command);

// Wait for RTSP stream to start
sleep(3);

// Check if the stream is running
exec("ffprobe -show_streams -v quiet $rtsp_url 2>&1", $output, $status);
if ($status != 0) {
    // Log error for debugging
    file_put_contents('/tmp/rtsp_check_error.log', implode("\n", $output));
    header("Refresh:0");
} else {
    echo "<a href='$rtsp_url'>Watch Stream</a><br>";
    echo "<a href='index.php'>Back</a>";
}
?>