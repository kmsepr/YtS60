<?php
// Display CPU usage and viewers
echo "Current viewers: ";
echo shell_exec("ps -ax | grep ffmpeg | wc | awk ' { print $1-3 }'");
echo " | CPU Usage: ";
echo shell_exec("top -b -n1 | grep \"Cpu(s)\" | awk '{print $2}'") . "%";
echo "<br><br>";

// Search form
echo '<form action="index.php" method="POST">
        YouTube Search: <input type="text" name="videoname">
        <input type="submit" value="Search">
      </form>';

// Process search query
$request = $_POST["videoname"] ?? "";
if (empty($request)) { die(); }

$reqenc = urlencode($request);
$ids = shell_exec("curl -s 'https://www.youtube.com/results?search_query=$reqenc' | grep -oP '(?<=\"videoId\":\")\\w{11}' | uniq");
$idsarray = array_unique(preg_split('/\s+/', trim($ids)));

foreach ($idsarray as $index => $video_id) {
    if ($index >= 10) break;

    $title = shell_exec("curl -s 'https://www.youtube.com/watch?v=$video_id' | grep -o -P '(?<=<title>).*?(?=</title>)' | sed 's/- YouTube//g'");
    echo "<a href='stream.php?id=$video_id'>$title</a> <br>";
    echo "<a href='stream.php?id=$video_id'><img src='https://i.ytimg.com/vi/$video_id/1.jpg'></a><br><br>";
}
?>