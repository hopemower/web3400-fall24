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

// Step 3: Check if the $_GET['id'] exists; if it does, get the user the record from the database and store it in the associative array $user. If a user record with that ID does not exist, display the message "A user with that ID did not exist."
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id']; // Ensure the ID is an integer
    try {
        // Get user info from the database
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->execute([$user_id]); // Use $user_id here to fetch the correct user
        $user = $stmt->fetch();

        // Check if user record exists
        if (!$user) {
            // If the user does not exist, set a message and redirect
            $_SESSION['messages'][] = "A user with that ID did not exist.";
            header('Location: users_manage.php');
            exit;
        }
    } catch (PDOException $e) {
        // Handle any database errors (optional)
        die("Database error occurred: " . $e->getMessage());
    }
}

// Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the user id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the users_manage.php page.
if (isset($_GET['confirm'])) {
    if ($_GET['confirm'] === 'yes') {
        try {
            // Prepare and execute SQL DELETE statement
            $delete_stmt = $pdo->prepare("DELETE FROM `users` WHERE `id` = ?");
            $delete_stmt->execute([$user_id]);

            $_SESSION['messages'][] = "User account for {$user['full_name']} has been successfully deleted.";
        } catch (PDOException $e) {
            // Handle any database errors during deletion
            die("Database error occurred: " . $e->getMessage());
        }

        // Redirect back to users management page after deletion
        header('Location: users_manage.php');
        exit;
    } elseif ($_GET['confirm'] === 'no') {
        // If 'no' was clicked, redirect back to the management page
        header('Location: users_manage.php');
        exit;
    }
}


?>


<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete User Account</h1>
    <p class="subtitle">Are you sure you want to delete the user: <?= $user['full_name'] ?></p>
    <div class="buttons">
        <a href="?id=<?= $user['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="?id=<?= $user['id'] ?>&confirm=no" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->


<?php include 'templates/footer.php'; ?>