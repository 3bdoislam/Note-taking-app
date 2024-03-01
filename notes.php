<?php
session_start();
include('db.php');

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Fetch user's notes from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT id, content FROM notes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Add a note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addNote"])) {
    $content = htmlspecialchars($_POST["noteContent"]);

    $sql = "INSERT INTO notes (user_id, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();
    $stmt->close();
}

// Delete a note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteNote"])) {
    $note_id = $_POST["noteId"];

    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Edit a note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editNote"])) {
    $note_id = $_POST["noteId"];
    $content = htmlspecialchars($_POST["editedContent"]);

    $sql = "UPDATE notes SET content = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $content, $note_id, $user_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Taking App - Notes</title>
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

    <!-- Form to add a note -->
    <form method="post" action="">
        <label for="noteContent">Add a Note:</label>
        <textarea name="noteContent" required></textarea>
        <button type="submit" name="addNote">Add Note</button>
    </form>

    <!-- Display user's notes -->
    <ul>
        <?php
        while ($row = $result->fetch_assoc()) {
            $note_id = $row["id"];
            $content = $row["content"];
            echo "<li>$content 
                    <form method='post' action=''>
                        <input type='hidden' name='noteId' value='$note_id'>
                        <button type='submit' name='deleteNote'>Delete</button>
                    </form>
                    <form method='post' action=''>
                        <input type='hidden' name='noteId' value='$note_id'>
                        <textarea name='editedContent' required></textarea>
                        <button type='submit' name='editNote'>Edit</button>
                    </form>
                </li>";
        }
        ?>
    </ul>

    <form method="post" action="index.php">
        <button type="submit">Logout</button>
    </form>

</body>
</html>
