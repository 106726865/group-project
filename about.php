<?php
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
        <img src="assets/teampic.png" width="1200" height="675" alt="The CLAZITERIUM team" class="team-picture" style="filter: grayscale();">
        <figcaption>the group</figcaption>
      </figure>
      <h2 class="members-heading">Meet the members</h2>
      <div class="member-list">
        <article class="member-card">
          <div class="member-image">
            <img src="assets/dang.png" width="300" height="450" alt="Đăng">
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
            <img src="assets/ta.png" width="300" height="450" alt="Tuấn Anh">
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
            <img src="assets/dat.png" width="300" height="450" alt="Đức Đạt">
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
            <img src="assets/khang.png" width="300" height="450" alt="Nhật Khang">
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
