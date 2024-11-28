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
// Step 3: Check if the $_GET['id'] exists; if it does, get the article record from the database and store it in the associative array $article. If an article with that ID does not exist, display "An article with that ID did not exist."
if (isset($_GET['id'])) {
    // Get user info from the database
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
    $stmt->execute([$_GET['id']]); 
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if article record exists
    if (!$article) {
        // If the article does not exist, set a message and redirect
        $_SESSION['messages'][] = "An article with that ID did not exist.";
        header('Location: articles.php');
        exit;

    }
 

    // Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the article id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the articles.php page.
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // Prepare and execute SQL DELETE statement
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            header('Location: articles.php');
            exit;
        } else {
            header('Location: articles.php');
            exit;
        }
    } 

} else {
    $_SESSION['messages'][] = 'No article ID was found.';
    header('Location: articles.php');
    exit;
}


?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete Article</h1>
    <p class="subtitle">Are you sure you want to delete the article: <?= $article['title'] ?></p>
    <div class="buttons">
        <a href="?id=<?= $article['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="articles.php" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->
<?php include 'templates/footer.php'; ?>