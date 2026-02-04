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

// 3. Finalize the leaderboard entry appending to data/leaderboard.txt
if (isset($_SESSION['nickname'])) {
    $filename = __DIR__ . "/data/leaderboard.txt";

    // Build an associative map of existing scores (case-insensitive keys)
    $scoreMap = [];
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode("|", $line);
            if (count($parts) == 2) {
                // Parse existing entry
                $existingName = trim($parts[0]);
                $existingScore = (int)trim($parts[1]);
                // Set to false initially to detect nicknames if found
                $found = false;
                foreach ($scoreMap as $k => $entry) {
                    // if case-insensitive name match with the existing entry, accumulate score
                    if (strcasecmp($entry['name'], $existingName) === 0) {
                        $scoreMap[$k]['score'] += $existingScore;
                        $found = true;
                        break;
                    }
                }

                // Add new entry if the nickname not found
                if (!$found) {
                    $scoreMap[] = ['name' => $existingName, 'score' => $existingScore];
                }
            }
        }
    }

    // Merge current user's score (case-insensitive, preserve original casing)
    $found = false;
    foreach ($scoreMap as $k => $entry) {
        if (strcasecmp($entry['name'], $nickname) === 0) {
            $filePrevious = $scoreMap[$k]['score'];
            $finalScore = max($filePrevious, (int)$overallScore);
            $scoreMap[$k]['score'] = $finalScore;
            $_SESSION['overall_score'] = $finalScore;
            $found = true;
            break;
        }
    }
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