<?php
include 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract and sanitize user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Variables used to check activation status and set name
    $activation_code = $user['activation_code'];
    $full_name = $user['full_name'];

    // Set account activation status
    $accountActivated = substr($activation_code, 0, 9) === 'activated' ? true : false;

    // If user exists and is activated and password is verified
    if ($user && $accountActivated && password_verify($password, $user['pass_hash'])) {
        
        // Update last login date/time in the database
        $updateStmt = $pdo->prepare("UPDATE `users` SET `last_login` = NOW() WHERE `id` = ?");
        $updateResult = $updateStmt->execute([$user['id']]);

        // Set session variables to indicate user is logged in
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['messages'][] = "Welcome back, {$user['full_name']}!";

        // Redirect to profile page or admin dashboard based on user role
        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: profile.php');
        }
        exit;
    } elseif ($user && !$accountActivated) {
        // Account exists but is not activated -> re-generate activation link.
        $activation_link = "register.php?code=$activation_code";

        // Create an activation link message
        $_SESSION['messages'][] = "$full_name Your account has not been activated. To activate your account, <a href='$activation_link'>click here</a>.";
        header('Location: login.php');
        exit;
    } else {
        // User does not exist or password is incorrect
        $_SESSION['messages'][] = "Invalid email or password. Please try again.";
        header('Location: login.php');
        exit;
    }
}
?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Login</h1>
    <form class="box" action="login.php" method="post">
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <!-- Password -->
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link">Login</button>
            </div>
        </div>
    </form>
    <a href="register.php" class="is-link"><strong>Create a new user account</strong></a>
</section>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
