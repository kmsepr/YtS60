<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = $_POST["videoname"] ?? "";
    if (empty($request)) exit();

    // Log search request
    file_put_contents('/var/www/html/reqlog.txt', $request . "\n", FILE_APPEND);

    // YouTube API request
    $apikey = getenv('YOUTUBE_API_KEY');
    $reqenc = urlencode($request);
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$reqenc&type=video&key=$apikey&maxResults=10";

    $response = file_get_contents($search_url);
    if (!$response) {
        die("<p>Error: Unable to fetch search results.</p>");
    }

    $search_results = json_decode($response, true);
    if (isset($search_results['error'])) {
        die("<p>Error: " . $search_results['error']['message'] . "</p>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube to RTSP</title>
</head>
<body>
    <h1>YouTube to RTSP Stream</h1>
    
    <form action="index.php" method="POST">
        <label>YouTube Search:</label>
        <input type="text" name="videoname" required>
        <input type="submit" value="Search">
    </form>

    <p>
        <strong>Current Viewers:</strong> <?php echo shell_exec("pgrep -c ffmpeg"); ?> |
        <strong>CPU Usage:</strong> <?php echo shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'"); ?>%
    </p>

    <?php if (!empty($search_results)): ?>
        <h2>Search Results</h2>
        <ul>
            <?php foreach ($search_results['items'] as $item): ?>
                <?php 
                    $videoId = $item['id']['videoId'];
                    $title = htmlspecialchars($item['snippet']['title']);
                    $thumbnail = $item['snippet']['thumbnails']['default']['url'];
                ?>
                <li>
                    <a href='stream.php?id=<?php echo $videoId; ?>'><img src='<?php echo $thumbnail; ?>' alt='Thumbnail'></a>
                    <a href='stream.php?id=<?php echo $videoId; ?>'><?php echo $title; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
