<?php
$pageTitle   = 'CLAZITERIUM | Creative Agency';
$inlineStyle = '.intro-info-bar > p { text-wrap: balance; }';
$bodyClass   = 'intro-page';

include __DIR__ . '/includes/head.inc';
include __DIR__ . '/includes/intro-header.inc';
?>

  <main class="intro-main">
    <section class="intro-sec">
      <div class="intro-tagline">
        <h2>We make digital work impossible to ignore.</h2>
      </div>
    </section>

    <section class="intro-details">
      <h2 class="meh">What we do</h2>
      <table>
        <tbody>
          <tr><th scope="row">Web design</th><td>Minimal, coherent, readable, professional.</td></tr>
          <tr><th scope="row">Product engineering</th><td>Design → build → operate a new web app end-to-end.</td></tr>
          <tr><th scope="row">Rescue & rewrite</th><td>Take over stalled projects. Stabilize, then ship fully optimized.</td></tr>
          <tr><th scope="row">Commerce & Shopify</th><td>Headless, Shopify Plus, custom checkout, subscriptions.</td></tr>
        </tbody>
      </table>
    </section>

    <section class="intro-idea">
      <p>Small studio.<br>Big ideas.</p>
    </section>

    <section class="intro-search">
      <h2 class="meh">Search</h2>
      <div class="uselessbutton">
        <label for="site-search">Search this site</label>
        <input id="site-search" name="q" type="search">
        <button type="button" id="site-search-button">Search</button>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.inc'; ?>
