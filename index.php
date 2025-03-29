<?php
echo "<title>Youtube</title>";
echo "<form action='index.php' method='POST'>
      Youtube Search: <input type='text' name='videoname'>
      <input type='submit' value='Search videos!'>
      </form>";

echo "Current viewers: ";
echo shell_exec("ps -ax | grep ffmpeg | wc | awk ' { print $1-3 }'");
echo " | CPU: ";
echo shell_exec("top -b -n1 | grep \"Cpu(s)\" | awk '{print $2}'");
echo "%<br><br>";

$request = $_POST["videoname"] ?? '';
if (empty($request)) { die(); }

file_put_contents('/var/www/html/reqlog.txt', $request . "\r\n", FILE_APPEND);
$reqenc = urlencode($request);
$apiKey = getenv('YOUTUBE_API_KEY');

$searchUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=10&q=$reqenc&key=$apiKey";
$searchResults = json_decode(file_get_contents($searchUrl), true);

foreach ($searchResults['items'] as $item) {
    $videoId = $item['id']['videoId'];
    $title = htmlspecialchars($item['snippet']['title']);
    $durationData = json_decode(file_get_contents(
        "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$videoId&key=$apiKey"
    ), true);
    $duration = $durationData['items'][0]['contentDetails']['duration'];

    echo "<a href='stream.php?id=$videoId'>$title</a> ($duration)<br>";
    echo "<a href='stream.php?id=$videoId'><img src='https://i.ytimg.com/vi/$videoId/1.jpg'></a><br>";
}
?>