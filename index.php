Symbian youtube client. Youtube to rtsp gateway.<br><br>
<title>Youtube</title>
<form action="index.php" method="POST">
Поиск видеороликов на Youtube: <input type="text" name="videoname">
<input type="submit" value="Искать!">
</form>

<?php
echo "Количество yt-dlp + ffmpeg-тредов: ";
echo shell_exec("ps -ax | grep ffmpeg | wc | awk ' { print $1-2 }'");
echo "Загрузка CPU: ";
echo shell_exec("top -b -n1 | grep \"Cpu(s)\" | awk '{print $2}'");
echo "%";
echo "<br><br>";

$request = $_POST["videoname"];
if (empty($request)){ die(); }
file_put_contents('/var/www/html/reqlog.txt', $request . "\r\n", FILE_APPEND);
$reqenc = urlencode($request);
$ids = shell_exec("curl -A 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36' 'https://www.youtube.com/results?search_query=$reqenc' | grep -oP '(?<=\"videoId\":\"|\"videoId\":\\\")\\w{11}' | uniq");
$idsarray = preg_split('/\s+/', trim($ids));
foreach ($idsarray as $item) {
  if (++$i == 11) break;
  $videon = shell_exec("curl -s -A 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36' 'https://www.youtube.com/watch?v=$item' | grep -o -P '(?<=<title>).*(?=</title>)' | sed 's/- YouTube//g'");
  echo "<font color=blue><a href='stream.php?id=$item'>$videon</font></a>";
  echo "<br>";
  echo "<a href='stream.php?id=$item'><img src=https://i.ytimg.com/vi/$item/1.jpg></a><br>";
  }
?>
