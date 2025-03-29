<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTSP YouTube Streamer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        input {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
        button {
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Search and Stream YouTube Videos via RTSP</h2>
    <form action="search.php" method="get">
        <input type="text" name="q" placeholder="Enter YouTube search term" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>
