<?php
session_start();

// 1. Reset session if user goes to index.php?reset=1
if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['nickname'])) {
        echo "<script>alert('Nickname is required. Please enter your nickname.'); window.location.href='index.php';</script>";
        exit();
    }

    // Capture and clean nickname
    $_SESSION['nickname'] = trim($_POST['nickname']);
    echo "<script>alert('Welcome, " . htmlspecialchars($_SESSION['nickname']) . "! Get ready for the quiz.');</script>";

    // Initialize overall_score only if it doesn't exist yet
    if (!isset($_SESSION['overall_score'])) {
        $_SESSION['overall_score'] = 0;
    }

    // Store selected topic for the quiz logic
    $_SESSION['selected_topic'] = $_POST['topic'];

    // Define the path to your data folder for global use
    $_SESSION['data_path'] = "data/";

    header('Location: quiz_category.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Game - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 style="text-align:center;">Welcome to the ISIT307 Quiz!</h1>
        <form method="post" action="index.php">
            <label for="nickname">Enter Nickname:</label>
            <input type="text" id="nickname" name="nickname" required placeholder="Your nickname">

            <label for="topic">Select Topic:</label>
            <select id="topic" name="topic">
                <option value="Animals">Animals</option>
                <option value="Environment">Environment</option>
            </select>

            <input type="submit" value="Start Quiz">
        </form>
        <!-- Menu removed as requested -->
    </div>
</body>
</html>