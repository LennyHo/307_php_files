<?php
// leaderboard.php
session_start();

$lines = file("leaderboard", FILE_IGNORE_NEW_LINES);
$data = [];

foreach ($lines as $line) {
    $data[] = explode(",", $line);
}

$sortType = $_GET['sort'] ?? 'score';

if ($sortType == 'name') {
    // strcasecmp treats 'A' and 'a' as the same value
    usort($data, function($a, $b) {
        return strcasecmp($a[0], $b[0]);
    });
}else {
    // Default to score sorting
    usort($data, function ($a, $b) {
        return $b[1] - $a[1]; // Highest score first
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
</head>

<body>
    <table>
        <tr>
            <th><a href="leaderboard.php?sort=name">Name</a></th>
            <th><a href="leaderboard.php?sort=score">Score</a></th>
        </tr>
        <?php foreach ($data as $entry): ?>
            <tr>
                <td><?php echo htmlspecialchars($entry[0]); ?></td>
                <td><?php echo htmlspecialchars($entry[1]); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>