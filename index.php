<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube RTSP Streamer</title>
</head>
<body>
    <h2>Search YouTube and Stream via RTSP</h2>
    <form action="search.php" method="POST">
        YouTube Search: <input type="text" name="videoname" required>
        <input type="submit" value="Search">
    </form>

    <h3>Server Info:</h3>
    <p>Current viewers: <?php echo shell_exec("ps -ax | grep ffmpeg | wc -l"); ?></p>
    <p>CPU Usage: <?php echo shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'"); ?>%</p>
</body>
</html>
