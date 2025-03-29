<?php
echo "<h2>YouTube to RTSP Gateway</h2>";

echo "<form action='index.php' method='POST'>
    YouTube Search: <input type='text' name='videoname'>
    <input type='submit' value='Search'>
</form>";

// Show server load
echo "<br><b>Server Load:</b><br>";
echo "Current viewers: " . shell_exec("ps -ax | grep ffmpeg | wc -l") . "<br>";
echo "CPU Usage: " . shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'") . "%<br><br>";

// Handle search request
if (!empty($_POST["videoname"])) {
    $request = trim($_POST["videoname"]);
    file_put_contents('/var/www/html/reqlog.txt', $request . "\n", FILE_APPEND);

    // Fetch video IDs from YouTube search
    $reqenc = urlencode($request);
    $api_key = "YOUR_YOUTUBE_API_KEY"; // Replace with your API key
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$reqenc&type=video&maxResults=10&key=$api_key";
    
    $response = file_get_contents($search_url);
    $json = json_decode($response, true);

    if (!isset($json["items"])) {
        die("Error fetching results. Check API key.");
    }

    foreach ($json["items"] as $video) {
        $video_id = $video["id"]["videoId"];
        $title = htmlspecialchars($video["snippet"]["title"]);
        $thumbnail = $video["snippet"]["thumbnails"]["default"]["url"];

        echo "<a href='stream.php?id=$video_id'>$title</a><br>";
        echo "<a href='stream.php?id=$video_id'><img src='$thumbnail'></a><br><br>";
    }
}
?>