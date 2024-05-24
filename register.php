<html>
    <body>
        <?php
        session_start();
        $username = $email = $password = "";
        $db = new SQLite3("grupp.db");
        $validationError = false;
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $recaptchaSecret = 'YOUR_SECRET_KEY';
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Verify reCAPTCHA response
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse,
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($recaptchaData),
            ],
        ];
        $context = stream_context_create($options);
        $verify = file_get_contents($recaptchaUrl, false, $context);
        $captchaSuccess = json_decode($verify);

        if ($captchaSuccess->success == false) {
            echo "<script>alert('reCAPTCHA verification failed. Redirecting...');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
            exit();
        }
            if (empty($_POST["username"]) || empty($_POST["email"]) || empty($_POST["password"])) {
                exit();
            } else {
                $username = test_input($_POST["username"]);
                if (!preg_match("/.{4,}$/", $username)) {
                    $validationError = true;
                }
                
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validationError = true;
                }
                
                $password = test_input($_POST["password"]);
                if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/", $password)) {
                    $validationError = true;
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                }

                if (!$validationError) {
                    $insertQuery = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
                    $stmt = $db->prepare($insertQuery);
                    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
                    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
                    $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
                    $stmt->execute();
                    $newUserID = $db->lastInsertRowID();
    
                    $_SESSION['userID'] = $newUserID;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    header("Location: index.php");
                    exit();
                }
            }
        
            $db->close();
        }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        ?>
    </body>
</html>