<?php
// Step 1: Include config.php file
include 'config.php';
// Step 2: Check if the $_GET['id'] exists; if it does, get the article record from the database and store it in the associative array named $article.
// SQL example: SELECT articles.*, users.full_name AS author FROM articles JOIN users ON articles.author_id = users.id WHERE is_published = 1 AND articles.id = ?
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT articles.*, users.full_name AS author FROM articles JOIN users ON articles.author_id = users.id WHERE is_published = 1 AND articles.id = ?');
    
    // execute the statement with the provided article ID
    $stmt->execute([$_GET['id']]); 
    $article = $stmt->fetch();

    // Step 3: If an article with that ID does not exist, display the message "An article with that ID did not exist."
    if (!$article) {
        // If the article does not exist, set a message and redirect
        $_SESSION['messages'][] = "An article with that ID did not exist.";
        exit;
    }
}    


?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title"><?= $article['title'] ?></h1>
    <div class="box">
        <article class="media">
            <figure class="media-left">
                <p class="image is-128x128">
                    <img src="https://picsum.photos/128?random=<?= $article['id']?>">
                </p>
            </figure>
            <div class="media-content">
                <div class="content">
                    <p>
                        <?= $article['content'] ?>
                    </p>
                    <p>
                        <small><strong>Author: <?= $article['author'] ?></strong>
                            | Published: <?= time_ago($article['created_at']) ?>
                            <?php if ($article['modified_on'] !== NULL) : ?>
                                | Updated: <?= time_ago($article['modified_on']) ?>
                            <?php endif; ?>
                        </small>
                    </p>
                </div>
                <p class="buttons">
                    <a href="contact.php" class="button is-small is-info is-rounded">
                        <span class="icon">
                            <i class="fas fa-lg fa-hiking"></i>
                        </span>
                        <span><strong>Begin your journey now</strong></span>
                    </a>
                </p>
                <p class="buttons">
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                        <span><?= $article['likes_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-star"></i>
                        </span>
                        <span><?= $article['favs_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-comment"></i>
                        </span>
                        <span><?= $article['comments_count'] ?></span>
                    </a>
                </p>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>