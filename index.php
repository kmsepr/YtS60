<?php
// Load API Key
$api_key = getenv('YOUTUBE_API_KEY');
$query = $_POST["videoname"] ?? '';

if (!empty($query)) {
    $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&key=$api_key&maxResults=5";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);
    
    foreach ($data['items'] as $video) {
        $idstream = $video['id']['videoId'];
        $title = htmlspecialchars($video['snippet']['title']);
        echo "<a href='stream.php?id=$idstream'>$title</a><br>";
    }
}
?>

<html>
<head><title>Youtube RTSP Gateway</title></head>
<body>
<form action="index.php" method="POST">
    Youtube Search: <input type="text" name="videoname">
    <input type="submit" value="Search">
</form>
</body>
</html>