<?php
// RecommendationSystem.php
$conn = mysqli_connect('localhost', 'root', '', 'myspotify');
mysqli_set_charset($conn, "utf8");
if (!$conn) {
    echo mysqli_connect_error();
}
class ContentBasedRecommendation {
    private $conn;
    private $weightings = [
        'title_similarity' => 0.4,
        'artist_similarity' => 0.4,  
        'popularity' => 0.2         
    ];
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Calculate similarity score between two strings using improved metrics
     */
    private function calculateStringSimilarity($str1, $str2) {
        $str1 = trim(strtolower($str1));
        $str2 = trim(strtolower($str2));
        
        if (empty($str1) && empty($str2)) return 1.0;
        if (empty($str1) || empty($str2)) return 0.0;
        
        // Combine Levenshtein with similar_text for better accuracy
        similar_text($str1, $str2, $percentSimilar);
        $levenSimilarity = 1 - (levenshtein($str1, $str2) / max(strlen($str1), strlen($str2)));
        
        return ($levenSimilarity + ($percentSimilar / 100)) / 2;
    }
    
    /**
     * Get similar songs with enhanced matching criteria
     */
    public function getSimilarSongs($songId, $limit = 5) {
        // Sanitize input
        $songId = filter_var($songId, FILTER_VALIDATE_INT);
        if (!$songId) return [];
        
        // Get current song details
        $query = "SELECT s.*, si.name as singerName, si.id as singerID,
                        (SELECT COUNT(*) FROM play_history WHERE songId = s.id) as playCount
                 FROM songs s
                 LEFT JOIN singers si ON s.singerID = si.id
                 WHERE s.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $songId);
        $stmt->execute();
        $currentSong = $stmt->get_result()->fetch_assoc();
        
        if (!$currentSong) return [];
        
        // Get all other songs
        $query = "SELECT s.*, si.name as singerName, si.id as singerID,
                        (SELECT COUNT(*) FROM play_history WHERE songId = s.id) as playCount
                 FROM songs s
                 LEFT JOIN singers si ON s.singerID = si.id
                 WHERE s.id != ?
                 LIMIT 100"; // Limit initial pool for performance
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $songId);
        $stmt->execute();
        $allSongs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Calculate comprehensive similarity scores
        $recommendations = [];
        foreach ($allSongs as $song) {
            // Title similarity
            $titleSimilarity = $this->calculateStringSimilarity(
                $currentSong['title'],
                $song['title']
            ) * $this->weightings['title_similarity'];
            
            // Artist similarity
            $artistSimilarity = ($currentSong['singerID'] === $song['singerID']) 
                ? $this->weightings['artist_similarity'] 
                : 0;
            
            // Simple genre matching (if genreID exists in your schema)
            $genreMatch = (isset($currentSong['genreID']) && 
                          isset($song['genreID']) && 
                          $currentSong['genreID'] === $song['genreID']) ? 0.2 : 0;
            
            // Popularity factor (normalized)
            $maxPlays = max(array_column($allSongs, 'playCount'));
            $popularityScore = $maxPlays > 0 
                ? ($song['playCount'] / $maxPlays) * $this->weightings['popularity']
                : 0;
            
            // Calculate total similarity score
            $totalSimilarity = $titleSimilarity + $artistSimilarity + 
                             $genreMatch + $popularityScore;
            
            $recommendations[] = [
                'id' => $song['id'],
                'title' => $song['title'],
                'filePath' => $song['filePath'],
                'imgPath' => $song['imgPath'],
                'singerID' => $song['singerID'],
                'singerName' => $song['singerName'],
                'similarity_score' => $totalSimilarity
            ];
        }
        
        // Sort by similarity score
        usort($recommendations, function($a, $b) {
            return $b['similarity_score'] <=> $a['similarity_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Track play history
     */
    public function recordPlayHistory($songName, $userId = null) {

        $query = "SELECT id FROM songs WHERE filePath = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $songName); // Use "s" for string
        $stmt->execute();
        $result = $stmt->get_result(); // Get the result set
        $songId = $result->fetch_assoc(); // Fetch the result as an associative array

        $query = "INSERT INTO play_history (songId, userId) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $songId['id'], $userId);
        return $stmt->execute();
    }
    
    /**
     * Get session-based recommendations
     */
    public function getSessionBasedRecommendations($limit = 5) {
        if (!isset($_SESSION['recently_played']) || empty($_SESSION['recently_played'])) {
            return $this->getPopularSongs($limit);
        }
        
        // Get recommendations based on last played song
        $lastPlayedId = end($_SESSION['recently_played']);
        $recommendations = $this->getSimilarSongs($lastPlayedId, $limit);
        
        // If we don't have enough recommendations, add some popular songs
        if (count($recommendations) < $limit) {
            $popularSongs = $this->getPopularSongs($limit - count($recommendations));
            $recommendations = array_merge($recommendations, $popularSongs);
        }
        
        return $recommendations;
    }
    
    /**
     * Get popular songs as fallback recommendations
     */
    private function getPopularSongs($limit = 5) {
        $query = "SELECT s.*, si.name as singerName, 
                        COUNT(ph.id) as playCount
                 FROM songs s
                 LEFT JOIN singers si ON s.singerID = si.id
                 LEFT JOIN play_history ph ON ph.songId = s.id
                 GROUP BY s.id
                 ORDER BY playCount DESC, s.dateAdded DESC
                 LIMIT ?";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}