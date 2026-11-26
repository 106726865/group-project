<?php
declare(strict_types=1);

session_set_cookie_params(["httponly" => true, "samesite" => "Lax"]);
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

if (empty($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
if (time() - ($_SESSION["last_activity"] ?? 0) > 1800) {
    header("Location: logout.php");
    exit();
}
$_SESSION["last_activity"] = time();

require_once __DIR__ . "/settings.php";

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_errno) {
    error_log("Manage DB connection failed: " . $conn->connect_error);
    die("The database is unavailable right now. Please try again later.");
}
$conn->set_charset("utf8mb4");

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$allowedStatuses = ["New", "Current", "Final"];
$sortFields = [
    "EOInumber" => "EOI number",
    "job_reference" => "Job reference",
    "first_name" => "First name",
    "last_name" => "Last name",
    "status" => "Status",
];

$message = "";
$messageIsError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf"], (string) ($_POST["csrf"] ?? ""))) {
        http_response_code(403);
        die("Invalid request token.");
    }
    $action = (string) ($_POST["action"] ?? "");

    if ($action === "delete") {
        $ref = trim(stripslashes((string) ($_POST["job_ref"] ?? "")));
        if (!preg_match('/^[A-Za-z0-9]{5}$/', $ref)) {
            $message =
                "Enter a valid job reference (exactly 5 letters or numbers).";
            $messageIsError = true;
        } else {
            $stmt = $conn->prepare("DELETE FROM eoi WHERE job_reference = ?");
            if ($stmt && $stmt->bind_param("s", $ref) && $stmt->execute()) {
                $message =
                    "Deleted " .
                    $stmt->affected_rows .
                    " EOI(s) for job reference " .
                    h($ref) .
                    ".";
            } else {
                $message = "Could not delete those records.";
                $messageIsError = true;
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    } elseif ($action === "status") {
        $id = (int) ($_POST["eoi_id"] ?? 0);
        $newStatus = (string) ($_POST["status"] ?? "");
        if ($id > 0 && in_array($newStatus, $allowedStatuses, true)) {
            $stmt = $conn->prepare(
                "UPDATE eoi SET status = ? WHERE EOInumber = ?",
            );
            if (
                $stmt &&
                $stmt->bind_param("si", $newStatus, $id) &&
                $stmt->execute()
            ) {
                $message = "EOI #" . $id . " is now " . h($newStatus) . ".";
            } else {
                $message = "Could not update that status.";
                $messageIsError = true;
            }
            if ($stmt) {
                $stmt->close();
            }
        } else {
            $message = "Invalid status update.";
            $messageIsError = true;
        }
    }
}

$mode = (string) ($_GET["mode"] ?? "all");
if (!in_array($mode, ["all", "job", "name"], true)) {
    $mode = "all";
}
$jobRef = trim(stripslashes((string) ($_GET["job_ref"] ?? "")));
$firstName = trim(stripslashes((string) ($_GET["first_name"] ?? "")));
$lastName = trim(stripslashes((string) ($_GET["last_name"] ?? "")));

$sortKey = (string) ($_GET["sort"] ?? "EOInumber");
if (!array_key_exists($sortKey, $sortFields)) {
    $sortKey = "EOInumber";
}
$dir = ($_GET["dir"] ?? "asc") === "desc" ? "DESC" : "ASC";

$where = [];
$types = "";
$params = [];
if ($mode === "job" && $jobRef !== "") {
    $where[] = "job_reference = ?";
    $types .= "s";
    $params[] = $jobRef;
}
if ($mode === "name") {
    if ($firstName !== "") {
        $where[] = "first_name LIKE ?";
        $types .= "s";
        $params[] = "%" . $firstName . "%";
    }
    if ($lastName !== "") {
        $where[] = "last_name LIKE ?";
        $types .= "s";
        $params[] = "%" . $lastName . "%";
    }
}

$sql =
    "SELECT EOInumber, job_reference, first_name, last_name, email, phone, skills, status FROM eoi" .
    ($where ? " WHERE " . implode(" AND ", $where) : "") .
    " ORDER BY $sortKey $dir, EOInumber ASC";

$rows = [];
$queryFailed = false;
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
    } else {
        $queryFailed = true;
    }
    $stmt->close();
} else {
    $queryFailed = true;
}
$conn->close();

$pageTitle = "CLAZITERIUM | Manage EOIs";
$pageStyles = ["manage.css"];
$inlineStyle = "";
$bodyClass = "";

