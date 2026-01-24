<?php
session_start();

$filename = "leaderboard.txt";
$data = [];

// 1. Read and parse the file safely
if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode("|", $line);
        if (count($parts) == 2) {
            // trim() removes accidental spaces around names or scores
            $data[] = [trim($parts[0]), (int)trim($parts[1])];
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
<html>

<head>
    <title>Quiz Leaderboard</title>
</head>

<body>
    <h1>Quiz Leaderboard</h1>
    <table border="1">
        <tbody>
            <?php foreach ($data as $entry): ?>
                <tr>
                    <td><?php echo htmlspecialchars($entry[0]); ?></td>
                    <td><?php echo htmlspecialchars($entry[1]); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th><a href="leaderboard.php?sort=name">Name (Sort A-Z)</a></th>
                <th><a href="leaderboard.php?sort=score">Score (Sort High-Low)</a></th>
            </tr>
        </tbody>
    </table>
    <br>
    <a href="index.php">Restart Game</a>
</body>

</html>