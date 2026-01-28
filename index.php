<?php
session_start();

// Reset session if user goes to index.php?reset=1
if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['nickname'])) {
        echo "<script>alert('Nickname is required. Please enter your nickname.'); window.location.href='index.php';</script>";
        exit();
    }

    // Store nickname
    $_SESSION['nickname'] = trim($_POST['nickname']);

    // Only set score to 0 if not set yet (so points can be remembered)
    if (!isset($_SESSION['overall_score'])) {
        $_SESSION['overall_score'] = 0;
    }

    // Store selected topic
    $_SESSION['selected_topic'] = $_POST['topic'];

    // Go to ONE quiz page
    header('Location: quiz_category.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Game - Login</title>
</head>
<body>
    <h1>Welcome to the ISIT307 Quiz!</h1>

    <form method="post" action="index.php">
        <label>Enter Nickname:</label>
        <input type="text" name="nickname" required><br><br>

        <label>Select Topic:</label>
        <select name="topic">
            <option value="Animals">Animals</option>
            <option value="Environment">Environment</option>
        </select><br><br>

        <input type="submit" value="Start Quiz">
    </form>
</body>
</html>