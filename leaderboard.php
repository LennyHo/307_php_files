<?php
// ===============
// Leaderboard.php
// ===============

session_start();

// leaderboard data file path
$filename = __DIR__ . "/data/leaderboard.txt";

// Initialize data structures
$data = [];
$malformedLines = [];

// Read and parse the file into an associative array for processing and display.
if (file_exists($filename)) {
    // Read all lines from the leaderboard file and parse.
    $textLines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $scoreMap = [];
    // Parse each line into name and score, accumulating scores for duplicate names (case-insensitive)
    foreach ($textLines as $lineNum => $line) {
        // Each line format: Name | Score
        $parts = explode("|", $line);
        // Valid line should have exactly 2 parts
        if (count($parts) == 2) {
            // Process valid line
            $rawName = trim($parts[0]);
            $score = (int)trim($parts[1]);
            $found = false;
            foreach ($scoreMap as $currentScore => $entry) {
                // if case-insensitive name match with the existing entry, accumulate score
                if (strcasecmp($entry['name'], $rawName) === 0) {
                    $scoreMap[$currentScore]['score'] += $score;
                    $found = true;
                    break;
                }
            }
            //  Add new entry if the nickname not found
            if (!$found) {
                $scoreMap[] = ['name' => $rawName, 'score' => $score];
            }
            // Also add to data for display
        } else {
            // Record malformed line number for reporting or debugging
            $malformedLines[] = $lineNum + 1;
        }
    }

    // Sort stored leaderboard for case-insensitive or score descending
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
    // Sort by score High-Low
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
                    <!-- Sortable column headers -->
                    <tr>
                        <th><a href="leaderboard.php?sort=name">Name (Sort A-Z)</a></th>
                        <th><a href="leaderboard.php?sort=score">Score (Sort High-Low)</a></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display leaderboard entries -->
                    <?php foreach ($data as $entry): ?>
                        <tr>
                            <!-- Escape output to prevent XSS -->
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