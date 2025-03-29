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

// Kill old process
$existpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!empty($existpid)) {
    exec("pkill -f 'ffmpeg.*$safe_idstream'");
}

// Start RTSP Streaming Process (Low Quality for Symbian)
$command = "/usr/bin/nohup yt-dlp -f 'best[ext=mp4]' https://www.youtube.com/watch?v=$safe_idstream -o - | " .
    "ffmpeg -re -i pipe:0 -c:v libx264 -preset ultrafast -crf 28 -b:v 256k -r 15 -s 320x240 " .
    "-c:a aac -b:a 32k -ar 22050 -ac 1 " .
    "-f rtsp rtsp://tv.tg-gw.com/$safe_idstream " .
    ">/tmp/yt_dlpdebug.txt 2>&1 &";

exec($command);

// Wait and check if process started
sleep(5);
$newpid = trim(shell_exec("pgrep -f 'ffmpeg.*$safe_idstream'"));
if (!$newpid) {
    die("Failed to start streaming. Check logs: <pre>" . file_get_contents('/tmp/yt_dlpdebug.txt') . "</pre>");
}

// Output RTSP stream links
echo "<h3>Stream Started</h3>";
echo "<a href='rtsp://tv.tg-gw.com:554/$safe_idstream'>Watch (Port 554)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:443/$safe_idstream'>Watch (Port 443)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8080/$safe_idstream'>Watch (Port 8080)</a><br>";
echo "<a href='rtsp://tv.tg-gw.com:8554/$safe_idstream'>Watch (Port 8554)</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>