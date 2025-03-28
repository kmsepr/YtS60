<?php
$id = $_GET['id'] ?? '';

if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $id)) {
    die("Invalid video ID");
}

$video_url = "https://www.youtube.com/watch?v=$id";
$download_path = "/var/www/html/downloads/$id.mp4";

if (!file_exists($download_path)) {
    $download_command = "/usr/bin/yt-dlp -f best -o '$download_path' '$video_url'";
    exec($download_command, $output, $status);

    if ($status !== 0) {
        die("Download failed.");
    }
}

echo "Download successful! <a href='/downloads/$id.mp4'>Click here</a> to download.";
?>