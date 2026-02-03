<?php
// ===============
// Leaderboard.php
// ===============

session_start();

$filename = __DIR__ . "/data/leaderboard.txt";
$data = [];
$malformedLines = [];

// 1. Read and parse the file safely
if (file_exists($filename)) {
    $textLines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $scoreMap = [];
    foreach ($textLines as $lineNum => $line) {
        $parts = explode("|", $line);
        if (count($parts) == 2) {
            $rawName = trim($parts[0]);
            $score = (int)trim($parts[1]);
            $found = false;
            foreach ($scoreMap as $currentScore => $entry) {
                if (strcasecmp($entry['name'], $rawName) === 0) {
                    $scoreMap[$currentScore]['score'] += $score;
                    $found = true;
                    break;
                }
            }
            //  Add new entry if not found
            if (!$found) {
                $scoreMap[] = ['name' => $rawName, 'score' => $score];
            }
            // Also add to data for display
        } else {
            // 
            $malformedLines[] = $lineNum + 1;
        }
    }
    // Sort stored leaderboard for case-insensitive uniqueness or score descending
    usort($scoreMap, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    // one entry per username, sorted by score descending for canonical storage.
    $canonicalLines = [];
    foreach ($scoreMap as $entry) {
        $canonicalLines[] = $entry['name'] . "|" . $entry['score'];
        $data[] = [$entry['name'], $entry['score']];
    }
    // If file needs updating, write back sorted canonical content
    file_put_contents($filename, implode(PHP_EOL, $canonicalLines) . PHP_EOL, LOCK_EX);
}

// 2. Sort the data based on user selection.
$sortType = $_GET['sort'] ?? 'score';
if ($sortType == 'name') {
    // Sort by name A-Z
    usort($data, function ($a, $b) {
        return strcasecmp($a[0], $b[0]); // Case-insensitive alphabetical order
    });
} else {
    // Default: Sort by score High-Low
    usort($data, function ($a, $b) {
        return (int)$b[1] - (int)$a[1]; // Highest score first
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1 style="text-align:center;">Quiz Leaderboard</h1>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><a href="leaderboard.php?sort=name">Name (Sort A-Z)</a></th>
                        <th><a href="leaderboard.php?sort=score">Score (Sort High-Low)</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry[0]); ?></td>
                            <td><?php echo htmlspecialchars($entry[1]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="menu" style="margin-top:24px;">
            <a href="index.php"><button type="button">Start a new game</button></a>
            <a href="exit.php"><button type="button">Exit</button></a>
        </div>
    </div>
</body>

</html>