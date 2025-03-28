<?php
echo "<h1>YouTube to HLS Gateway</h1>";

echo "<form action=\"index.php\" method=\"POST\">
Youtube Search: <input type=\"text\" name=\"videoname\">
<input type=\"submit\" value=\"Search videos!\">
</form>";

// Display system info (AJAX-based)
echo "<br>Current viewers: <span id='viewers'>Loading...</span>";
echo "<br>CPU Usage: <span id='cpu'>Loading...</span>%";
echo "<br><br>";

// Handle search request
$request = $_POST["videoname"] ?? '';

if (empty($request)) {
    die();
}

// Log search requests
file_put_contents('/var/www/html/reqlog.txt', $request . PHP_EOL, FILE_APPEND);

// Get API Key securely
$api_key = getenv('YOUTUBE_API_KEY');
$reqenc = urlencode($request);
$search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=10&q=$reqenc&key=$api_key";

$response = file_get_contents($search_url);
$data = json_decode($response, true);

if (!isset($data['items'])) {
    die("Error fetching search results.");
}

foreach ($data['items'] as $item) {
    $video_id = $item['id']['videoId'];
    $title = htmlspecialchars($item['snippet']['title'], ENT_QUOTES, 'UTF-8');
    $thumbnail = $item['snippet']['thumbnails']['medium']['url'];

    // Fetch video duration
    $details_url = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$video_id&key=$api_key";
    $details_response = file_get_contents($details_url);
    $details_data = json_decode($details_response, true);
    $duration = $details_data['items'][0]['contentDetails']['duration'] ?? 'N/A';

    echo "<a href='stream.php?id=$video_id'><font color=blue>$title</font></a> ($duration)<br>";
    echo "<a href='stream.php?id=$video_id'><img src='$thumbnail'></a><br><br>";
}
?>

<script>
    function updateStats() {
        fetch("stats.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("viewers").innerText = data.viewers;
                document.getElementById("cpu").innerText = data.cpu;
            });
    }
    setInterval(updateStats, 5000);
    updateStats();
</script>