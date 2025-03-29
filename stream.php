<?php
$idstream = $_GET["id"] ?? "";
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { echo "Invalid ID"; die(); }

// Kill existing stream
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
if (!empty($existpid)) {
    exec("kill $existpid");
}

// Start new FFmpeg process
$command = "/usr/bin/nohup yt-dlp -f best -g https://www.youtube.com/watch?v=$idstream | ".
           "ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k ".
           "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp ".
           "rtsp://tv.tg-gw.com:554/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Check if stream is active
sleep(3);
while (empty(exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com:554/$idstream"))) {
    sleep(3);
}

// Display RTSP links
echo "<a href='rtsp://tv.tg-gw.com:554/$idstream'>Watch (port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$idstream'>Watch (port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$idstream'>Watch (port 8080)</a><br>";
echo "<a href='index.php'>Back</a>";
?>