<?php
require 'config.php';  // Include YouTube API Key

$query = $_POST["videoname"] ?? "";

// Validate input
if (empty($query)) {
    die("<p>Error: Empty search query. <a href='index.php'>Try Again</a></p>");
}

// Use YouTube API for search
$api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&maxResults=5&type=video&key=" . YT_API_KEY;
$response = file_get_contents($api_url);
$data = json_decode($response, true);

// Handle API errors
if (empty($data['items'])) {
    die("<p>No results found. <a href='index.php'>Try Again</a></p>");
}

// Display search results
echo "<h2>Select a Video to Stream</h2><ul>";
foreach ($data['items'] as $item) {
    $video_id = $item['id']['videoId'];
    $title = htmlspecialchars($item['snippet']['title']);
    $thumbnail = $item['snippet']['thumbnails']['default']['url'];
    
    echo "<li><a href='stream.php?id=$video_id'>$title</a> <br>";
    echo "<a href='stream.php?id=$video_id'><img src='$thumbnail'></a></li><hr>";
}
echo "</ul><a href='index.php'>Back</a>";
?>
