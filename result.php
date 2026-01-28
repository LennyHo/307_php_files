<?php
session_start();

// 1. Get data from the quiz session and the form post
$selectedQuestions = $_SESSION['current_quiz'] ?? [];
// User's answers from the form submission
$userAnswers = $_POST['ans'] ?? [];
// Initialize counters
$numCorrect = 0;
// Initialize incorrect counter
$numIncorrect = 0;

// 2. The Grading Loop
foreach ($selectedQuestions as $i => $q) {
    $correct = strtolower(trim($q['answer']));
    // Check if the user actually answered the question
    $userProvided = isset($userAnswers[$i]) ? strtolower(trim($userAnswers[$i])) : "";

    if ($userProvided === $correct) {
        $numCorrect++;
    } else {
        $numIncorrect++;
    }
}

// 3. Scoring Formula: +2 for Correct, -1 for Incorrect
$pointsThisRound = ($numCorrect * 2) - ($numIncorrect * 1);
$_SESSION['overall_score'] = ($_SESSION['overall_score'] ?? 0) + $pointsThisRound;
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
        <p>Correct Answers: <?php echo $numCorrect; ?></p>
        <p>Incorrect Answers: <?php echo $numIncorrect; ?></p>
        <p>Points for this round: <strong><?php echo $pointsThisRound; ?></strong></p>
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
            <a href="leaderboard.php"><button type="button">Leaderboard</button></a>
            <a href="exit.php"><button type="button">Exit</button></a>
        </form>
    </div>
</body>

</html>