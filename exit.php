<?php
// exit.php
session_start();

// 1. retreive session variables
$nickname = $_SESSION['nickname'] ?? 'Guest';
$overallScore = $_SESSION['overall_score'] ?? 0;

// 2. Handle the "Restart Game" button logic first
if (isset($_POST['restart'])) {
    // Clear all session data to allow a fresh start
    session_unset();
    session_destroy();
    // Redirect to the start page
    header("Location: index.php");
    exit();
}

// 3. finalize the leaderboard entry (only if not restarting)
if (isset($_SESSION['nickname'])) {
    $entry = $nickname . "|" . $overallScore . PHP_EOL;
    file_put_contents("data/leaderboard.txt", $entry, FILE_APPEND | LOCK_EX);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exit page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 style="text-align:center;">Game over!</h1>
        <p>Name: <strong><?php echo htmlspecialchars($nickname); ?></strong></p>
        <p>Overall Score: <strong><?php echo htmlspecialchars($overallScore); ?></strong></p>
        <form method="post" style="text-align:center; margin-top:24px;">
            <button type="submit" name="restart">Restart Game</button>
        </form>
    </div>
</body>
</html>

<?php
?>