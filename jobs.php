<?php

declare(strict_types=1);

$pageTitle = 'CLAZITERIUM | Careers';
$pageStyles = ['jobs-style.css'];
$inlineStyle = '.site-header nav a { text-wrap: balance; }';

require_once __DIR__ . '/settings.php';

$databaseError = '';
$jobs = [];

if (!function_exists('mysqli_report')) {
    error_log('Jobs database unavailable: MySQLi extension is not loaded.');
    $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
} else {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_errno) {
        error_log('Jobs database connection failed: ' . $conn->connect_error);
        $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
    } else {
        $conn->set_charset('utf8mb4');

        $createTableSQL = "CREATE TABLE IF NOT EXISTS jobs (
            job_reference CHAR(5) NOT NULL,
            title VARCHAR(150) NOT NULL,
            summary VARCHAR(500) NOT NULL,
            salary VARCHAR(100) NOT NULL,
            reports_to VARCHAR(100) NOT NULL,
            tags TEXT NOT NULL,
            responsibilities TEXT NOT NULL,
            essential_requirements TEXT NOT NULL,
            preferable_requirements TEXT NOT NULL,
            PRIMARY KEY (job_reference)
        )";

        if (!$conn->query($createTableSQL)) {
            error_log('Jobs table creation failed: ' . $conn->error);
            $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
        } else {
            $jobData = [
                [
                    'UX204',
                    'UI/UX Designer',
                    'Shape clear, useful digital experiences from early ideas through polished interface designs.',
                    'VND 18–28 million / month',
                    'Design Lead',
                    "UX research\nPrototyping\nUI systems",
                    "Map user journeys and turn project goals into wireframes and prototypes.\nDesign consistent, accessible interfaces for desktop and mobile.\nGather feedback and work with the team to refine designs.",
                    "A portfolio showing interface or experience design work.\nFamiliarity with a collaborative design and prototyping tool.",
                    'Knowledge of accessibility and responsive design principles.',
                ],
                [
                    'FE316',
                    'Front-end Developer',
                    "Bring the studio's designs to life as responsive, accessible web pages.",
                    'VND 20–32 million / month',
                    'Development Lead',
                    "HTML / CSS\nResponsive\nAccessibility",
                    'Build and maintain web pages from design mockups and prototypes.' . "\n"
                        . 'Make layouts work across screen sizes and support accessible use.' . "\n"
                        . 'Collaborate with designers to check details and improve the finished experience.',
                    'Familiarity with HTML, CSS and JavaScript fundamentals.' . "\n"
                        . 'Care for visual detail and clear communication.',
                    'Experience with responsive layouts or UI prototyping.',
                ],
                [
                    'VD428',
                    'Visual Designer',
                    'Create distinctive visual identities and digital images that help brands stand out.',
                    'VND 16–25 million / month',
                    'Creative Director',
                    "Brand identity\nArt direction\nDigital images",
                    'Develop visual directions, layouts and design images for client projects.' . "\n"
                        . 'Help keep brand elements consistent across web and social content.' . "\n"
                        . 'Present ideas and respond constructively to feedback.',
                    'A portfolio that shows strong visual thinking and craft.' . "\n"
                        . 'Confidence with common graphic or interface design tools.',
                    'Interest in typography, branding or digital campaigns.',
                ],
                [
                    'MC502',
                    'Motion & Digital Content Designer',
                    'Give digital stories energy through motion, short-form content and thoughtful visual details.',
                    'VND 17–27 million / month',
                    'Creative Director',
                    "Motion graphics\nSocial content\nInteraction",
                    'Plan and create short motion pieces and digital content for client projects.' . "\n"
                        . 'Explore simple animations and interactions that support the user experience.' . "\n"
                        . 'Prepare visual images for web and social formats in collaboration with the team.',
                    'A portfolio showing visual or motion-based work.' . "\n"
                        . 'An eye for timing, composition and visual storytelling.',
                    'Familiarity with video, motion or prototyping tools.',
                ],
                [
                    'CW610',
                    'Copywriter / Content Designer',
                    'Create clear, engaging content that gives every brand a consistent and memorable voice.',
                    'VND 16–25 million / month',
                    'Content Lead',
                    "Copywriting\nContent strategy\nBrand voice",
                    'Write and edit copy for websites, campaigns and social channels.' . "\n"
                        . 'Shape messaging to fit each audience, brief and brand voice.' . "\n"
                        . 'Proofread content and collaborate with designers to prepare finished work.',
                    'A portfolio showing strong writing and editing skills.' . "\n"
                        . 'Ability to adapt tone and style for different audiences.',
                    'Familiarity with SEO-friendly writing or content management systems.',
                ],
                [
                    'SE724',
                    'SEO Specialist',
                    'Improve how people discover our clients through useful content and data-informed search strategy.',
                    'VND 18–30 million / month',
                    'Digital Marketing Lead',
                    "Keyword research\nAnalytics\nContent optimization",
                    'Research search terms and identify opportunities to improve organic visibility.' . "\n"
                        . 'Review pages and recommend on-page and technical improvements.' . "\n"
                        . 'Track performance and communicate clear, actionable findings.',
                    'Understanding of SEO fundamentals and search analytics.' . "\n"
                        . 'Experience interpreting data and explaining recommendations.',
                    'Familiarity with technical SEO audits or structured data.',
                ],
                [
                    'PM835',
                    'Project Manager',
                    'Keep creative and digital projects organized, collaborative and moving toward clear outcomes.',
                    'VND 22–35 million / month',
                    'Operations Director',
                    "Project planning\nDelivery\nCoordination",
                    'Build project plans, schedules and budgets with the team.' . "\n"
                        . 'Coordinate designers, developers and client stakeholders through delivery.' . "\n"
                        . 'Monitor progress, manage risks and share timely status updates.',
                    'Experience coordinating projects, timelines and multiple stakeholders.' . "\n"
                        . 'Strong organization and clear communication skills.',
                    'Experience delivering digital or creative agency projects.',
                ],
                [
                    'AM946',
                    'Account Manager',
                    'Build trusted client relationships and connect business goals with thoughtful creative work.',
                    'VND 20–32 million / month',
                    'Client Services Lead',
                    "Client relationships\nAccount strategy\nReporting",
                    'Act as a day-to-day contact for client questions and project updates.' . "\n"
                        . 'Understand client goals and coordinate suitable work with internal teams.' . "\n"
                        . 'Track account health, feedback and opportunities for continued collaboration.',
                    'Experience in a client-facing role and managing stakeholder expectations.' . "\n"
                        . 'Clear communication, organization and problem-solving skills.',
                    'Experience working with creative, marketing or digital teams.',
                ],
                [
                    'CD157',
                    'Creative Director',
                    'Set a clear creative vision and help teams deliver distinctive, thoughtful work for every client.',
                    'VND 35–55 million / month',
                    'Studio Director',
                    "Creative strategy\nArt direction\nTeam leadership",
                    'Lead creative direction across brand, digital and campaign projects.' . "\n"
                        . 'Guide teams from early concepts through client presentation and final delivery.' . "\n"
                        . 'Mentor creative staff and help maintain a high standard of work.',
                    'A strong portfolio of creative work and experience leading multidisciplinary teams.' . "\n"
                        . 'Ability to present ideas and give constructive creative direction.',
                    'Experience directing brand or digital work in a studio or agency.',
                ],
            ];

            $insertSQL = 'INSERT IGNORE INTO jobs (
                job_reference, title, summary, salary, reports_to, tags,
                responsibilities, essential_requirements, preferable_requirements
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $insertStatement = $conn->prepare($insertSQL);

            if ($insertStatement === false) {
                error_log('Jobs seed statement preparation failed: ' . $conn->error);
                $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
            } else {
                $insertStatement->bind_param(
                    'sssssssss',
                    $reference,
                    $title,
                    $summary,
                    $salary,
                    $reportsTo,
                    $tags,
                    $responsibilities,
                    $essentialRequirements,
                    $preferableRequirements
                );

                foreach ($jobData as $job) {
                    [
                        $reference,
                        $title,
                        $summary,
                        $salary,
                        $reportsTo,
                        $tags,
                        $responsibilities,
                        $essentialRequirements,
                        $preferableRequirements,
                    ] = $job;

                    if (!$insertStatement->execute()) {
                        error_log('Jobs seed insert failed: ' . $insertStatement->error);
                        $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
                        break;
                    }
                }
                $insertStatement->close();
            }

            if ($databaseError === '') {
                $searchQuery = isset($_GET['q']) && is_string($_GET['q'])
                    ? trim($_GET['q'])
                    : '';

                $selectSQL = 'SELECT job_reference, title, summary, salary, reports_to,
                    tags, responsibilities, essential_requirements, preferable_requirements
                    FROM jobs';

                if ($searchQuery !== '') {
                    $selectSQL .= " WHERE CONCAT_WS(' ', job_reference, title, summary, salary,
                        reports_to, tags, responsibilities, essential_requirements,
                        preferable_requirements) LIKE ? ESCAPE '!'";
                    $escapedSearchQuery = str_replace(
                        ['!', '%', '_'],
                        ['!!', '!%', '!_'],
                        $searchQuery
                    );
                    $searchPattern = '%' . $escapedSearchQuery . '%';
                }

                $selectSQL .= ' ORDER BY title';
                $selectStatement = $conn->prepare($selectSQL);

                if ($selectStatement === false) {
                    error_log('Jobs search statement preparation failed: ' . $conn->error);
                    $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
                } else {
                    if ($searchQuery !== '') {
                        $selectStatement->bind_param('s', $searchPattern);
                    }

                    if (!$selectStatement->execute()) {
                        error_log('Jobs search failed: ' . $selectStatement->error);
                        $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
                    } else {
                        $result = $selectStatement->get_result();
                        if ($result === false) {
                            error_log('Jobs search result retrieval failed: ' . $selectStatement->error);
                            $databaseError = 'Job listings are temporarily unavailable. Please try again later.';
                        } else {
                            $jobs = $result->fetch_all(MYSQLI_ASSOC);
                        }
                    }
                    $selectStatement->close();
                }
            }
        }

        $conn->close();
    }
}

