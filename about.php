<?php
$pageTitle   = 'CLAZITERIUM | About';
$pageStyles  = ['about-styles.css'];
$inlineStyle = '.site-header nav a { text-wrap: balance; }';

require_once __DIR__ . '/settings.php';

$databaseError = '';
$members = [];

if (!function_exists('mysqli_report')) {
    error_log('About database unavailable: MySQLi extension is not loaded.');
    $databaseError = 'Team information is temporarily unavailable.';
} else {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_errno) {
        error_log('About database connection failed: ' . $conn->connect_error);
        $databaseError = 'Team information is temporarily unavailable.';
    } else {
        $conn->set_charset('utf8mb4');

        $createTableSQL = "CREATE TABLE IF NOT EXISTS about_members (
            member_id VARCHAR(30) NOT NULL,
            member_name VARCHAR(100) NOT NULL,
            image_name VARCHAR(255) NOT NULL,
            roles TEXT NOT NULL,
            quote_vietnamese TEXT NOT NULL,
            quote_translation TEXT NOT NULL,
            dream_job TEXT NOT NULL,
            favorite_dish VARCHAR(150) NOT NULL,
            hometown VARCHAR(150) NOT NULL,
            fun_fact_name VARCHAR(100) NOT NULL,
            first_project_contribution TEXT NOT NULL,
            second_project_contribution TEXT NOT NULL,
            PRIMARY KEY (member_id)
        )";

        if (!$conn->query($createTableSQL)) {
            error_log('About members table creation failed: ' . $conn->error);
            $databaseError = 'Team information is temporarily unavailable.';
        } else {
            $memberData = [
                [
                    'dang',
                    'Đăng',
                    'dang.png',
                    "Director\nUI/UX Designer\nAdvisor",
                    'Đôi khi buông bỏ lại tốt hơn.',
                    "Sometimes it's better to let go.",
                    'Software Security Engineer, Game Developer, VFX artist & graphics programmer, jack of all trades',
                    'Water',
                    'Mars',
                    'Đăng',
                ],
                [
                    'tu-anh',
                    'Tuấn Anh',
                    'ta.png',
                    'UI/UX Designer',
                    'Không quan trọng bạn sinh ra ở đâu hay bắt đầu ở đâu. Điều quan trọng là bạn sẵn sàng vươn lên như thế nào.',
                    "It doesn't matter where you were born or where you started. What matters is how hard you're willing to rise.",
                    'Security Engineer',
                    'Hot Pot',
                    'Dong Nai province',
                    'Tuấn Anh',
                ],
                [
                    'duc-dat',
                    'Đức Đạt',
                    'dat.png',
                    'Project Manager',
                    'Hãy mạnh mẽ, nhưng đủ mạnh mẽ để nhẹ nhàng.',
                    'Be strong, but strong enough to be gentle.',
                    'Software Engineer',
                    'Pizza',
                    'HCM city',
                    'Đạt',
                ],
                [
                    'nhat-khang',
                    'Nhật Khang',
                    'khang.png',
                    'UI/UX Designer',
                    'Cách duy nhất để làm việc xuất sắc là yêu thích những gì bạn làm.',
                    'The only way to do great work is to love what you do.',
                    'Cybersecurity Engineer & Pen-tester',
                    'Japanese curry',
                    'HCM city',
                    'Khang',
                ],
            ];

            $insertSQL = 'INSERT INTO about_members (
                member_id, member_name, image_name, roles, quote_vietnamese,
                quote_translation, dream_job, favorite_dish, hometown, fun_fact_name,
                first_project_contribution, second_project_contribution
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "", "")
            ON DUPLICATE KEY UPDATE
                member_name = VALUES(member_name),
                image_name = VALUES(image_name),
                roles = VALUES(roles),
                quote_vietnamese = VALUES(quote_vietnamese),
                quote_translation = VALUES(quote_translation),
                dream_job = VALUES(dream_job),
                favorite_dish = VALUES(favorite_dish),
                hometown = VALUES(hometown),
                fun_fact_name = VALUES(fun_fact_name)';

            $insertStatement = $conn->prepare($insertSQL);

            if ($insertStatement === false) {
                error_log('About members seed statement preparation failed: ' . $conn->error);
                $databaseError = 'Team information is temporarily unavailable.';
            } else {
                $insertStatement->bind_param(
                    'ssssssssss',
                    $memberId,
                    $memberName,
                    $imageName,
                    $roles,
                    $quoteVietnamese,
                    $quoteTranslation,
                    $dreamJob,
                    $favoriteDish,
                    $hometown,
                    $funFactName
                );

                foreach ($memberData as $member) {
                    [
                        $memberId,
                        $memberName,
                        $imageName,
                        $roles,
                        $quoteVietnamese,
                        $quoteTranslation,
                        $dreamJob,
                        $favoriteDish,
                        $hometown,
                        $funFactName,
                    ] = $member;

                    if (!$insertStatement->execute()) {
                        error_log('About members seed insert failed: ' . $insertStatement->error);
                        $databaseError = 'Team information is temporarily unavailable.';
                        break;
                    }
                }
                $insertStatement->close();
            }

            if ($databaseError === '') {
                $selectStatement = $conn->prepare(
                    'SELECT member_id, member_name, image_name, roles,
                        quote_vietnamese, quote_translation, dream_job, favorite_dish,
                        hometown, fun_fact_name, first_project_contribution,
                        second_project_contribution
                    FROM about_members
                    ORDER BY FIELD(member_id, "dang", "tu-anh", "duc-dat", "nhat-khang")'
                );

                if ($selectStatement === false) {
                    error_log('About members select preparation failed: ' . $conn->error);
                    $databaseError = 'Team information is temporarily unavailable.';
                } else {
                    if (!$selectStatement->execute()) {
                        error_log('About members select failed: ' . $selectStatement->error);
                        $databaseError = 'Team information is temporarily unavailable.';
                    } else {
                        $result = $selectStatement->get_result();
                        if ($result === false) {
                            error_log('About members result retrieval failed: ' . $selectStatement->error);
                            $databaseError = 'Team information is temporarily unavailable.';
                        } else {
                            $members = $result->fetch_all(MYSQLI_ASSOC);
                        }
                    }
                    $selectStatement->close();
                }
            }
        }

        $conn->close();
    }
}

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
      <div class="member-list">
        <?php if ($databaseError !== ''): ?>
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
            </blockquote>
          </div>
          </article>
        <?php else: ?>
          <?php foreach ($members as $member): ?>
            <?php $memberRoles = preg_split('/\R/', $member['roles']) ?: []; ?>
            <article class="member-card">
              <div class="member-image">
                <img src="images/<?= htmlspecialchars($member['image_name']) ?>" width="300" height="450" alt="<?= htmlspecialchars($member['member_name']) ?>">
                <div class="role-list">
                  <?php foreach ($memberRoles as $role): ?>
                    <?php if (trim($role) !== ''): ?>
                      <p class="role"><?= htmlspecialchars(trim($role)) ?></p>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="member-details">
                <h3><?= htmlspecialchars($member['member_name']) ?></h3>
                <blockquote>
                  <p><?= htmlspecialchars($member['quote_vietnamese']) ?></p>
                  <p class="translate"><?= htmlspecialchars($member['quote_translation']) ?></p>
                </blockquote>
                <div class="member-contributions">
                  <h4>Project contributions</h4>
                  <p><strong>First project:</strong> <?= htmlspecialchars($member['first_project_contribution']) ?></p>
                  <p><strong>Second project:</strong> <?= htmlspecialchars($member['second_project_contribution']) ?></p>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
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
            <?php if ($databaseError === ''): ?>
              <?php foreach ($members as $member): ?>
                <tr>
                  <th scope="row"><?= htmlspecialchars($member['fun_fact_name']) ?></th>
                  <td><?= htmlspecialchars($member['dream_job']) ?></td>
                  <td><?= htmlspecialchars($member['favorite_dish']) ?></td>
                  <td><?= htmlspecialchars($member['hometown']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><th scope="row">Đạt</th><td>Software Engineer</td><td>Pizza</td><td>HCM city</td></tr>
              <tr><th scope="row">Khang</th><td>Cybersecurity Engineer &amp; Pen-tester</td><td>Japanese curry</td><td>HCM city</td></tr>
              <tr><th scope="row">Đăng</th><td>Software Security Engineer, Game Developer, VFX artist &amp; graphics programmer, jack of all trades</td><td>Water</td><td>Mars</td></tr>
              <tr><th scope="row">Tuấn Anh</th><td>Security Engineer</td><td>Hot Pot</td><td>Dong Nai province</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.inc'; ?>
