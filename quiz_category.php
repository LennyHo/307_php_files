<?php
session_start();

if (!isset($_SESSION['nickname']) || !isset($_SESSION['selected_topic'])) {
    header("Location: index.php");
    exit();
}
    if (isset($_POST['topic'])) {
        $_SESSION['selected_topic'] = $_POST['topic'];
    }

$topic = $_SESSION['selected_topic']; // "Animals" or "Environment"
$file  = __DIR__ . "/data/questions.txt";

if (!file_exists($file)) {
    die("questions.txt not found in data folder.");
}

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$topicQuestions = [];
foreach ($lines as $line) {
    $parts = explode("|", $line);
    if (count($parts) !== 3) continue;

    $qTopic = trim($parts[0]);
    $qText  = trim($parts[1]);
    $qAns   = trim($parts[2]);

    if ($qTopic === $topic) {
        $topicQuestions[] = ["question" => $qText, "answer" => $qAns];
    }
}

if (count($topicQuestions) < 4) {
    die("Not enough questions for topic: " . htmlspecialchars($topic));
}

shuffle($topicQuestions);
$selected = array_slice($topicQuestions, 0, 4);

// store answers for marking later
$_SESSION['current_quiz'] = $selected;
$_SESSION['current_topic'] = $topic;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($topic); ?> Topic quiz page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2 style="text-align:center;"><?php echo htmlspecialchars($topic); ?> Topic Quiz</h2>
        <p style="text-align:center;">Player: <strong><?php echo htmlspecialchars($_SESSION['nickname']); ?></strong></p>
        <form method="post" action="result.php">
            <?php foreach ($selected as $i => $q): ?>
                <p><strong>Q<?php echo $i + 1; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></p>
                <?php if ($topic === "Animals"): ?>
                    <input type="text" name="ans[<?php echo $i; ?>]" placeholder="Type your answer">
                <?php else: ?>
                    <label><input type="radio" name="ans[<?php echo $i; ?>]" value="True"> True</label>
                    <label><input type="radio" name="ans[<?php echo $i; ?>]" value="False"> False</label>
                <?php endif; ?>
                <br><br>
            <?php endforeach; ?>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>