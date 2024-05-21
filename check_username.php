<?php
$db = new SQLite3("grupp.db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];

    $usernameQuery = "SELECT * FROM Users WHERE username = :username";
    $stmt = $db->prepare($usernameQuery);
    $stmt->bindValue(':username', $username);
    $result = $stmt->execute();

    if ($result->fetchArray(SQLITE3_ASSOC)) {
        echo json_encode(["status" => "error", "message" => "Username already exists"]);
    } else {
        echo json_encode(["status" => "success", "message" => "Username available"]);
    }

    $db->close();
    exit();
}
?>
