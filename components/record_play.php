<?php
session_start();
include("RecommendationSystem.php");

$conn = mysqli_connect('localhost', 'root', '', 'myspotify');
mysqli_set_charset($conn, "utf8");
if (!$conn) {
    echo mysqli_connect_error();
}

$filename = $_POST["filename"];

if ($filename) {
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    $recommender = new ContentBasedRecommendation($conn);
    $recommender->recordPlayHistory($filename, $userId);

    $query = "SELECT id FROM songs WHERE filePath = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $filename); // Use "s" for string
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set
    $songId = $result->fetch_assoc(); // Fetch the result as an associative array
    
    // Update recently played in session
    if (!isset($_SESSION['recently_played'])) {
        $_SESSION['recently_played'] = [];
    }
    $_SESSION['recently_played'][] = $songId['id'];
    // Keep only last 10 played songs
    $_SESSION['recently_played'] = array_slice($_SESSION['recently_played'], -10);
}