function jobLines(string $text): array
{
    return array_values(array_filter(
        array_map('trim', explode("\n", $text)),
        static fn (string $line): bool => $line !== ''
    ));
}

function jobEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

include __DIR__ . '/includes/head.inc';
include __DIR__ . '/includes/header.inc';
?>

<main class="careers-page">
  <header class="page-header">
    <div class="page-title">
      <h1>Opportunities<span class="role-count"><?= count($jobs) ?></span></h1>
    </div>
  </header>

  <div class="careers-content">
    <aside class="application-note">
      <h2>Want to join us?</h2>
      <p>we got moneyy obviously</p>
      <a href="apply.php">APPLY -&gt;</a>
    </aside>
    <div class="absolute-random">
      <form class="job-search" action="jobs.php" method="get" role="search">
          <label for="job-search">Search opportunities</label>
          <div class="job-search-controls">
            <input id="job-search" name="q" type="search" value="<?= jobEscape($searchQuery ?? '') ?>">
            <button type="submit">Search</button>
          </div>
        </form>
    </div>
   
    <br>

    <?php if ($databaseError !== ''): ?>
      <p class="joblistmsg" role="alert"><?= jobEscape($databaseError) ?></p>
    <?php elseif ($jobs === []): ?>
      <p class="joblistmsg">No opportunities matched your search. Try another term.</p>
    <?php else: ?>
      <section class="job-listing">
        <?php foreach ($jobs as $job): ?>
          <?php
          $tags = jobLines($job['tags']);
          $responsibilities = jobLines($job['responsibilities']);
          $essentialRequirements = jobLines($job['essential_requirements']);
          $preferableRequirements = jobLines($job['preferable_requirements']);
          ?>
          <article class="job-card">
            <p class="job-info"><span class="job-reference"><?= jobEscape($job['job_reference']) ?></span></p>
            <h2><?= jobEscape($job['title']) ?></h2>
            <p class="job-sum"><?= jobEscape($job['summary']) ?></p>
            <dl class="job-maininfo">
              <div><dt>Salary</dt><dd><?= jobEscape($job['salary']) ?></dd></div>
              <div><dt>Reports to</dt><dd><?= jobEscape($job['reports_to']) ?></dd></div>
            </dl>
            <?php if ($tags !== []): ?>
              <ul class="job-tags">
                <?php foreach ($tags as $tag): ?>
                  <li><?= jobEscape($tag) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <details class="job-details">
              <summary>Role details</summary>
              <div class="job-dcont">
                <?php if ($responsibilities !== []): ?>
                  <section class="job-section">
                    <h3>Key responsibilities</h3>
                    <ol>
                      <?php foreach ($responsibilities as $responsibility): ?>
                        <li><?= jobEscape($responsibility) ?></li>
                      <?php endforeach; ?>
                    </ol>
                  </section>
                <?php endif; ?>
                <?php if ($essentialRequirements !== [] || $preferableRequirements !== []): ?>
                  <section class="job-section">
                    <h3>Requirements</h3>
                    <ul>
                      <?php foreach ($essentialRequirements as $requirement): ?>
                        <li><strong>Essential:</strong> <?= jobEscape($requirement) ?></li>
                      <?php endforeach; ?>
                      <?php foreach ($preferableRequirements as $requirement): ?>
                        <li><strong>Preferable:</strong> <?= jobEscape($requirement) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </section>
                <?php endif; ?>
              </div>
            </details>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.inc'; ?>
