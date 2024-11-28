<?php
// Include config.php file
include 'config.php';
// Check if the user is logged in
if (!$_SESSION['loggedin']) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}
// If the form was submitted, insert a new ticket into the database and redirect back to the `ticket_create.php` for regular users and 'tickets.php' for admin users page with the message "The ticket was successfully added."
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, title, description, priority) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['priority']]);
        // Redirect based on user role
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: tickets.php');
            $_SESSION['messages'][] = "The ticket was successfully added.";
        } else {
            header('Location: ticket_create.php');
            $_SESSION['messages'][] = "The ticket was successfully added.";
        }
        exit;
}
?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>
<!-- BEGIN YOUR CONTENT -->
<?php
// Check if a success message is present in the query parameters
if (isset($_GET['msg'])) {
    echo '<div class="notification is-success">' . htmlspecialchars($_GET['msg']) . '</div>';
}
?>
<section class="section">
    <h1 class="title">Create Ticket</h1>
    <form action="" method="post">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" placeholder="Ticket title" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Description</label>
            <div class="control">
                <textarea class="textarea" name="description" placeholder="Ticket description" required></textarea>
            </div>
        </div>
        <div class="field">
            <label class="label">Priority</label>
            <div class="control">
                <div class="select">
                    <select name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Create Ticket</button>
            </div>
            <div class="control">
                <a href="tickets.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<?php include 'templates/footer.php'; ?>