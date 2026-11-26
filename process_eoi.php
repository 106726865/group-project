<?php

declare(strict_types=1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: apply.php");
    exit();
}

$requiredPostFields = [
    "job_reference",
    "first_name",
    "last_name",
    "date_of_birth",
    "gender",
    "street_address",
    "suburb",
    "state",
    "postcode",
    "email",
    "phone",
];
foreach ($requiredPostFields as $field) {
    if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
        header("Location: apply.php");
        exit();
    }
}

require_once __DIR__ . "/settings.php";

$job_reference = trim(stripslashes($_POST["job_reference"]));
$first_name = trim(stripslashes($_POST["first_name"]));
$last_name = trim(stripslashes($_POST["last_name"]));
$date_of_birth = trim(stripslashes($_POST["date_of_birth"]));
$gender = trim(stripslashes($_POST["gender"]));
$street_address = trim(stripslashes($_POST["street_address"]));
$suburb = trim(stripslashes($_POST["suburb"]));
$state = trim(stripslashes($_POST["state"]));
$postcode = trim(stripslashes($_POST["postcode"]));
$email = trim(stripslashes($_POST["email"]));
$phone = trim(stripslashes($_POST["phone"]));
$other_skills =
    isset($_POST["other_skills"]) && is_string($_POST["other_skills"])
        ? trim(stripslashes($_POST["other_skills"]))
        : "";

$errors = [];

if (!preg_match('/^[A-Za-z0-9]{5}$/', $job_reference)) {
    $errors[] = "Job reference number must be exactly 5 letters or numbers.";
}
if (!preg_match('/^[A-Za-z]{1,20}$/', $first_name)) {
    $errors[] = "First name must contain 1 to 20 letters only.";
}
if (!preg_match('/^[A-Za-z]{1,20}$/', $last_name)) {
    $errors[] = "Last name must contain 1 to 20 letters only.";
}

$dateParts = [];
if (
    !preg_match(
        '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/',
        $date_of_birth,
        $dateParts,
    ) ||
    !checkdate((int) $dateParts[2], (int) $dateParts[1], (int) $dateParts[3])
) {
    $errors[] = "Date of birth must be a real date in DD/MM/YYYY format.";
}

$allowedGenders = ["female", "male", "non-binary", "prefer-not-to-say"];
if (!in_array($gender, $allowedGenders, true)) {
    $errors[] = "Select a valid gender.";
}

if ($street_address === "" || strlen($street_address) > 40) {
    $errors[] = "Street address must be between 1 and 40 characters.";
}
if ($suburb === "" || strlen($suburb) > 40) {
    $errors[] = "Suburb / town must be between 1 and 40 characters.";
}

$allowedStates = ["VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT"];
if (!in_array($state, $allowedStates, true)) {
    $errors[] = "Select a valid state.";
}

if (!preg_match('/^[0-9]{4}$/', $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
}
if (
    filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
    strlen($email) > 100
) {
    $errors[] = "Enter a valid email address (100 characters or fewer).";
}
if (!preg_match('/^[0-9]{8,12}$/', $phone)) {
    $errors[] =
        "Phone number must be 8 to 12 digits with no spaces or symbols.";
}

$allowedSkills = [
    "ux-research",
    "prototyping",
    "ui-design",
    "html-css",
    "javascript",
    "motion-graphics",
];
$selectedSkills = [];
if (
    !isset($_POST["skills"]) ||
    !is_array($_POST["skills"]) ||
    $_POST["skills"] === []
) {
    $errors[] = "Select at least one skill.";
} else {
    foreach ($_POST["skills"] as $skill) {
        if (!is_string($skill)) {
            continue;
        }
        $skill = trim(stripslashes($skill));
        if (!in_array($skill, $allowedSkills, true)) {
            $errors[] = "An invalid skill was selected.";
            break;
        }
        if (!in_array($skill, $selectedSkills, true)) {
            $selectedSkills[] = $skill;
        }
    }
    if ($selectedSkills === []) {
        $errors[] = "Select at least one valid skill.";
    }
}

if (strlen($other_skills) > 1000) {
    $errors[] = "Other skills must be 1000 characters or fewer.";
}

if ($errors !== []) {
    $pageTitle = "CLAZITERIUM | Application error";
    $pageStyles = ["apply-style.css"];
    $inlineStyle = "";
    $bodyClass = "";

    include __DIR__ . "/includes/head.inc";
    include __DIR__ . "/includes/header.inc";

    $errorItems = "";
    foreach ($errors as $error) {
        $errorItems .= "      <li>" . htmlspecialchars($error) . "</li>" . "\n";
    }

    echo <<<HTML
      <main class="application-page">
        <header class="application-heading">
          <h1>APPLICATION NOT SENT<span>.</span></h1>
          <p class="application-intro">Please correct the problems below and submit the form again.</p>
        </header>
        <ul class="form-errors">
    {$errorItems}    </ul>
        <p><a href="apply.php">Back to the application form</a></p>
      </main>
    HTML;

    include __DIR__ . "/includes/footer.inc";
    exit();
}

$job_reference = htmlspecialchars($job_reference);
$first_name = htmlspecialchars($first_name);
$last_name = htmlspecialchars($last_name);
$date_of_birth = htmlspecialchars($date_of_birth);
$gender = htmlspecialchars($gender);
$street_address = htmlspecialchars($street_address);
$suburb = htmlspecialchars($suburb);
$state = htmlspecialchars($state);
$postcode = htmlspecialchars($postcode);
$email = htmlspecialchars($email);
$phone = htmlspecialchars($phone);
$skills = htmlspecialchars(implode(", ", $selectedSkills));
$other_skills = htmlspecialchars($other_skills);

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_errno) {
    error_log("EOI database connection failed: " . $conn->connect_error);

    $pageTitle = "CLAZITERIUM | Application error";
    $pageStyles = ["apply-style.css"];
    $inlineStyle = "";
    $bodyClass = "";

    include __DIR__ . "/includes/head.inc";
    include __DIR__ . "/includes/header.inc";
    echo <<<HTML
      <main class="application-page">
        <header class="application-heading">
          <h1>TRY AGAIN LATER<span>.</span></h1>
        </header>
        <p>We could not save your application right now. Please try again later.</p>
        <p><a href="apply.php">Back to the application form</a></p>
      </main>
    HTML;
    include __DIR__ . "/includes/footer.inc";
    exit();
}

