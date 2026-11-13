<?php
$pageTitle   = 'CLAZITERIUM | Apply';
$pageStyles  = ['apply-style.css'];
$inlineStyle = '.site-header nav a { text-wrap: balance; }';

include __DIR__ . '/includes/head.inc';
include __DIR__ . '/includes/header.inc';
?>

  <main class="application-page">
    <header class="application-heading">
      <div>
        <h1>MAKE YOUR MOVE<span>.</span></h1>
        <p class="application-intro">Interested in a front-end developer or designer role? Tell us a little about yourself.</p>
      </div>
    </header>

    <aside class="inclusive-employment">
      <h2 id="inclusive-employment-heading">Inclusive applications</h2>
      <p>CLAZITERIUM welcomes applications from Aboriginal and Torres Strait Islander peoples. We are committed to treating applicants respectfully throughout the recruitment process. Contact us if you have questions or need support when applying.</p>
    </aside>

    <form action="https://mercury.swin.edu.au/it000000/formtest.php" method="post">
      <section class="form-section">
        <h2 id="personal-heading"><span>01</span> Personal details</h2>
        <div class="form-grid">
          <div class="form-field">
            <label for="job-reference">Job reference number <span>*</span></label>
            <input id="job-reference" name="job_reference" type="text" required pattern="[A-Za-z0-9]{5}" maxlength="5" title="Enter exactly 5 letters or numbers." autocomplete="off">
            <small>Exactly 5 letters or numbers</small>
          </div>
          <div class="form-field">
            <label for="first-name">First name <span>*</span></label>
            <input id="first-name" name="first_name" type="text" required pattern="[A-Za-z]{1,20}" maxlength="20" title="Use 1 to 20 letters only." autocomplete="given-name">
          </div>
          <div class="form-field">
            <label for="last-name">Last name <span>*</span></label>
            <input id="last-name" name="last_name" type="text" required pattern="[A-Za-z]{1,20}" maxlength="20" title="Use 1 to 20 letters only." autocomplete="family-name">
          </div>
          <div class="form-field">
            <label for="date-of-birth">Date of birth <span>*</span></label>
            <input id="date-of-birth" name="date_of_birth" type="text" required pattern="(?:0[1-9]|[12][0-9]|3[01])/(?:0[1-9]|1[0-2])/[0-9]{4}" placeholder="DD/MM/YYYY" title="Enter a valid date in DD/MM/YYYY format." inputmode="numeric" autocomplete="bday">
            <small>DD/MM/YYYY</small>
          </div>
          <fieldset class="form-field gender-field">
            <legend>Gender <span>*</span></legend>
            <div class="choice-list">
              <label><input name="gender" type="radio" value="female" required> Female</label>
              <label><input name="gender" type="radio" value="male" required> Male</label>
              <label><input name="gender" type="radio" value="non-binary" required> Non-binary</label>
              <label><input name="gender" type="radio" value="prefer-not-to-say" required> Prefer not to say</label>
            </div>
          </fieldset>
        </div>
      </section>

      <section class="form-section">
        <h2 id="contact-heading"><span>02</span> Contact details</h2>
        <div class="form-grid">
          <div class="form-field form-field-wide">
            <label for="street-address">Street address <span>*</span></label>
            <input id="street-address" name="street_address" type="text" required maxlength="40" autocomplete="street-address">
          </div>
          <div class="form-field">
            <label for="suburb">Suburb / town <span>*</span></label>
            <input id="suburb" name="suburb" type="text" required maxlength="40" autocomplete="address-level2">
          </div>
          <div class="form-field">
            <label for="state">State <span>*</span></label>
            <select id="state" name="state" required autocomplete="address-level1">
              <option value="" disabled selected>Select a state</option>
              <option value="VIC">VIC</option>
              <option value="NSW">NSW</option>
              <option value="QLD">QLD</option>
              <option value="NT">NT</option>
              <option value="WA">WA</option>
              <option value="SA">SA</option>
              <option value="TAS">TAS</option>
              <option value="ACT">ACT</option>
            </select>
          </div>
          <div class="form-field">
            <label for="postcode">Postcode <span>*</span></label>
            <input id="postcode" name="postcode" type="text" required pattern="[0-9]{4}" maxlength="4" inputmode="numeric" autocomplete="postal-code" title="Enter exactly 4 digits.">
          </div>
          <div class="form-field">
            <label for="email">Email <span>*</span></label>
            <input id="email" name="email" type="email" required autocomplete="email">
          </div>
          <div class="form-field">
            <label for="phone">Phone number <span>*</span></label>
            <input id="phone" name="phone" type="tel" required pattern="[0-9]{8,12}" minlength="8" maxlength="12" inputmode="numeric" autocomplete="tel" title="Enter 8 to 12 digits, without spaces or symbols.">
            <small>8–12 digits, no spaces</small>
          </div>
        </div>
      </section>

      <section class="form-section">
        <h2 id="skills-heading"><span>03</span> Your skills</h2>
        <div class="form-grid">
          <fieldset class="form-field form-field-wide skills-field">
            <legend>Skill list <span>*</span></legend>
            <div class="choice-list skill-list">
              <label><input name="skills[]" type="checkbox" value="ux-research" required> UX research</label>
              <label><input name="skills[]" type="checkbox" value="prototyping" required> Prototyping</label>
              <label><input name="skills[]" type="checkbox" value="ui-design" required> UI design</label>
              <label><input name="skills[]" type="checkbox" value="html-css" required> HTML / CSS</label>
              <label><input name="skills[]" type="checkbox" value="javascript" required> JavaScript</label>
              <label><input name="skills[]" type="checkbox" value="motion-graphics" required> Motion graphics</label>
            </div>
          </fieldset>
          <div class="form-field form-field-wide">
            <label for="other-skills">Other skills <span class="optional">OPTIONAL</span></label>
            <textarea id="other-skills" name="other_skills" rows="5" placeholder="Anything else you'd like us to know?"></textarea>
          </div>
        </div>
      </section>

      <div class="form-actions">
        <p><span>*</span> Required fields</p>
        <button type="submit">Send application -></button>
      </div>
    </form>
  </main>

<?php include __DIR__ . '/includes/footer.inc'; ?>
