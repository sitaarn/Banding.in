<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Banding.in - <?= __('about_us') ?></title>
<link rel="icon" href="./public/images/favicon.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Instrument+Serif:ital@0;1&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./public/css/aboutus.css">
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<nav id="mainNav">
  <div class="logo" style="font-family:'DM Serif Display',serif;">
    banding<em style="font-style:italic">.in</em>
</div>

  <div class="nav-buttons">
    <!-- Akan diisi oleh JavaScript -->
  </div>
</nav>

<main>
  <section class="hero">
    <h2 class="hero-label" style="font-size: 1rem; letter-spacing: 0.1em; margin-bottom: 12px;"><?= __('about_story_label') ?></h2>
    <h1 class="hero-title"><?= __('shop_smarter') ?><br><?= __('not_harder') ?></h1>
    <p class="hero-sub"><?= __('find_best_prices_desc') ?></p>
  </section>

  <div class="story-card">
    <p class="story-label"><?= __('about_story_label') ?></p>
    <p><?= __('about_story_1') ?></p>
    <p><?= __('about_story_2') ?></p>
  </div>

  <div class="features">
    <div class="feature-card">
      <div class="feature-title"><?= __('feature_1_title') ?></div>
      <p class="feature-desc"><?= __('feature_1_desc') ?></p>
    </div>
    <div class="feature-card">
      <div class="feature-title"><?= __('feature_2_title') ?></div>
      <p class="feature-desc"><?= __('feature_2_desc') ?></p>
    </div>
    <div class="feature-card">
      <div class="feature-title"><?= __('feature_3_title') ?></div>
      <p class="feature-desc"><?= __('feature_3_desc') ?></p>
    </div>
  </div>

  <div class="cta-group">
    <button class="btn-primary" onclick="window.location.href='/bandingin/list'"><?= __('try_now') ?></button>
  </div>
</main>

<script  src="./public/js/aboutus.js"></script>

</body>
</html>