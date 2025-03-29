<?php
$idstream = $_GET["id"] ?? "";
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    echo "Invalid Video ID";
    die();
}

// Kill old FFmpeg process if running
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
if (!empty($existpid)) {
    exec("kill $existpid");
}

// Generate RTSP stream from YouTube
exec("nohup yt-dlp -f best -g https://www.youtube.com/watch?v=$idstream | 
      ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k 
      -vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 
      -f rtsp rtsp://tv.tg-gw.com/$idstream > /tmp/yt_dlpdebug.txt 2>&1 &");

sleep(3);

// Verify if RTSP stream is active
$retry = 5;
while ($retry > 0 && empty(shell_exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com/$idstream"))) {
    sleep(3);
    $retry--;
}

// Show stream links if active
if ($retry > 0) {
    echo "<h2>Watch Stream</h2>";
    echo "<a href='rtsp://tv.tg-gw.com:554/$idstream'>Link 1 (Port 554)</a><br>";
    echo "<a href='rtsp://tv.tg-gw.com:443/$idstream'>Link 2 (Port 443)</a><br>";
    echo "<a href='rtsp://tv.tg-gw.com:8080/$idstream'>Link 3 (Port 8080)</a><br>";
    echo "<a href='rtsp://tv.tg-gw.com:8554/$idstream'>Link 4 (Port 8554)</a><br>";
} else {
    echo "<h2>Stream is not available. Try again later.</h2>";
}
echo "<a href='index.php'>Back</a>";
?>