<?php
$idstream = $_GET["id"] ?? "";

// Validate YouTube ID format
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    echo "Invalid video ID"; 
    die();
}

// Define cache directory
$cache_dir = "/mnt/data/cache";
if (!file_exists($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}
$cache_file = "$cache_dir/$idstream.txt";

// Check cache
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 3600) { // 1-hour cache
    $video_url = file_get_contents($cache_file);
} else {
    // Fetch video URL from local yt-dlp API
    $api_url = "http://localhost:9080/api/info?url=https://www.youtube.com/watch?v=$idstream";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);
    $video_url = $data['url'] ?? "";

    if ($video_url) {
        file_put_contents($cache_file, $video_url); // Cache result
    }
}

// Exit if no valid video URL
if (!$video_url) {
    echo "Error fetching video";
    die();
}

// Define RTSP output URL
$rtsp_url = "rtsp://tv.tg-gw.com:554/$idstream";

// Construct the FFmpeg streaming command
$command = "ffmpeg -re -i \"$video_url\" -acodec amr_wb -ar 16000 -ac 1 -ab 24k " .
           "-vcodec mpeg4 -vb 128k -r 15 -vf scale=320:240 -f rtsp " .
           "$rtsp_url >/tmp/yt_dlpdebug.txt 2>&1 &";

// Execute FFmpeg streaming command
exec($command);

// Wait for RTSP stream to start
sleep(3);

// Check if the stream is running
$check_stream = exec("ffprobe -show_streams -v quiet $rtsp_url 2>&1", $output, $status);
if ($status != 0) {
    file_put_contents('/tmp/rtsp_check_error.log', implode("\n", $output)); // Log errors
    header("Refresh:0");
} else {
    echo "<a href='$rtsp_url'>Watch Stream</a><br>";
    echo "<a href='index.php'>Back</a>";
}
?>