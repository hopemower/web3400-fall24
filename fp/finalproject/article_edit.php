<?php
// Step 1: Include config.php file
include 'config.php';
// Step 2: Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect user to login page or display an error message
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}
// Step 3: Check if the update form was submitted. If so, update article details using an UPDATE SQL query.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepare the SQL UPDATE statement
    $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ? WHERE id = ?');

    // Execute the SQL UPDATE statement
    if ($stmt->execute([$title, $content, $id])) {
        $_SESSION['messages'][] = "The article was successfully updated.";
        header('Location: articles.php');
        exit;
    } else {
        $_SESSION['messages'][] = "An error occurred while updating the article.";
    }

}  else {
    // Step 4: Else it's an initial page request, fetch the article's current data from the database by preparing and executing a SQL statement that uses the article id from the query string (ex. $_GET['id'])
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Prepare the SQL SELECT statement to fetch the article
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

    }

}



?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Article</h1>
    <form action="" method="post">
        <!-- ID -->
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= $article['title'] ?>" required>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content" required><?= $article['content'] ?></textarea>
            </div>
        </div>
        <!-- Submit -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Article</button>
            </div>
            <div class="control">
                <a href="articles.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<?php include 'templates/footer.php'; ?>