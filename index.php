<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM articles WHERE is_featured = TRUE LIMIT 2");
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = ['World', 'Technology', 'Sports', 'Entertainment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Home</title>
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
        .featured { padding: 20px; background: white; margin: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .featured h2 { font-size: 2em; margin-bottom: 20px; color: #c00; }
        .featured-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .article-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); cursor: pointer; }
        .article-card img { width: 100%; height: 200px; object-fit: cover; }
        .article-card h3 { font-size: 1.5em; padding: 10px; }
        .article-card p { padding: 0 10px 10px; color: #666; }
        .categories { margin: 20px; }
        .category-section { margin-bottom: 40px; }
        .category-section h2 { font-size: 1.8em; color: #c00; margin-bottom: 15px; }
        footer { background: #333; color: white; text-align: center; padding: 10px; margin-top: 20px; }
        @media (max-width: 768px) {
            nav a { display: block; margin: 10px 0; }
            .search-bar input { width: 100%; }
        }
    </style>
</head>
<body>
    <header>
        <h1>News Website</h1>
    </header>
    <nav>
        <a href="#" onclick="redirect('index.php')">Home</a>
        <?php foreach ($categories as $category): ?>
            <a href="#" onclick="redirect('category.php?cat=<?php echo urlencode($category); ?>')"><?php echo $category; ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="search-bar">
        <input type="text" id="search" placeholder="Search articles...">
        <button onclick="searchArticles()">Search</button>
    </div>
    <div class="featured">
        <h2>Featured News</h2>
        <div class="featured-grid">
            <?php foreach ($featured as $article): ?>
                <div class="article-card" onclick="redirect('article.php?id=<?php echo $article['id']; ?>')">
                    <img src="<?php echo $article['image_url']; ?>" alt="Article Image">
                    <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p><?php echo substr(htmlspecialchars($article['content']), 0, 100) . '...'; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="categories">
        <?php foreach ($categories as $category): ?>
            <div class="category-section">
                <h2><?php echo $category; ?></h2>
                <div class="featured-grid">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM articles WHERE category = ? LIMIT 3");
                    $stmt->execute([$category]);
                    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($articles as $article): ?>
                        <div class="article-card" onclick="redirect('article.php?id=<?php echo $article['id']; ?>')">
                            <img src="<?php echo $article['image_url']; ?>" alt="Article Image">
                            <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p><?php echo substr(htmlspecialchars($article['content']), 0, 100) . '...'; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
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