$conn->set_charset("utf8mb4");

$createTableSQL = "CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT NOT NULL AUTO_INCREMENT,
    job_reference VARCHAR(5) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    date_of_birth VARCHAR(10) NOT NULL,
    gender ENUM('female', 'male', 'non-binary', 'prefer-not-to-say') NOT NULL,
    street_address VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state ENUM('VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT') NOT NULL,
    postcode CHAR(4) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills VARCHAR(255) NOT NULL,
    other_skills TEXT,
    status ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New',
    PRIMARY KEY (EOInumber)
)";

if (!$conn->query($createTableSQL)) {
    error_log("EOI table creation failed: " . $conn->error);

    $pageTitle = "CLAZITERIUM | Application error";
    $pageStyles = ["apply-style.css"];
    $inlineStyle = "";
    $bodyClass = "";

    include __DIR__ . "/includes/head.inc";
    include __DIR__ . "/includes/header.inc";
    echo <<<HTML
      <main class="application-page">
        <header class="application-heading">
          <h1>TRY AGAIN LATER<span>.</span></h1>
        </header>
        <p>We could not save your application right now. Please try again later.</p>
        <p><a href="apply.php">Back to the application form</a></p>
      </main>
    HTML;
    include __DIR__ . "/includes/footer.inc";
    exit();
}

$insertSQL = 'INSERT INTO eoi (
        job_reference, first_name, last_name, date_of_birth, gender,
        street_address, suburb, state, postcode, email, phone, skills, other_skills
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = $conn->prepare($insertSQL);
if ($stmt === false) {
    error_log("EOI statement preparation failed: " . $conn->error);

    $pageTitle = "CLAZITERIUM | Application error";
    $pageStyles = ["apply-style.css"];
    $inlineStyle = "";
    $bodyClass = "";

    include __DIR__ . "/includes/head.inc";
    include __DIR__ . "/includes/header.inc";
    echo <<<HTML
      <main class="application-page">
        <header class="application-heading">
          <h1>TRY AGAIN LATER<span>.</span></h1>
        </header>
        <p>We could not save your application right now. Please try again later.</p>
        <p><a href="apply.php">Back to the application form</a></p>
      </main>
    HTML;
    include __DIR__ . "/includes/footer.inc";
    exit();
}

$stmt->bind_param(
    "sssssssssssss",
    $job_reference,
    $first_name,
    $last_name,
    $date_of_birth,
    $gender,
    $street_address,
    $suburb,
    $state,
    $postcode,
    $email,
    $phone,
    $skills,
    $other_skills,
);

if (!$stmt->execute()) {
    error_log("EOI insert failed: " . $stmt->error);
    $stmt->close();

    $pageTitle = "CLAZITERIUM | Application error";
    $pageStyles = ["apply-style.css"];
    $inlineStyle = "";
    $bodyClass = "";

    include __DIR__ . "/includes/head.inc";
    include __DIR__ . "/includes/header.inc";
    echo <<<HTML
      <main class="application-page">
        <header class="application-heading">
          <h1>TRY AGAIN LATER</h1>
        </header>
        <p>We could not save your application right now. Please try again later.</p>
        <p><a href="apply.php">Back to the application form</a></p>
      </main>
    HTML;
    include __DIR__ . "/includes/footer.inc";
    exit();
}

$eoiNumber = $stmt->insert_id;
$stmt->close();
$conn->close();

$pageTitle = "CLAZITERIUM | Application received";
$pageStyles = ["apply-style.css"];
$inlineStyle = "";
$bodyClass = "";

include __DIR__ . "/includes/head.inc";
include __DIR__ . "/includes/header.inc";
echo <<<HTML
  <main class="application-page">
    <header class="application-heading">
      <h1>APPLICATION RECEIVED</h1>
      <p class="application-intro">Thanks, {$first_name}. Your expression of interest has been saved.</p>
    </header>
    <section class="form-section">
      <h2>Your EOI number</h2>
      <p style="font-size: 2.5rem; font-weight: 700;">{$eoiNumber}</p>
      <p>Quote this number if you contact us about your application.</p>
      <p><a href="apply.php">Submit another application</a></p>
    </section>
  </main>
HTML;
include __DIR__ . "/includes/footer.inc";
