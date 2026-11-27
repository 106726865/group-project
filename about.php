<?php
$databaseError = '';
$memberContributions = [];

require_once __DIR__ . '/settings.php';
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_errno) {
    error_log('About page database connection failed: ' . $conn->connect_error);
    $databaseError = 'Member contributions are temporarily unavailable.';
} else {
    if (!$conn->set_charset('utf8mb4')) {
        error_log('About page database charset failed: ' . $conn->error);
        $databaseError = 'Member contributions are temporarily unavailable.';
    } else {
        $createTableSQL = "CREATE TABLE IF NOT EXISTS member_contributions (
            member_name VARCHAR(100) NOT NULL,
            project_one_contribution TEXT NOT NULL,
            project_two_contribution TEXT NOT NULL,
            PRIMARY KEY (member_name)
        )";

        if (!$conn->query($createTableSQL)) {
            error_log('Member contributions table creation failed: ' . $conn->error);
            $databaseError = 'Member contributions are temporarily unavailable.';
        } else {
            $seedData = [
                ['Đăng', 'Director, UI/UX Designer, Advisor', 'Implemented PHP and database functionality in jobs.php.'],
                ['Tuấn Anh', 'UI/UX Designer', 'UI/UX Designer'],
                ['Đức Đạt', 'Project Manager', 'Project Manager'],
                ['Nhật Khang', 'Created the Jobs file.', 'Created settings.php and about.php in PHP.'],
            ];
            $seedStatement = $conn->prepare(
                'INSERT IGNORE INTO member_contributions '
                . '(member_name, project_one_contribution, project_two_contribution) '
                . 'VALUES (?, ?, ?)',
            );

            if (!$seedStatement) {
                error_log('Member contributions seed query failed: ' . $conn->error);
                $databaseError = 'Member contributions are temporarily unavailable.';
            } else {
                foreach ($seedData as [$memberName, $projectOne, $projectTwo]) {
                    $seedStatement->bind_param('sss', $memberName, $projectOne, $projectTwo);
                    if (!$seedStatement->execute()) {
                        error_log('Member contribution seed failed: ' . $seedStatement->error);
                        $databaseError = 'Member contributions are temporarily unavailable.';
                        break;
                    }
                }
                $seedStatement->close();
            }

            if ($databaseError === '') {
                $updateStatement = $conn->prepare(
                    'UPDATE member_contributions '
                    . 'SET project_one_contribution = ?, project_two_contribution = ? '
                    . 'WHERE member_name = ? AND project_one_contribution = ? '
                    . 'AND project_two_contribution = ?',
                );

                if (!$updateStatement) {
                    error_log('Member contribution update query failed: ' . $conn->error);
                    $databaseError = 'Member contributions are temporarily unavailable.';
                } else {
                    $legacyUpdates = [
                        [
                            'Nhật Khang',
                            'UI/UX Designer',
                            'UI/UX Designer',
                            'Created the Jobs file.',
                            'Created settings.php and about.php in PHP.',
                        ],
                        [
                            'Đăng',
                            'Director, UI/UX Designer, Advisor',
                            'Director, UI/UX Designer, Advisor',
                            'Director, UI/UX Designer, Advisor',
                            'Implemented PHP and database functionality in jobs.php.',
                        ],
                    ];

                    foreach ($legacyUpdates as [
                        $legacyMember,
                        $legacyProjectOne,
                        $legacyProjectTwo,
                        $projectOneContribution,
                        $projectTwoContribution,
                    ]) {
                        $updateStatement->bind_param(
                            'sssss',
                            $projectOneContribution,
                            $projectTwoContribution,
                            $legacyMember,
                            $legacyProjectOne,
                            $legacyProjectTwo,
                        );
                        if (!$updateStatement->execute()) {
                            error_log('Member contribution update failed: ' . $updateStatement->error);
                            $databaseError = 'Member contributions are temporarily unavailable.';
                            break;
                        }
                    }
                    $updateStatement->close();
                }
            }
        }

        if ($databaseError === '') {
            $result = $conn->query(
                'SELECT member_name, project_one_contribution, project_two_contribution '
                . 'FROM member_contributions',
            );

            if (!$result) {
                error_log('Member contributions query failed: ' . $conn->error);
                $databaseError = 'Member contributions are temporarily unavailable.';
            } else {
                while ($row = $result->fetch_assoc()) {
                    $memberContributions[$row['member_name']] = $row;
                }
                $result->free();
            }
        }
    }

    $conn->close();
}

$pageTitle   = 'CLAZITERIUM | About';
$pageStyles  = ['about-styles.css'];
$inlineStyle = '.site-header nav a { text-wrap: balance; }';

