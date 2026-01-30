<?php
// ========
// exit.php
// ========
session_start();

// 1. retreive session data for display and leaderboard entry.
$nickname = $_SESSION['nickname'] ?? 'Guest';
$overallScore = $_SESSION['overall_score'] ?? 0;

// 2. Handle Restart Game Request
if (isset($_POST['restart'])) {
    // Clear all session data to allow a fresh start
    session_unset();
    session_destroy();
    // Redirect to the start page (index.php)
    header("Location: index.php");
    exit();
}

// 3. Finalize the leaderboard entry appending to leaderboard.txt
if (isset($_SESSION['nickname'])) {
    $entry = $nickname . "|" . $overallScore . PHP_EOL;
    // Append the entry to leaderboard.txt
    file_put_contents("leaderboard.txt", $entry, FILE_APPEND | LOCK_EX);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exit Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1 style="text-align:center;">Game over!</h1>
        <!-- Display final stats -->
        <p>Name: <strong><?php echo htmlspecialchars($nickname); ?></strong></p>
        <!-- Display overall score -->
        <p>Overall Score: <strong><?php echo htmlspecialchars($overallScore); ?></strong></p>
        <form method="post" style="text-align:center; margin-top:24px;">
            <button type="submit" name="restart">Restart Game</button>
        </form>
    </div>
</body>

</html>

<?php
?>