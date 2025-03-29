<?php
echo "<title>YouTube to RTSP</title>";
echo "<form action='index.php' method='POST'>";
echo "YouTube Search: <input type='text' name='videoname'>";
echo "<input type='submit' value='Search'>";
echo "</form>";

echo "Current viewers: " . shell_exec("ps -ax | grep ffmpeg | wc -l");
echo " | CPU: " . shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'") . "%<br><br>";

$request = $_POST["videoname"] ?? "";
if (empty($request)) die();

file_put_contents('/var/www/html/reqlog.txt', $request . "\n", FILE_APPEND);
$reqenc = urlencode($request);

// Search YouTube (Using YouTube API Key from Koyeb)
$apikey = getenv('YOUTUBE_API_KEY');
$search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$reqenc&type=video&key=$apikey&maxResults=10";
$search_results = json_decode(file_get_contents($search_url), true);

foreach ($search_results['items'] as $item) {
    $videoId = $item['id']['videoId'];
    $title = $item['snippet']['title'];
    $thumbnail = $item['snippet']['thumbnails']['default']['url'];

    echo "<a href='stream.php?id=$videoId'><img src='$thumbnail'></a> ";
    echo "<a href='stream.php?id=$videoId'>$title</a><br>";
}
?>