include __DIR__ . '/includes/head.inc';
include __DIR__ . '/includes/header.inc';
?>

  <main class="about-page">
    <section class="about-intro">
      <div class="about-copy">
        <h1>About us</h1>
        <p>We’re a creative agency making thoughtful websites, brands, and digital content for our clients.</p>
      </div>
      <dl class="group-details">
        <div><dt>Group</dt><dd>The Comp Sci Nerds (TCSN)</dd></div>
        <div><dt>Class day</dt><dd>Thursday</dd></div>
        <div><dt>Class time</dt><dd>7:00 AM – 10:00 AM</dd></div>
      </dl>
    </section>

    <section class="team-section">
      <div class="section-heading">
        <h2 id="team-heading">Our team</h2>
      </div>
      <figure>
        <img src="images/teampic.png" width="1200" height="675" alt="The CLAZITERIUM team" class="team-picture" style="filter: grayscale();">
        <figcaption>the group</figcaption>
      </figure>
      <h2 class="members-heading">Meet the members</h2>
      <?php if ($databaseError !== ''): ?>
        <p><?= htmlspecialchars($databaseError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <div class="member-list">
        <article class="member-card">
          <div class="member-image">
            <img src="images/dang.png" width="300" height="450" alt="Đăng">
            <div class="role-list">
              <p class="role">Director</p>
              <p class="role">UI/UX Designer</p>
              <p class="role">Advisor</p>
            </div>
          </div>
          <div class="member-details">
            <h3>Đăng</h3>
            <blockquote>
              <p>Đôi khi buông bỏ lại tốt hơn.</p>
              <p class="translate">Sometimes it's better to let go.</p>
              <p><strong>Project 1:</strong> <?= htmlspecialchars($memberContributions['Đăng']['project_one_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p><strong>Project 2:</strong> <?= htmlspecialchars($memberContributions['Đăng']['project_two_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </blockquote>
          </div>
        </article>

        <article class="member-card">
          <div class="member-image">
            <img src="images/ta.png" width="300" height="450" alt="Tuấn Anh">
            <div class="role-list">
              <p class="role">UI/UX Designer</p>
            </div>
          </div>
          <div class="member-details">
            <h3>Tuấn Anh</h3>
            <blockquote>
              <p>Không quan trọng bạn sinh ra ở đâu hay bắt đầu ở đâu. Điều quan trọng là bạn sẵn sàng vươn lên như thế nào.</p>
              <p class="translate">It doesn't matter where you were born or where you started. What matters is how hard you're willing to rise.</p>
              <p><strong>Project 1:</strong> <?= htmlspecialchars($memberContributions['Tuấn Anh']['project_one_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p><strong>Project 2:</strong> <?= htmlspecialchars($memberContributions['Tuấn Anh']['project_two_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </blockquote>
          </div>
        </article>

        <article class="member-card">
          <div class="member-image">
            <img src="images/dat.png" width="300" height="450" alt="Đức Đạt">
            <div class="role-list">
              <p class="role">Project Manager</p>
            </div>
          </div>
          <div class="member-details">
            <h3>Đức Đạt</h3>
            <blockquote>
              <p>Hãy mạnh mẽ, nhưng đủ mạnh mẽ để nhẹ nhàng.</p>
              <p class="translate">Be strong, but strong enough to be gentle.</p>
              <p><strong>Project 1:</strong> <?= htmlspecialchars($memberContributions['Đức Đạt']['project_one_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p><strong>Project 2:</strong> <?= htmlspecialchars($memberContributions['Đức Đạt']['project_two_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </blockquote>
          </div>
        </article>

        <article class="member-card">
          <div class="member-image">
            <img src="images/khang.png" width="300" height="450" alt="Nhật Khang">
            <div class="role-list">
              <p class="role">UI/UX Designer</p>
            </div>
          </div>
          <div class="member-details">
            <h3>Nhật Khang</h3>
            <blockquote>
              <p>Cách duy nhất để làm việc xuất sắc là yêu thích những gì bạn làm.</p>
              <p class="translate">The only way to do great work is to love what you do.</p>
              <p><strong>Project 1:</strong> <?= htmlspecialchars($memberContributions['Nhật Khang']['project_one_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p><strong>Project 2:</strong> <?= htmlspecialchars($memberContributions['Nhật Khang']['project_two_contribution'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </blockquote>
          </div>
        </article>
      </div>
    </section>

    <section class="facts-section">
      <h2 id="facts-heading">Fun facts</h2>
      <div class="thisgoob">
        <table>
          <thead>
            <tr>
              <th scope="col">Member</th>
              <th scope="col">Dream job</th>
              <th scope="col">Favorite dish</th>
              <th scope="col">Hometown</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row">Đạt</th><td>Software Engineer</td><td>Pizza</td><td>HCM city</td></tr>
            <tr><th scope="row">Khang</th><td>Cybersecurity Engineer &amp; Pen-tester</td><td>Japanese curry</td><td>HCM city</td></tr>
            <tr><th scope="row">Đăng</th><td>Software Security Engineer, Game Developer, VFX artist &amp; graphics programmer, jack of all trades</td><td>Water</td><td>Mars</td></tr>
            <tr><th scope="row">Tuấn Anh</th><td>Security Engineer</td><td>Hot Pot</td><td>Dong Nai province</td></tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.inc'; ?>
