<!DOCTYPE html>
<html lang="en">
<head>
    <title>Youtube to RTSP Gateway</title>
</head>
<body>
    <h1>Youtube to RTSP Gateway</h1>
    
    <form action="index.php" method="POST">
        Youtube Search: <input type="text" name="videoname" required>
        <input type="submit" value="Search videos!">
    </form>
    
    <h3>System Status:</h3>
    <?php
    echo "Current viewers: " . shell_exec("pgrep -c ffmpeg") . "<br>";
    echo "CPU Usage: " . shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'") . "%<br><br>";

    // Get user search query
    $request = $_POST["videoname"] ?? "";
    if (empty($request)) { die(); }

    // Log request
    file_put_contents('/var/www/html/reqlog.txt', $request . "\n", FILE_APPEND);

    // Load API Key
    $api_key = getenv('YOUTUBE_API_KEY');
    if (empty($api_key)) { die("Error: YouTube API Key not set."); }

    // Search YouTube using API
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($request) . "&type=video&maxResults=10&key=$api_key";
    $response = file_get_contents($search_url);
    $data = json_decode($response, true);

    if (empty($data['items'])) {
        die("No videos found.");
    }

    // Display video results
    foreach ($data['items'] as $video) {
        $videoId = $video['id']['videoId'];
        $title = htmlspecialchars($video['snippet']['title'], ENT_QUOTES, 'UTF-8');
        $thumbnail = "https://i.ytimg.com/vi/$videoId/1.jpg";

        echo "<a href='stream.php?id=$videoId'><img src='$thumbnail' alt='$title'></a><br>";
        echo "<a href='stream.php?id=$videoId'><b>$title</b></a><br><br>";
    }
    ?>
</body>
</html>