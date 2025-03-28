<?php
// Load API Key
$api_key = getenv('YOUTUBE_API_KEY');
$query = $_GET['q'] ?? '';
$idstream = $_GET["id"] ?? '';

// Debug: Check API key
if (!$api_key) {
    die("Missing YouTube API Key");
}

// Search for a video if query is provided
if (!empty($query)) {
    $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&key=$api_key&maxResults=1";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if (!isset($data['items'][0]['id']['videoId'])) {
        die("No video found.");
    }
    
    $idstream = $data['items'][0]['id']['videoId'];
}

// Validate video ID
if (!preg_match("/^[a-zA-Z0-9_-]{11}$/", $idstream)) {
    http_response_code(404);
    die("Invalid video ID");
}

// Redirect to streaming page
header("Location: stream.php?id=$idstream");
exit;
?>