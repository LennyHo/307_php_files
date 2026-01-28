<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// DEBUG: show request + session quickly (temporary)
echo "<pre>";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST ans exists? " . (isset($_POST['ans']) ? "YES" : "NO") . "\n";
echo "SESSION current_quiz exists? " . (isset($_SESSION['current_quiz']) ? "YES" : "NO") . "\n";
echo "</pre>";

// If no quiz data, show message (never blank)
if (!isset($_SESSION['current_quiz'])) {
    echo "<h3>No quiz data found.</h3>";
    echo "<p>Go back and start the quiz again.</p>";
    echo '<a href="index.php">Back to index</a>';
    exit();
}

$quiz = $_SESSION['current_quiz'];
$userAnswers = $_POST['ans'] ?? [];

$correct = 0;
$incorrect = 0;

foreach ($quiz as $i => $q) {
    $expected = strtolower(trim($q['answer']));
    $given = strtolower(trim($userAnswers[$i] ?? ''));

    if ($given !== '' && $given === $expected) {
        $correct++;
    } else {
        $incorrect++;
    }
}

$quizPoints = ($correct * 2) - ($incorrect * 1);

if (!isset($_SESSION['overall_score'])) {
    $_SESSION['overall_score'] = 0;
}
$_SESSION['overall_score'] += $quizPoints;

// prevent refresh double-add
unset($_SESSION['current_quiz']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz Results Page</title>
</head>
<body>
  <h2>Quiz Result</h2>

  <p><strong>Correct:</strong> <?php echo $correct; ?></p>
  <p><strong>Incorrect:</strong> <?php echo $incorrect; ?></p>
  <p><strong>Points this quiz:</strong> <?php echo $quizPoints; ?></p>
  <p><strong>Total points:</strong> <?php echo $_SESSION['overall_score']; ?></p>

  <a href="quiz_category.php"><button>New Quiz</button></a>
  <a href="leaderboard.php"><button>Leaderboard</button></a>
  <a href="exit.php"><button>Exit</button></a>
</body>
</html>