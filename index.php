<?php
session_start();
include('db.php');

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = sanitizeInput($_POST["username"]);
    $password = password_hash(sanitizeInput($_POST["password"]), PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();
}

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = sanitizeInput($_POST["username"]);
    $password = sanitizeInput($_POST["password"]);

    $sql = "SELECT id, username, password FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            header("Location: notes.php");
        } else {
            $loginError = "Invalid username or password";
        }
    } else {
        $loginError = "Invalid username or password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Note Taking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <section class="vh-100" id="login-container">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-7 col-lg-6 col-xl-5">
                    <img src="./pic/log.webp" class="img-fluid" alt="Cat image">
                </div>

                <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                    <form id="form-container" method="post" action="">
                        <div class="form-outline mb-4" id="login-form">
                            <input type="text" id="username" name="username" required class="form-control form-control-lg" />
                            <label class="form-label" for="username">Username</label>
                        </div>

                        <div class="form-outline mb-4" id="login-form">
                            <input type="password" id="password" name="password" required class="form-control form-control-lg" />
                            <label class="form-label" for="password">Password</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block" name="login">Login</button>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" name="register">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php
    if (isset($loginError)) {
        echo "<p>$loginError</p>";
    }
    ?>

</body>
</html>
