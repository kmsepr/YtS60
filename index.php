<?php
echo "<title>YouTube to RTSP Gateway</title>";
echo "<form action='index.php' method='POST'>";
echo "YouTube Search: <input type='text' name='videoname'>";
echo "<input type='submit' value='Search videos!'>";
echo "</form>";

echo "Current viewers: ";
echo shell_exec("ps -ax | grep ffmpeg | wc | awk '{print $1-3}'");
echo " CPU: ";
echo shell_exec("top -b -n1 | grep \"Cpu(s)\" | awk '{print $2}'");
echo "%<br><br>";

$request = $_POST["videoname"] ?? '';
if (empty($request)) { die(); }

file_put_contents('/var/www/html/reqlog.txt', $request . "\r\n", FILE_APPEND);
$reqenc = urlencode($request);

// Fetch video IDs from YouTube search
$ids = shell_exec("curl -s -A 'Mozilla/5.0' 'https://www.youtube.com/results?search_query=$reqenc' | grep -oP '(?<=\"videoId\":\"|\"videoId\":\\\")\\w{11}' | uniq");
$idsarray = preg_split('/\s+/', trim($ids));

$api_key = getenv('YOUTUBE_API_KEY'); // Fetch API key from Koyeb environment
$i = 0;

foreach ($idsarray as $item) {
    if (++$i > 10) break;
    
    // Get video title
    $videon = shell_exec("curl -s 'https://www.youtube.com/watch?v=$item' | grep -oP '(?<=<title>).*(?=</title>)' | sed 's/- YouTube//g'");

    // Get video duration using YouTube API
    $duration = shell_exec("curl -s 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$item&key=$api_key' | grep \"duration\" | awk -F ' ' '{print $2}' | sed -e 's/\"PT//g' | sed -e 's/S\",/ СЕК/g' | sed -e 's/H/ ЧАС:/g' | sed -e 's/M/ МИН:/g'");

    echo "<a href='stream.php?id=$item'>$videon</a> ($duration)<br>";
    echo "<a href='stream.php?id=$item'><img src='https://i.ytimg.com/vi/$item/1.jpg'></a><br>";
}
?>