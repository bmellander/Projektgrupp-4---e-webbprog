<?php
session_start();
$username = $email = $password = "";
$db = new SQLite3("grupp.db");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $searchResult = $db->prepare("SELECT * FROM Users WHERE username = :username");
    $searchResult->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $searchResult->execute();

    // Kollar om användarnamnet finns i databasen
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $stored_hashed_password = $row['password'];

        // Verifierar passwordet
        if (password_verify($password, $stored_hashed_password)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['email'] = $row['email'];
            header("Location: main.php");
            exit();


        } else {
            echo "<script>alert('Password does not match username. Redirecting...');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
            exit();
        }
    } else {
        echo "<script>alert('Username does not exist. Redirecting...');</script>";
        echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
        exit();
    }
    $searchResult->close();
    $db->close();
}
?>