<?php
// Your YouTube API Key (make sure it's stored securely!)
$api_key = "YOUR_YOUTUBE_API_KEY";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $query = $_POST["videoname"] ?? "";

    if (empty($query)) {
        die("Please enter a search term.");
    }

    // YouTube API Search URL
    $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&q=" . urlencode($query) . "&key=" . $api_key . "&maxResults=10";

    // Fetch video data from YouTube API
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if (!isset($data["items"])) {
        die("No results found. Try Again.");
    }

    // Display search results
    echo "<h2>Search Results</h2>";
    foreach ($data["items"] as $item) {
        $video_id = $item["id"]["videoId"];
        $title = htmlspecialchars($item["snippet"]["title"], ENT_QUOTES);
        $thumbnail = $item["snippet"]["thumbnails"]["medium"]["url"];

        echo "<div>";
        echo "<a href='stream.php?id=$video_id'>";
        echo "<img src='$thumbnail' alt='$title'><br>";
        echo "<strong>$title</strong></a>";
        echo "</div><hr>";
    }
}
?>

<!-- Search Form -->
<form action="index.php" method="POST">
    <label>Search YouTube: </label>
    <input type="text" name="videoname" required>
    <input type="submit" value="Search">
</form>
