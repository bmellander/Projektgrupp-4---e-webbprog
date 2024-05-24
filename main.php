<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$db = new SQLite3("grupp.db");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'publish' && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['category'])) {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $categoryID = intval($_POST['category']);
        $userID = $_SESSION['userID'];

        $stmt = $db->prepare("INSERT INTO Adverts (userID, title, description, categoryID) VALUES (:userID, :title, :description, :categoryID)");
        $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':categoryID', $categoryID, SQLITE3_INTEGER);
        $stmt->execute();
    }

    if ($_POST['action'] == 'fetch') {
        $categoryFilter = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $query = "SELECT Adverts.*, Users.username, Categories.name as categoryName FROM Adverts JOIN Users ON Adverts.userID = Users.userID JOIN Categories ON Adverts.categoryID = Categories.id";
        if ($categoryFilter > 0) {
            $query .= " WHERE Adverts.categoryID = :categoryID";
        }
        $query .= " ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        if ($categoryFilter > 0) {
            $stmt->bindValue(':categoryID', $categoryFilter, SQLITE3_INTEGER);
        }
        $adverts = $stmt->execute();
        $result = [];
        while ($row = $adverts->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $row;
        }
        echo json_encode($result);
        exit();
    }
}

$categories = $db->query("SELECT * FROM Categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <a href="logout.php" class="log-out-btn">Sign Out</a>
        <h2>Post a New Advert</h2>
        <form id="advert-form">
            <div class="form-group">
                <input type="text" name="title" placeholder="Advert Title" required>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Advert Description" required></textarea>
            </div>
            <div class="form-group">
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <input type="submit" value="Publish Advert">
        </form>

        <h2>Adverts</h2>
        <div class="form-group">
            <select id="category-filter">
                <option value="0">All Categories</option>
                <?php
                $categories->reset();
                while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="adverts" id="adverts"></div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchAdverts(category = 0) {
                $.post('main.php', { action: 'fetch', category: category }, function(data) {
                    const adverts = JSON.parse(data);
                    let advertsHtml = '';
                    adverts.forEach(advert => {
                        advertsHtml += `
                            <div class="advert">
                                <h3>${advert.title}</h3>
                                <p>${advert.description}</p>
                                <small>Posted by: ${advert.username} in ${advert.categoryName} on ${advert.created_at}</small>
                            </div>
                        `;
                    });
                    $('#adverts').html(advertsHtml);
                });
            }

            $('#advert-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize() + '&action=publish';
                $.post('main.php', formData, function() {
                    fetchAdverts();
                    $('#advert-form')[0].reset();
                });
            });

            $('#category-filter').on('change', function() {
                const category = $(this).val();
                fetchAdverts(category);
            });

            fetchAdverts();
        });
    </script>
</body>
</html>