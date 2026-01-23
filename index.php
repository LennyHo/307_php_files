<?php
// Start the session.
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['nickname'])) {
        echo "<script>alert('Nickname is required. Please enter your nickname.'); window.location.href='index.php';</script>";
        exit();
    }
    // Set session variables based on POST data.
    $_SESSION['nickname'] = $nickname = trim($_POST['nickname']);
    $_SESSION['overall_score'] = 0;
    $_SESSION['selected_topic'] = $_POST['topic'];
    header('Location: quiz.php');
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
        <input type="text" name="nickname" required> <br><br>

        <label>Select Topic:</label>
        <select name="topic">
            <option value="Animals">Animals</option>
            <option value="Environment">Environment</option>
        </select> <br><br>

        <input type="submit" value="Start Quiz">
    </form>
</body>

</html>

<?php
?>