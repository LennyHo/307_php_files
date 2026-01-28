<?php
session_start();

$filename = "data/leaderboard.txt";
$data = [];
$malformedLines = [];

// 1. Read and parse the file safely
if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $lineNum => $line) {
        $parts = explode("|", $line);
        if (count($parts) == 2) {
            // trim() removes accidental spaces around names or scores
            $data[] = [trim($parts[0]), (int)trim($parts[1])];
        } else {
            $malformedLines[] = $lineNum + 1;
        }
    }
}

// 2. Sorting Logic
$sortType = $_GET['sort'] ?? 'score';
if ($sortType == 'name') {
    usort($data, function ($a, $b) {
        return strcasecmp($a[0], $b[0]); // True A-Z sort
    });
} else {
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
    <title>Leaderboard page</title>
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