<?php
// Load API Key from environment variable
$api_key = getenv('YOUTUBE_API_KEY');
$query = $_GET['q'] ?? '';
$idstream = $_GET["id"] ?? '';

// If a search query is given, get the first video ID
if (!empty($query) && !empty($api_key)) {
    $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&key=$api_key&maxResults=1";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if (!empty($data['items'][0]['id']['videoId'])) {
        $idstream = $data['items'][0]['id']['videoId'];
    } else {
        die("No video found.");
    }
}

// Validate YouTube video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid video ID");
}

$safe_idstream = escapeshellcmd($idstream);

// Ensure the streams directory exists
$stream_path = "/var/www/html/streams/$safe_idstream.m3u8";
if (!file_exists("/var/www/html/streams")) {
    mkdir("/var/www/html/streams", 0777, true);
}

// Kill any existing stream process
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Start new stream (Download + Convert to HLS)
$yt_dlp_path = "/usr/local/bin/yt-dlp";
$command = "/usr/bin/nohup $yt_dlp_path -f 'best' https://www.youtube.com/watch?v=$safe_idstream -o - " .
    "| ffmpeg -re -i - -c:v libx264 -preset ultrafast -crf 18 -c:a aac -b:a 128k " .
    "-f hls -hls_time 5 -hls_list_size 10 $stream_path " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";
exec($command);

// Wait and check if process started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start streaming. Check logs: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
}

// Output stream link (HLS)
echo "<h3>Stream Started</h3>";
echo "<a href='streams/$safe_idstream.m3u8'>Watch Stream (HLS)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>