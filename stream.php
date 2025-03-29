<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    die("Invalid video ID");
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// Retry mechanism for yt-dlp download
$retries = 5;
$attempt = 0;
$yt_dl_command = "/opt/venv/bin/yt-dlp --cookies /mnt/data/cookies.txt -f best -g https://www.youtube.com/watch?v=$idstream";

while ($attempt < $retries) {
    $output = shell_exec($yt_dl_command);
    if ($output) break;
    $attempt++;
    sleep(10);
}

// Construct the FFmpeg command for RTSP streaming
$ffmpeg_command = "$yt_dl_command | ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
                  "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp $rtsp_url " .
                  ">/tmp/yt_dlpdebug.txt 2>&1 &";

// Execute FFmpeg streaming command
exec($ffmpeg_command);
sleep(3);

// Check if the stream is running
exec("ffprobe -show_streams -v quiet $rtsp_url 2>&1", $output, $status);
if ($status != 0) {
    file_put_contents('/tmp/rtsp_check_error.log', implode("\n", $output));
    echo "<p>Stream failed. <a href='index.php'>Try Again</a></p>";
} else {
    echo "<p>Stream started! <a href='$rtsp_url'>Watch Stream</a></p>";
    echo "<a href='index.php'>Back</a>";
}
?>
