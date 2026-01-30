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

// capture previous cumulative total from the leaderboard file (source of truth)
$previousTotal = 0;
$dataFile = __DIR__ . '/data/leaderboard.txt';
if (file_exists($dataFile)) {
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nickKey = mb_strtolower($_SESSION['nickname'] ?? '');
    foreach ($lines as $ln) {
        $parts = explode("|", $ln);
        if (count($parts) == 2) {
            $existingName = trim($parts[0]);
            $existingScore = (int)trim($parts[1]);
            if (mb_strtolower($existingName) === $nickKey) {
                $previousTotal = $existingScore;
                break;
            }
        }
    }
}

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

// Add to cumulative total (base on file's previous total)
$_SESSION['overall_score'] = $previousTotal + $pointsThisRound; // now session matches file-based total

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