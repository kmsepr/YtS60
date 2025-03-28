<?php
$api_key = getenv('YOUTUBE_API_KEY') ?: 'YOUR_YOUTUBE_API_KEY';
$query = $_GET['query'] ?? '';

if ($query) {
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&key=$api_key&maxResults=5";
    $response = json_decode(file_get_contents($search_url), true);
}
?>

<form method="GET">
    <input type="text" name="query" placeholder="Search YouTube" value="<?= htmlspecialchars($query) ?>">
    <button type="submit">Search</button>
</form>

<?php if (!empty($response['items'])): ?>
    <ul>
        <?php foreach ($response['items'] as $video): ?>
            <li>
                <a href="stream.php?id=<?= $video['id']['videoId'] ?>" target="_blank">
                    <?= htmlspecialchars($video['snippet']['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>