<?php
require 'db.php';
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) {
    header("Location: index.php");
    exit;
}
$comment_stmt = $pdo->prepare("SELECT * FROM comments WHERE article_id = ?");
$comment_stmt->execute([$article_id]);
$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
$related_stmt = $pdo->prepare("SELECT * FROM articles WHERE category = ? AND id != ? LIMIT 3");
$related_stmt->execute([$article['category'], $article_id]);
$related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = ['World', 'Technology', 'Sports', 'Entertainment'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commenter_name'], $_POST['comment_text'])) {
    $commenter_name = $_POST['commenter_name'];
    $comment_text = $_POST['comment_text'];
    $stmt = $pdo->prepare("INSERT INTO comments (article_id, commenter_name, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$article_id, $commenter_name, $comment_text]);
    header("Location: article.php?id=$article_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
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
        .article { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .article img { width: 100%; max-width: 600px; height: auto; border-radius: 10px; margin-bottom: 20px; }
        .article h2 { font-size: 2em; color: #c00; margin-bottom: 10px; }
        .article-meta { color: #666; margin-bottom: 20px; }
        .article-content { line-height: 1.6; margin-bottom: 20px; }
        .related-articles { margin: 20px; }
        .related-articles h3 { font-size: 1.8em; color: #c00; margin-bottom: 15px; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .related-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); cursor: pointer; }
        .related-card img { width: 100%; height: 150px; object-fit: cover; }
        .related-card h4 { font-size: 1.2em; padding: 10px; }
        .comments { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .comments h3 { font-size: 1.8em; color: #c00; margin-bottom: 15px; }
        .comment-form { margin-bottom: 20px; }
        .comment-form input, .comment-form textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .comment-form button { padding: 10px 20px; background: #c00; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .comment { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #eee; }
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
        <?php foreach ($categories as $cat): ?>
            <a href="#" onclick="redirect('category.php?cat=<?php echo urlencode($cat); ?>')"><?php echo $cat; ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="search-bar">
        <input type="text" id="search" placeholder="Search articles...">
        <button onclick="searchArticles()">Search</button>
    </div>
    <div class="article">
        <h2><?php echo htmlspecialchars($article['title']); ?></h2>
        <div class="article-meta">
            By <?php echo htmlspecialchars($article['author']); ?> | <?php echo date('F j, Y', strtotime($article['publish_date'])); ?>
        </div>
        <img src="<?php echo $article['image_url']; ?>" alt="Article Image">
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>
    </div>
    <div class="related-articles">
        <h3>Related Articles</h3>
        <div class="related-grid">
            <?php foreach ($related_articles as $related): ?>
                <div class="related-card" onclick="redirect('article.php?id=<?php echo $related['id']; ?>')">
                    <img src="<?php echo $related['image_url']; ?>" alt="Related Article">
                    <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="comments">
        <h3>Comments</h3>
        <div class="comment-form">
            <form method="POST">
                <input type="text" name="commenter_name" placeholder="Your Name" required>
                <textarea name="comment_text" placeholder="Your Comment" required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        </div>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?php echo htmlspecialchars($comment['commenter_name']); ?></strong>
                <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                <small><?php echo date('F j, Y, g:i a', strtotime($comment['comment_date'])); ?></small>
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
