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
                $existingName = trim($parts[0]);
                $existingScore = (int)trim($parts[1]);
                $key = mb_strtolower($existingName);
                if (!isset($scoreMap[$key])) {
                    $scoreMap[$key] = ['name' => $existingName, 'score' => $existingScore];
                } else {
                    // In case the file has duplicates, accumulate them
                    $scoreMap[$key]['score'] += $existingScore;
                }
            }
        }
    }

    // Merge current user's score (case-insensitive)
    // Use file previous score as base and avoid decreasing a stored score if session appears stale
    $userKey = mb_strtolower($nickname);
    $filePrevious = $scoreMap[$userKey]['score'] ?? 0;
    $finalScore = max($filePrevious, (int)$overallScore);
    $scoreMap[$userKey] = ['name' => $nickname, 'score' => $finalScore];
    // Update session to reflect the canonical final score
    $_SESSION['overall_score'] = $finalScore;

    // Prepare lines to write back (one entry per username)
    // Sort by score descending for nice ordering
    uasort($scoreMap, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    $outLines = [];
    foreach ($scoreMap as $entry) {
        $outLines[] = $entry['name'] . "|" . $entry['score'];
    }

    // Write the canonicalized leaderboard back to the file with exclusive lock
    file_put_contents($filename, implode(PHP_EOL, $outLines) . PHP_EOL, LOCK_EX);

    // Ensure the file on disk is canonicalized and sorted for future reads
    // (This is idempotent because we already wrote the sorted content.)
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