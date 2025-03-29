
<?php
$query = $_GET["q"] ?? "";

// Validate search query
if (empty($query)) {
    die("Search query missing");
}

// Search YouTube and get multiple results
$search_command = "/opt/venv/bin/yt-dlp --cookies /mnt/data/cookies.txt 'ytsearch5:$query' --get-title --get-id 2>&1";
$results = explode("\n", trim(shell_exec($search_command)));

if (count($results) < 2) {
    die("<p>No results found. <a href='index.php'>Try Again</a></p>");
}

echo "<h2>Select a video to stream</h2>";
echo "<ul>";
for ($i = 0; $i < count($results); $i += 2) {
    $title = htmlspecialchars($results[$i]);
    $video_id = $results[$i + 1];
    echo "<li><a href='stream.php?id=$video_id'>$title</a></li>";
}
echo "</ul>";
echo "<a href='index.php'>Back</a>";
?>
