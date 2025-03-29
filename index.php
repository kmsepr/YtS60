<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Search</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        form {
            margin-bottom: 20px;
        }
        .results {
            width: 60%;
        }
        .video {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>YouTube Video Search</h1>
    <form action="index.php" method="POST">
        <label>Search YouTube: </label>
        <input type="text" name="videoname" required>
        <input type="submit" value="Search">
    </form><div class="results">
    <?php
    $api_key = getenv("YOUTUBE_API_KEY");
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $query = $_POST["videoname"] ?? "";

        if (empty($query)) {
            die("<p>Please enter a search term.</p>");
        }

        $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&q=" . urlencode($query) . "&key=" . $api_key . "&maxResults=10";
        $response = file_get_contents($api_url);
        $data = json_decode($response, true);

        if (!isset($data["items"])) {
            die("<p>No results found. Try Again.</p>");
        }

        echo "<h2>Search Results</h2>";
        foreach ($data["items"] as $item) {
            $video_id = $item["id"]["videoId"];
            $title = htmlspecialchars($item["snippet"]["title"], ENT_QUOTES);
            $thumbnail = $item["snippet"]["thumbnails"]["medium"]["url"];

            echo "<div class='video'>";
            echo "<a href='stream.php?id=$video_id'>";
            echo "<img src='$thumbnail' alt='$title'><br>";
            echo "<strong>$title</strong></a>";
            echo "</div><hr>";
        }
    }
    ?>
</div>

</body>
</html>