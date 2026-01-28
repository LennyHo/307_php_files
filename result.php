<?php
/* Author: [Your Name]
   ID: [Your ID]
   Description: Calculates quiz results and updates cumulative score.
*/
session_start();

// 1. Get data from the quiz session and the form post
$selectedQuestions = $_SESSION['current_quiz'] ?? [];
$userAnswers = $_POST['ans'] ?? [];
$numCorrect = 0;
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

// 3. Scoring Formula from your diagram
$pointsThisRound = ($numCorrect * 2) - ($numIncorrect * 1);
$_SESSION['overall_score'] += $pointsThisRound;
?>

<!DOCTYPE html>
<html>

<body>
    <h2>Quiz Results</h2>
    <p>Correct Answers: <?php echo $numCorrect; ?></p>
    <p>Incorrect Answers: <?php echo $numIncorrect; ?></p>
    <p>Points for this round: <strong><?php echo $pointsThisRound; ?></strong></p>
    <hr>
    <h3>Total Cumulative Points: <?php echo $_SESSION['overall_score']; ?></h3>

    <p>
        <a href="quiz_category.php"><button>New Quiz</button></a>
        <a href="leaderboard.php"><button>Leaderboard</button></a>
        <a href="exit.php"><button>Exit</button></a>
    </p>
</body>

</html>