<?php
// HTML Form for YouTube Search
echo '<title>Youtube to RTSP</title>
<form action="index.php" method="POST">
    Youtube Search: <input type="text" name="videoname">
    <input type="submit" value="Search videos!">
</form>';

// Show current FFmpeg processes
echo "Current viewers: ";
echo shell_exec("ps -ax | grep ffmpeg | wc -l");
echo "<br>";

// Read YouTube API Key from Koyeb environment
$api_key = getenv("YOUTUBE_API_KEY");

$request = $_POST["videoname"] ?? '';
if (empty($request)) { die(); }

$reqenc = urlencode($request);
$api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$reqenc&type=video&maxResults=10&key=$api_key";
$response = json_decode(file_get_contents($api_url), true);

// Display search results
foreach ($response["items"] as $item) {
    $video_id = $item["id"]["videoId"];
    $title = $item["snippet"]["title"];
    $thumbnail = $item["snippet"]["thumbnails"]["default"]["url"];
    echo "<a href='stream.php?id=$video_id'>$title</a><br>";
    echo "<a href='stream.php?id=$video_id'><img src='$thumbnail'></a><br>";
}
?>