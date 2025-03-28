<?php
$idstream = $_GET["id"] ?? '';

// Validate YouTube video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid stream ID");
}

$safe_idstream = escapeshellcmd($idstream);
$video_path = "/var/www/html/videos/$safe_idstream.mp4";

// Check if file already exists
if (!file_exists($video_path)) {
    // Download video in MP4 format
    $command = "/usr/bin/nohup /var/www/html/yt-dlp_linux -f 'best[ext=mp4]' " .
               "https://www.youtube.com/watch?v=$safe_idstream -o '$video_path' " .
               ">/tmp/yt_dlpdebug.txt 2>&1 &";
    exec($command);

    sleep(5); // Give it some time to start
}

// Check if download was successful
if (!file_exists($video_path)) {
    die("Failed to fetch video.");
}

// Output download link
echo "<a href='/videos/$safe_idstream.mp4'>Download MP4</a><br>";
echo "<br><a href='index.php'>Back</a>";
?>