<?php
if (isset($_POST['url'])) {
    $url = escapeshellarg($_POST['url']);
    $command = "yt-dlp -f best -o - $url | ffmpeg -i pipe:0 -c:v copy -c:a copy -f rtsp rtsp://tv.tg-gw.com/mystream";
    shell_exec($command);
    echo "Streaming started!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>YouTube to RTSP</title>
</head>
<body>
    <h2>YouTube to RTSP Stream</h2>
    <form method="post">
        <input type="text" name="url" placeholder="Enter YouTube URL" required>
        <button type="submit">Start Streaming</button>
    </form>
</body>
</html>
