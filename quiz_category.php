<?php
// ==================
// quiz_category.php
// ==================
session_start();

// 1. Ensure user is logged in and has selected a topic.
if (!isset($_SESSION['nickname']) || !isset($_SESSION['selected_topic'])) {
    header("Location: index.php");
    exit();
}

// 2. Load questions from data/questions.txt based on selected topic.
if (isset($_POST['topic'])) {
    $_SESSION['selected_topic'] = $_POST['topic'];
}

// "Animals" or "Environment"
$topic = $_SESSION['selected_topic'];
$file  = __DIR__ . "/data/questions.txt";

//  Read and filter questions by topic
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Filter questions for the selected topic
$topicQuestions = [];

//  3. Parse questions and filter by topic
foreach ($lines as $line) {
    //  Each line format: Topic | Question | Answer
    $parts = explode("|", $line);
    if (count($parts) !== 3) continue;
    $qTopic = trim($parts[0]);
    $qText  = trim($parts[1]);
    $qAns   = trim($parts[2]);

    //  Only include questions matching the selected topic.
    if ($qTopic === $topic) {
        $topicQuestions[] = ["question" => $qText, "answer" => $qAns];
    }
}

// 4. Randomly select 4 questions for the quiz from questions.txt
shuffle($topicQuestions);
$selected = array_slice($topicQuestions, 0, 4);

// 3. Store answers for marking later
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
            <!-- Display selected questions -->
            <?php foreach ($selected as $i => $q): ?>
                <p><strong>Q<?php echo $i + 1; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></p>
                <!-- Input type depends on topic -->
                <?php if ($topic === "Animals"): ?>
                    <!-- if Animals topic, use text input -->
                    <input type="text" name="ans[<?php echo $i; ?>]" placeholder="Type your answer">
                <?php else: ?>
                    <!-- if Environment topic, use True/False radio buttons -->
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