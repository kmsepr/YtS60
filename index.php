<?php
echo "<h1>YouTube Video Search</h1>";

echo "<form action=\"index.php\" method=\"POST\">
    YouTube Search: <input type=\"text\" name=\"videoname\">
    <input type=\"submit\" value=\"Search\">
</form>";

$api_key = getenv('YOUTUBE_API_KEY'); // Ensure this is set in your environment
$search_query = $_POST["videoname"] ?? '';

if (!empty($search_query)) {
    $encoded_query = urlencode($search_query);
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=5&q=$encoded_query&key=$api_key";

    $response = file_get_contents($search_url);
    $data = json_decode($response, true);

    if (!isset($data['items'])) {
        die("Error fetching search results.");
    }

    foreach ($data['items'] as $item) {
        $video_id = $item['id']['videoId'];
        $title = htmlspecialchars($item['snippet']['title'], ENT_QUOTES, 'UTF-8');
        $thumbnail = $item['snippet']['thumbnails']['medium']['url'];

        echo "<div>
            <a href='download.php?id=$video_id'><img src='$thumbnail' alt='$title'></a><br>
            <a href='download.php?id=$video_id'>$title</a><br>
        </div><br>";
    }
}
?>