<?php
$idstream = $_GET["id"] ?? "";
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { echo "Invalid ID"; die(); }

// Kill old FFmpeg process for this stream
exec("pkill -f 'ffmpeg.*$idstream'");

// Start new stream
exec("ffmpeg -re -i $(yt-dlp -f best -g https://www.youtube.com/watch?v=$idstream) \
    -acodec amr_wb -ar 16000 -ac 1 -ab 24k \
    -vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 \
    -f rtsp rtsp://tv.tg-gw.com:8554/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &");

// Wait for the stream to start
sleep(3);

// Check if the stream is running
if (empty(exec("ffprobe -show_streams -v quiet rtsp://tv.tg-gw.com:8554/$idstream"))) {
    sleep(3);
    header("Refresh:0");
} else {
    echo "<a href=rtsp://tv.tg-gw.com:554/$idstream>Watch (link 1)</a><br>";
    echo "<a href=rtsp://tv.tg-gw.com:443/$idstream>Watch (link 2)</a><br>";
    echo "<a href=rtsp://tv.tg-gw.com:8554/$idstream>Watch (link 3)</a><br>";
    echo "<a href=index.php>Back</a>";
}
?>