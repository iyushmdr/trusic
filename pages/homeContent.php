<?php
$conn = mysqli_connect('localhost', 'root', '', 'myspotify');
mysqli_set_charset($conn, "utf8");
if (!$conn) {
    echo mysqli_connect_error();
}
// Initialize recommendation system if not already initialized
if (!isset($recommender)) {
    require_once("./RecommendationSystem.php");
    $recommender = new ContentBasedRecommendation($conn);
}



// Get recommendations
$recommendations = $recommender->getSessionBasedRecommendations(10); // Get 3 recommended songs

// Get random songs if no recommendations available
if (empty($recommendations)) {
    $randomKeys = (count($songs) >= 10) ? array_rand($songs, 10) : array_keys($songs);
    $recommendations = array_map(function($key) use ($songs) {
        return $songs[$key];
    }, $randomKeys);
}
?>

<?php include('./components/navbar.php'); ?>

<?php
$getAllSongsQuery = "SELECT songs.id, songs.title title,
songs.filePath audio, songs.imgPath img,
singers.name singerName, singers.id singerID
FROM songs 
LEFT JOIN singers on singers.id = songs.singerID
ORDER BY songs.dateAdded DESC";

$result = mysqli_query($conn, $getAllSongsQuery);
$songs = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<!-- for logged in users -->
 <?php if(isset($_SESSION['username'])){ ?>

<!-- Recommended Songs Section -->
<section>
    <h1 class="sectionTitle">Recommend Songs</h1>
    <div class="cards-container">
    <div class="cards">
        <?php foreach ($recommendations as $song) : ?>
            <div class="card" data="<?php echo htmlspecialchars($song['id']); ?>">
                <div class="imgContainer">
                    <img src="<?php echo htmlspecialchars($song['imgPath'] ?? $song['img']); ?>" alt="">
                </div>
                <div class="cardInfo">
                    <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                    <h5><?php echo htmlspecialchars($song['singerName']); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</section>

<!-- New Songs Section -->
<section>
    <h1 class="sectionTitle">New Songs</h1>
    <div class="cards-container">
    <div class="cards">
        <?php 
        // Get the two most recent songs
        $recentSongs = array_slice($songs, 0, 4);
        foreach ($recentSongs as $song) : 
        ?>
            <div class="card" data="<?php echo htmlspecialchars($song['id']); ?>">
                <div class="imgContainer">
                    <img src="<?php echo htmlspecialchars($song['img']); ?>" alt="">
                </div>
                <div class="cardInfo">
                    <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                    <h5><?php echo htmlspecialchars($song['singerName']); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</section>

<?php }else{ ?>
    <!-- For Guest All Songs -->
<section>
    <h1 class="sectionTitle">All Songs</h1>
    <div class="cards-container">
    <div class="cards">
        <?php foreach ($songs as $song) : ?>
            <div class="card" data="<?php echo htmlspecialchars($song['id']); ?>">
                <div class="imgContainer">
                    <img src="<?php echo htmlspecialchars($song['imgPath'] ?? $song['img']); ?>" alt="">
                </div>
                <div class="cardInfo">
                    <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                    <h5><?php echo htmlspecialchars($song['singerName']); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
        </div>
</section>
        
<?php } ?>