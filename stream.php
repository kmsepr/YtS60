<?php
$idstream = $_GET["id"];
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) { echo 404; die(); }
//kill old worker
$existpid = shell_exec("pgrep -f 'ffmpeg.*$idstream'");
exec("kill $existpid");
//create new one
exec("/usr/bin/nohup /var/www/html/yt-dlp_linux https://www.youtube.com/watch?v=$idstream -o - | ffmpeg -re -i - -acodec amr_wb -ar 16000 -ac 1 -ab 24k -vcodec mpeg4 -vb 104k -r 15 -vf scale=320:240 -f rtsp rtsp://127.0.0.1:8080/$idstream >/tmp/yt_dlpdebug.txt 2>&1 &");
sleep(20);
echo "<a href=rtsp://enthusiastic-edeline-kmsepr-0cbdd0dd.koyeb.app/$idstream>Смотреть (ссылка 1)</a> *554 порт<br>";
