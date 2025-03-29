<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    echo "Invalid video ID"; 
    die();
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// yt-dlp command with anti-429 fixes
$yt_dl_command = "/opt/venv/bin/yt-dlp --cache-dir /tmp/yt-dlp-cache --cookies /mnt/data/cookies.txt " .
                 "--user-agent \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\" " .
                 "--extractor-args \"youtube:player_client=android\" -f b -g https://www.youtube.com/watch?v=$idstream";

// Retry mechanism for yt-dlp
$retries = 5;
$attempt = 0;
$video_url = null;

while ($attempt < $retries) {
    $video_url = shell_exec($yt_dl_command);
    if ($video_url) {
        break;
    }
    $attempt++;
    sleep(10); // Retry delay
}

// Stop if video URL couldn't be retrieved
if (!$video_url) {
    echo "Error: Unable to fetch video URL.";
    die();
}

// FFmpeg command for RTSP streaming
$command = "ffmpeg -re -i \"$video_url\" -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
           "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " .
           "$rtsp_url > /tmp/yt_dlpdebug.txt 2>&1 &";

// Execute FFmpeg streaming command
exec($command);
sleep(3); // Wait for stream to start

// Check if RTSP stream is running
exec("ffprobe -show_streams -v quiet $rtsp_url 2>&1", $output, $status);
if ($status != 0) {
    file_put_contents('/tmp/rtsp_check_error.log', implode("\n", $output));
    echo "Error: Stream failed to start. Try again.";
} else {
    echo "<a href='$rtsp_url'>Watch Stream</a><br>";
    echo "<a href='index.php'>Back</a>";
}
?>