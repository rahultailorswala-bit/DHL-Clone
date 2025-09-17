<?php
require 'db.php';
$query = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $pdo->prepare("SELECT * FROM articles WHERE title LIKE ? OR content LIKE ?");
$stmt->execute(['%' . $query . '%', '%' . $query . '%']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = ['World', 'Technology', 'Sports', 'Entertainment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f4f4; color: #333; }
        header { background: #c00; color: white; padding: 20px; text-align: center; }
        header h1 { font-size: 2.5em; }
        nav { background: #333; padding: 10px; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        nav a:hover { color: #c00; }
        .search-bar { padding: 10px; text-align: center; }
        .search-bar input { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 5px; }
        .search-bar button { padding: 8px 15px; background: #c00; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .search-results { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .search-results h2 { font-size: 2em; color: #c00; margin-bottom: 20px; }
        .article-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .article-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); cursor: pointer; }
        .article-card img { width: 100%; height: 200px; object-fit: cover; }
        .article-card h3 { font-size: 1.5em; padding: 10px; }
        .article-card p { padding: 0 10px 10px; color: #666; }
        footer { background: #333; color: white; text-align: center; padding: 10px; margin-top: 20px; }
        @media (max-width: 768px) {
            nav a { display: block; margin: 10px 0; }
            .search-bar input { width: 100%; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Search Results</h1>
    </header>
    <nav>
        <a href="#" onclick="redirect('index.php')">Home</a>
        <?php foreach ($categories as $cat): ?>
            <a href="#" onclick="redirect('category.php?cat=<?php echo urlencode($cat); ?>')"><?php echo $cat; ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="search-bar">
        <input type="text" id="search" placeholder="Search articles..." value="<?php echo htmlspecialchars($query); ?>">
        <button onclick="searchArticles()">Search</button>
    </div>
    <div class="search-results">
        <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        <div class="article-grid">
            <?php if ($results): ?>
                <?php foreach ($results as $article): ?>
                    <div class="article-card" onclick="redirect('article.php?id=<?php echo $article['id']; ?>')">
                        <img src="<?php echo $article['image_url']; ?>" alt="Article Image">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($article['content']), 0, 100) . '...'; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No results found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 News Website. All rights reserved.</p>
    </footer>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
        function searchArticles() {
            const query = document.getElementById('search').value;
            if (query) {
                redirect('search.php?q=' + encodeURIComponent(query));
            }
        }
    </script>
</body>
</html>
