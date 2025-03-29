<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    echo "Invalid video ID"; 
    die();
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// Retry mechanism for yt-dlp download
$retries = 5;
$attempt = 0;
while ($attempt < $retries) {
    $yt_dl_command = "/usr/bin/yt-dlp --cookies /mnt/data/cookies.txt -f best -g https://www.youtube.com/watch?v=$idstream";
    $output = shell_exec($yt_dl_command);
    if ($output) {
        break;
    }
    $attempt++;
    sleep(10); // Retry delay
}

// Construct the command using cookies
$command = "/usr/bin/nohup yt-dlp --cookies /mnt/data/cookies.txt -f best -g https://www.youtube.com/watch?v=$idstream | " .
           "ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
           "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " .
           "$rtsp_url >/tmp/yt_dlpdebug.txt 2>&1 &";

// Execute FFmpeg streaming command
exec($command);

// Wait for RTSP stream to start
sleep(3);

// Check if the stream is running
$check_stream = exec("ffprobe -show_streams -v quiet $rtsp_url 2>&1", $output, $status);
if ($status != 0) {
    // Log error for debugging
    file_put_contents('/tmp/rtsp_check_error.log', implode("\n", $output));
    header("Refresh:0");
} else {
    echo "<a href='$rtsp_url'>Watch Stream</a><br>";
    echo "<a href='index.php'>Back</a>";
}
?>
