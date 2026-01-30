<?php
session_start();

// 1. Get data from the quiz session and the form post
$selectedQuestions = $_SESSION['current_quiz'] ?? [];
// User's answers from the form submission
$userAnswers = $_POST['ans'] ?? [];
// Ensure we have a selected topic to show in the radio buttons
$selectedTopic = $_SESSION['selected_topic'] ?? 'Animals';
// Initialize per-question points and total for this round
$pointsPerQuestion = [];
$pointsThisRound = 0;

// Use session as the source of truth for cumulative score
$previousTotal = $_SESSION['overall_score'] ?? 0;

// 2. The Grading Loop (calculate points per question)
foreach ($selectedQuestions as $i => $q) {
    $correct = strtolower(trim($q['answer']));
    // Check if the user actually answered the question
    $userProvided = isset($userAnswers[$i]) ? strtolower(trim($userAnswers[$i])) : "";

    // Assign points: +2 for correct, -1 for incorrect (including blank answers)
    if ($userProvided === $correct) {
        $pts = 2;
    } else {
        $pts = -1;
    }

    $pointsPerQuestion[$i] = $pts;
    $pointsThisRound += $pts;
}

// Add to cumulative total (base on session's previous total)
$_SESSION['overall_score'] = $previousTotal + $pointsThisRound;

// Immediately update the leaderboard file with the new cumulative score
$nickname = $_SESSION['nickname'] ?? 'Guest';
$filename = __DIR__ . '/data/leaderboard.txt';
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
                $scoreMap[$key]['score'] += $existingScore;
            }
        }
    }
}
$userKey = mb_strtolower($nickname);
$scoreMap[$userKey] = ['name' => $nickname, 'score' => $_SESSION['overall_score']];
uasort($scoreMap, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});
$outLines = [];
foreach ($scoreMap as $entry) {
    $outLines[] = $entry['name'] . '|' . $entry['score'];
}
file_put_contents($filename, implode(PHP_EOL, $outLines) . PHP_EOL, LOCK_EX);

// Sum positive (correct) and negative (incorrect) points for display
$correctPoints = 0;
$incorrectPoints = 0;
foreach ($pointsPerQuestion as $pts) {
    if ($pts > 0) {
        $correctPoints += $pts;
    } elseif ($pts < 0) {
        $incorrectPoints += $pts; // negative value
    }
}

// Count correct and incorrect answers (for display ordering)
$numCorrectCount = 0;
$numIncorrectCount = 0;
foreach ($pointsPerQuestion as $pts) {
    if ($pts > 0) {
        $numCorrectCount++;
    } elseif ($pts < 0) {
        $numIncorrectCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz results page</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h2 style="text-align:center;">Quiz Results</h2>
        <p><strong>Number of Correct:</strong> <?php echo $numCorrectCount; ?></p>
        <p><strong>Number of Incorrect:</strong> <?php echo $numIncorrectCount; ?></p>
        <p><strong>Points for this round:</strong> <strong><?php echo $pointsThisRound; ?></strong></p>
        <hr>
        <h3>Total Cumulative Points: <?php echo $_SESSION['overall_score']; ?></h3>
        <form action="quiz_category.php" method="post">
            <p>Select topic for your next quiz:</p>
            <label>
                <input type="radio" name="topic" value="Animals" <?php echo ($_SESSION['selected_topic'] == 'Animals') ? 'checked' : ''; ?>> Animals
            </label><br>
            <label>
                <input type="radio" name="topic" value="Environment" <?php echo ($_SESSION['selected_topic'] == 'Environment') ? 'checked' : ''; ?>> Environment
            </label>
            <br><br>
            <button type="submit">New Quiz</button>
        </form>
        <a href="leaderboard.php"><button type="button">Leaderboard</button></a>
        <a href="exit.php"><button type="button">Exit</button></a>
    </div>
</body>

</html>