include __DIR__ . "/includes/head.inc";
include __DIR__ . "/includes/header.inc";
?>

  <main class="manage-page">
    <header class="manage-heading">
      <h1>MANAGE EOIs</h1>
      <p>Signed in as <?= h(
          $_SESSION["admin"],
      ) ?> &middot; <a href="logout.php">Logout</a></p>
    </header>

    <?php if ($message !== ""): ?>
      <p class="manage-message<?= $messageIsError
          ? " manage-error"
          : "" ?>"><?= $message ?></p>
    <?php endif; ?>

    <section class="manage-section">
      <h2>Search and sort</h2>
      <form class="manage-filter" action="manage.php" method="get" novalidate>
        <fieldset>
          <legend>Show</legend>
          <label><input type="radio" name="mode" value="all" <?= $mode === "all"
              ? "checked"
              : "" ?>> All EOIs</label>
          <label><input type="radio" name="mode" value="job" <?= $mode === "job"
              ? "checked"
              : "" ?>> By job reference</label>
          <label><input type="radio" name="mode" value="name" <?= $mode ===
          "name"
              ? "checked"
              : "" ?>> By applicant name</label>
        </fieldset>

        <div class="form-field">
          <label for="job_ref">Job reference</label>
          <input id="job_ref" name="job_ref" type="text" value="<?= h(
              $jobRef,
          ) ?>">
        </div>
        <div class="form-field">
          <label for="first_name">First name</label>
          <input id="first_name" name="first_name" type="text" value="<?= h(
              $firstName,
          ) ?>">
        </div>
        <div class="form-field">
          <label for="last_name">Last name</label>
          <input id="last_name" name="last_name" type="text" value="<?= h(
              $lastName,
          ) ?>">
        </div>

        <div class="form-field">
          <label for="sort">Sort by</label>
          <select id="sort" name="sort">
            <?php foreach ($sortFields as $key => $label): ?>
              <option value="<?= h($key) ?>" <?= $sortKey === $key
    ? "selected"
    : "" ?>><?= h($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-field">
          <label for="dir">Order</label>
          <select id="dir" name="dir">
            <option value="asc" <?= $dir === "ASC"
                ? "selected"
                : "" ?>>Ascending</option>
            <option value="desc" <?= $dir === "DESC"
                ? "selected"
                : "" ?>>Descending</option>
          </select>
        </div>
        <button type="submit">Apply</button>
      </form>
    </section>

    <section class="manage-section">
      <h2>Results (<?= count($rows) ?>)</h2>
      <?php if ($queryFailed): ?>
        <p>No EOI data is available yet. Submit an application first so the table is created.</p>
      <?php elseif ($rows === []): ?>
        <p>No EOIs match your search.</p>
      <?php else: ?>
        <div class="table-scroll">
          <table class="manage-table">
            <thead>
              <tr>
                <th scope="col">EOI</th>
                <th scope="col">Job ref</th>
                <th scope="col">First name</th>
                <th scope="col">Last name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Skills</th>
                <th scope="col">Status</th>
                <th scope="col">Change status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= h($r["EOInumber"]) ?></td>
                  <td><?= h($r["job_reference"]) ?></td>
                  <td><?= h($r["first_name"]) ?></td>
                  <td><?= h($r["last_name"]) ?></td>
                  <td><?= h($r["email"]) ?></td>
                  <td><?= h($r["phone"]) ?></td>
                  <td><?= h($r["skills"]) ?></td>
                  <td><?= h($r["status"]) ?></td>
                  <td>
                    <form action="manage.php?<?= h(
                        $_SERVER["QUERY_STRING"] ?? "",
                    ) ?>" method="post" novalidate>
                      <input type="hidden" name="csrf" value="<?= h(
                          $_SESSION["csrf"],
                      ) ?>">
                      <input type="hidden" name="action" value="status">
                      <input type="hidden" name="eoi_id" value="<?= h(
                          $r["EOInumber"],
                      ) ?>">
                      <label class="visually-hidden" for="status-<?= h(
                          $r["EOInumber"],
                      ) ?>">Status for EOI <?= h($r["EOInumber"]) ?></label>
                      <select id="status-<?= h(
                          $r["EOInumber"],
                      ) ?>" name="status">
                        <?php foreach ($allowedStatuses as $s): ?>
                          <option value="<?= h($s) ?>" <?= $r["status"] === $s
    ? "selected"
    : "" ?>><?= h($s) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit">Update</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <section class="manage-section">
      <h2>Delete EOIs by job reference</h2>
      <form class="manage-filter" action="manage.php" method="post" novalidate
            onsubmit="return confirm('Delete ALL EOIs for this job reference? This cannot be undone.');">
        <input type="hidden" name="csrf" value="<?= h($_SESSION["csrf"]) ?>">
        <input type="hidden" name="action" value="delete">
        <div class="form-field">
          <label for="del_job_ref">Job reference</label>
          <input id="del_job_ref" name="job_ref" type="text">
        </div>
        <button type="submit" class="danger">Delete all</button>
      </form>
    </section>
  </main>

<?php include __DIR__ . "/includes/footer.inc"; ?>
