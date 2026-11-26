<?php
declare(strict_types=1);

session_set_cookie_params(["httponly" => true, "samesite" => "Lax"]);
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
require_once __DIR__ . "/settings.php";

if (!empty($_SESSION["admin"])) {
    header("Location: manage.php");
    exit();
}

$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim(stripslashes((string) ($_POST["username"] ?? "")));
    $pass = (string) ($_POST["password"] ?? "");

    if (($_SESSION["lock_until"] ?? 0) > time()) {
        $error =
            "Too many failed attempts. Please wait a few minutes and try again.";
    } elseif ($username === "" || $pass === "") {
        $error = "Please enter both a username and a password.";
    } else {
        $conn = new mysqli($host, $user, $password, $database);
        if ($conn->connect_errno) {
            error_log("Login DB connection failed: " . $conn->connect_error);
            $error =
                "The service is unavailable right now. Please try again later.";
        } else {
            $conn->set_charset("utf8mb4");

            $conn->query("CREATE TABLE IF NOT EXISTS users (
                id INT NOT NULL AUTO_INCREMENT,
                username VARCHAR(50) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            )");

            $res = $conn->query("SELECT 1 FROM users LIMIT 1");
            if ($res && $res->num_rows === 0) {
                $seedUser = "admin";
                $seedHash = password_hash("admin", PASSWORD_DEFAULT);
                $seed = $conn->prepare(
                    "INSERT INTO users (username, password_hash) VALUES (?, ?)",
                );
                if ($seed) {
                    $seed->bind_param("ss", $seedUser, $seedHash);
                    $seed->execute();
                    $seed->close();
                }
            }

            $row = null;
            $stmt = $conn->prepare(
                "SELECT password_hash FROM users WHERE username = ?",
            );
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }
            $conn->close();

            $hash =
                $row["password_hash"] ??
                password_hash("not-a-real-password", PASSWORD_DEFAULT);
            if (password_verify($pass, $hash) && $row !== null) {
                session_regenerate_id(true);
                $_SESSION["admin"] = $username;
                $_SESSION["last_activity"] = time();
                $_SESSION["fails"] = 0;
                header("Location: manage.php");
                exit();
            }

            $_SESSION["fails"] = ($_SESSION["fails"] ?? 0) + 1;
            if ($_SESSION["fails"] >= 5) {
                $_SESSION["lock_until"] = time() + 300;
                $_SESSION["fails"] = 0;
            }
            $error = "Incorrect username or password.";
        }
    }
}

$pageTitle = "CLAZITERIUM | HR Login";
$pageStyles = ["manage-style.css"];
$inlineStyle = "";
$bodyClass = "";

include __DIR__ . "/includes/head.inc";
include __DIR__ . "/includes/header.inc";
?>

  <main class="manage-page">
    <header class="manage-heading">
      <h1>HR LOGIN<span>.</span></h1>
      <p>Sign in to manage expressions of interest.</p>
    </header>

    <?php if ($error !== ""): ?>
      <p class="manage-message manage-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="login-form" action="login.php" method="post" novalidate>
      <div class="form-field">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" value="<?= htmlspecialchars(
            $username,
        ) ?>">
      </div>
      <div class="form-field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password">
      </div>
      <button type="submit">Login</button>
    </form>
  </main>

<?php include __DIR__ . "/includes/footer.inc"; ?>
