<?php
$db = new SQLite3("grupp.db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $emailQuery = "SELECT * FROM Users WHERE email = :email";
    $stmt = $db->prepare($emailQuery);
    $stmt->bindValue(':email', $email);
    $result = $stmt->execute();

    if ($result->fetchArray(SQLITE3_ASSOC)) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email"]);
    } else {
        echo json_encode(["status" => "success", "message" => "Email available"]);
    }

    $db->close();
}
?>