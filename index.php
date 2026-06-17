<?php
/**
 * AI-Solutions — Homepage
 */
require_once __DIR__ . '/config.php';
$flash = getFlash();

try {
    $db = getDB();
    $blogs = $db->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $gallery = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to load home data: " . $e->getMessage());
    $blogs = [];
    $gallery = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="AI-Solutions provides AI Virtual Assistants, Software Development, Prototyping, and Consulting services for modern businesses.">
  <title>AI-Solutions — AI Services & Consulting</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI<span>Solutions</span></a>
    <div class="nav-links" id="navLinks">
      <a href="#services">Services</a>
      <a href="#projects">Projects</a>
      <a href="#reviews">Reviews</a>
      <a href="#blog">Blog</a>
      <a href="#gallery">Gallery</a>
      <a href="#events">Events</a>
      <a href="#contact">Contact</a>
      <?php if (isAdmin()): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php" class="nav-cta">Sign Out</a>
      <?php elseif (isUser()): ?>
        <a href="user_dashboard.php">My Account</a>
        <a href="logout.php" class="nav-cta">Sign Out</a>
      <?php else: ?>
        <a href="login.php">Sign In</a>
        <a href="register.php" class="nav-cta">Register</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Hero -->
<section class="hero" id="home">
  <div class="container animate-fade-in-up">
    <div class="hero-badge"><span class="dot"></span> Enterprise AI Engineering</div>
    <h1>Empowering Teams with <span>Autonomous AI Systems</span></h1>
    <p>We design and deploy production-grade agentic workflows, cognitive software systems, and tailored virtual assistants to optimize your operational performance.</p>
    <div class="hero-buttons">
      <a href="#contact" class="btn btn-primary">Schedule Consultation</a>
      <a href="#services" class="btn btn-outline">Explore Capabilities</a>
    </div>
  </div>
</section>

<!-- About & Stats -->
<section class="section-pad" id="about" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 48px; align-items: center;" class="form-grid">
      <div>
        <p class="overline" style="font-weight:700; color:var(--accent); text-transform:uppercase; letter-spacing:2px; margin-bottom:8px;">About Us</p>
        <h2 style="font-family:'DM Serif Display', serif; font-size:2.2rem; font-weight:400; margin-bottom:16px;">Built by Engineers, Dedicated to Performance</h2>
        <p style="color:var(--text-mid); margin-bottom:24px;">AI-Solutions is a specialized group of software engineers and AI developers focused on deploying high-reliability cognitive architectures. We bypass the industry buzzwords to engineer practical, scalable, and secure systems that optimize your business workflows.</p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="#contact" class="btn btn-primary">Partner With Us</a>
          <a href="#services" class="btn btn-outline">Our Capabilities</a>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        <div class="card" style="text-align: center; padding: 24px 16px;">
          <h3 style="font-family:'DM Serif Display', serif; font-size:2.2rem; color:var(--accent); font-weight:400; line-height:1;">50+</h3>
          <p style="color:var(--text-mid); font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:8px;">Projects Delivered</p>
        </div>
        <div class="card" style="text-align: center; padding: 24px 16px;">
          <h3 style="font-family:'DM Serif Display', serif; font-size:2.2rem; color:var(--accent); font-weight:400; line-height:1;">30+</h3>
          <p style="color:var(--text-mid); font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:8px;">Active Clients</p>
        </div>
        <div class="card" style="text-align: center; padding: 24px 16px;">
          <h3 style="font-family:'DM Serif Display', serif; font-size:2.2rem; color:var(--accent); font-weight:400; line-height:1;">99.9%</h3>
          <p style="color:var(--text-mid); font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:8px;">Uptime Achieved</p>
        </div>
        <div class="card" style="text-align: center; padding: 24px 16px;">
          <h3 style="font-family:'DM Serif Display', serif; font-size:2.2rem; color:var(--accent); font-weight:400; line-height:1;">&lt;24h</h3>
          <p style="color:var(--text-mid); font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:8px;">Response SLA</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section class="features section-pad" id="services" style="background: var(--white);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Our Services</p>
      <h2>What We Build for You</h2>
      <p>From autonomous agents to full-stack systems — we architect complex tech so you can scale efficiently.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-10 10c0 5.523 4.477 10 10 10s10-4.477 10-10A10 10 0 0 0 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-4 7h8v2H8z"/></svg>
        </div>
        <h3>AI Virtual Assistants</h3>
        <p>Deploy intelligent assistants and cognitive agents that resolve queries, integrate with existing workflows, and serve customers with sub-second latency.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M16.5 9.4 12 14.1l-4.5-4.7L6 10.8l6 6.3 6-6.3-1.5-1.4z"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/></svg>
        </div>
        <h3>Software Engineering</h3>
        <p>Custom microservices, production-ready APIs, and database solutions built to scale. We construct clean, modular software engineered for reliability.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M13 2v9h9v2h-9v9h-2v-9H2v-2h9V2h2z"/></svg>
        </div>
        <h3>Prototyping Solutions</h3>
        <p>Validate technical assumptions rapidly. We construct functional proof-of-concepts in days to test interfaces, system dependencies, and user response.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm1-15h2v2h-2V7zm0 4h2v6h-2v-6z"/></svg>
        </div>
        <h3>Consulting Services</h3>
        <p>Receive concrete technical advisory on AI models, safety guardrails, integration patterns, and architectural scaling from experienced staff.</p>
      </div>
    </div>
  </div>
</section>

<!-- Past Projects -->
<section class="features section-pad" id="projects" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Past Work</p>
      <h2>Enterprise Case Studies</h2>
      <p>A selection of production systems we have delivered across enterprise operations.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon" style="background:var(--white);">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 12H4v-4h8v4zm8 0h-6v-4h6v4zm0-6H4V6h16v4z"/></svg>
        </div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Logistics</p>
        <h3>Supply Chain Optimizer</h3>
        <p>Built a routing agent for a logistics group that reduced fuel expenditure by 22% by scheduling distribution corridors dynamically.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:var(--white);">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Finance</p>
        <h3>Compliance Search Engine</h3>
        <p>Deployed a secure semantic parsing database that audits policy documents, eliminating manual compliance search delays.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:var(--white);">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/></svg>
        </div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Retail</p>
        <h3>Automated Support Agent</h3>
        <p>Integrated a customer assistant resolving 85% of standard help desk tickets, improving response resolution times by 90%.</p>
      </div>
    </div>
  </div>
</section>

<!-- Gallery -->
<section class="features section-pad" id="gallery" style="background: var(--white);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Gallery</p>
      <h2>Corporate Events & Activities</h2>
      <p>A visual record of our developer summits, hackathons, and corporate sessions.</p>
    </div>
    <div class="features-grid">
      <?php if (!empty($gallery)): ?>
        <?php foreach (array_slice($gallery, 0, 4) as $item): ?>
          <?php
            $has_img = !empty($item['image_path']);
            if ($has_img) {
                $g_media = '<img src="' . e($item['image_path']) . '" alt="' . e($item['title']) . '">';
            } else {
                $g_media = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--accent-soft) 0%,var(--bg-soft) 100%);"><svg class="svg-icon" style="width:40px;height:40px;color:var(--accent);" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg></div>';
            }
          ?>
          <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; cursor: pointer;" onclick="openContentModal('gallery', <?= htmlspecialchars(json_encode($item['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($item['description']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($g_media), ENT_QUOTES, 'UTF-8') ?>, 'Gallery Item')">
            <div class="gallery-img-container">
              <?php if ($has_img): ?>
                <img src="<?= e($item['image_path']) ?>" alt="<?= e($item['title']) ?>" class="gallery-img">
              <?php else: ?>
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--accent-soft) 0%, var(--bg-soft) 100%);">
                  <svg class="svg-icon" style="width:40px; height:40px; color:var(--accent);" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                </div>
              <?php endif; ?>
            </div>
            <div style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
              <strong style="font-size: 1.05rem; color: var(--text); margin-bottom: 6px; display: block;"><?= e($item['title']) ?></strong>
              <p style="font-size: .85rem; color: var(--text-mid); line-height: 1.5;"><?= e($item['description']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--text-light); padding: 40px 0;">No gallery items added yet.</p>
      <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 32px;">
      <a href="gallery.php" class="btn btn-outline" style="border-radius: 20px; padding: 10px 24px;">View Full Gallery</a>
    </div>
  </div>
</section>

<!-- Reviews -->
<section class="features section-pad" id="reviews" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Testimonials</p>
      <h2>Client Feedback</h2>
      <p>Direct testimony from engineering teams we have collaborated with.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="star-rating">
          <?php for($i=0; $i<5; $i++): ?><svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg><?php endfor; ?>
        </div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"AI-Solutions engineered our integration prototype in under two weeks. The implementation was cleanly factored, documented, and production-ready."</p>
        <p style="font-weight: 700; font-size: .9rem;">Sarah Jenkins</p>
        <p style="font-size: .78rem; color: var(--text-light);">CTO, NexaFlow</p>
      </div>
      <div class="feature-card">
        <div class="star-rating">
          <?php for($i=0; $i<5; $i++): ?><svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg><?php endfor; ?>
        </div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"The semantic search architecture they built allows our compliance analysts to parse through thousands of records instantly. It completely resolved auditing latency."</p>
        <p style="font-weight: 700; font-size: .9rem;">Marcus Vance</p>
        <p style="font-size: .78rem; color: var(--text-light);">VP of Operations, Global Logistics</p>
      </div>
      <div class="feature-card">
        <div class="star-rating">
          <?php for($i=0; $i<5; $i++): ?><svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg><?php endfor; ?>
        </div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"Our automated support assistant manages the majority of daily support inquiries independently, freeing the engineering team to focus on development work."</p>
        <p style="font-weight: 700; font-size: .9rem;">Elena Rostova</p>
        <p style="font-size: .78rem; color: var(--text-light);">Director of Tech, FinCore</p>
      </div>
    </div>
  </div>
</section>

<!-- Blog -->
<section class="features section-pad" id="blog" style="background: var(--white);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Blog & Articles</p>
      <h2>AI Insights & News</h2>
      <p>Practical articles on AI systems, software patterns, and system engineering.</p>
    </div>
    <div class="features-grid">
      <?php if (!empty($blogs)): ?>
        <?php foreach (array_slice($blogs, 0, 3) as $post): ?>
          <?php
            $b_cat = strtolower($post['category'] ?? '');
            $b_svg = '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>';
            if (str_contains($b_cat, 'agent') || str_contains($b_cat, 'ai')) {
                $b_svg = '<path d="M12 2a10 10 0 0 0-10 10c0 5.523 4.477 10 10 10s10-4.477 10-10A10 10 0 0 0 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-4 7h8v2H8z"/>';
            } elseif (str_contains($b_cat, 'dev') || str_contains($b_cat, 'code') || str_contains($b_cat, 'engine')) {
                $b_svg = '<path d="M16.5 9.4 12 14.1l-4.5-4.7L6 10.8l6 6.3 6-6.3-1.5-1.4z"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/>';
            }
            $b_media = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--bg-soft);"><svg class="svg-icon" style="width:56px;height:56px;color:var(--accent);" viewBox="0 0 24 24">' . $b_svg . '</svg></div>';
          ?>
          <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; cursor: pointer;" onclick="openContentModal('blog', <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($post['content']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($b_media), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($post['category']), ENT_QUOTES, 'UTF-8') ?>)">
            <div style="background: var(--bg-soft); padding: 32px; text-align: center; border-bottom: 1px solid var(--border); display:flex; align-items:center; justify-content:center;">
              <svg class="svg-icon" style="width:36px; height:36px; color:var(--accent);" viewBox="0 0 24 24"><?= $b_svg ?></svg>
            </div>
            <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;"><?= e($post['category']) ?></p>
                <h3 style="margin-bottom: 10px; font-size:1.15rem;"><?= e($post['title']) ?></h3>
                <p style="font-size: .88rem; color: var(--text-mid); margin-bottom: 16px; line-height:1.5;"><?= e($post['content']) ?></p>
              </div>
              <a href="blog_detail.php?id=<?= $post['id'] ?>" onclick="event.stopPropagation();" style="font-size: .85rem; font-weight: 600; color: var(--accent); align-self: flex-start;">Read more →</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--text-light); padding: 40px 0;">No articles posted yet.</p>
      <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 32px;">
      <a href="blog.php" class="btn btn-outline" style="border-radius: 20px; padding: 10px 24px;">View All Articles</a>
    </div>
  </div>
</section>

<!-- Events -->
<section class="section-pad" id="events" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Events</p>
      <h2>Technical Sessions</h2>
      <p>Join our interactive technical sessions to learn how to deploy production-grade AI systems.</p>
    </div>
    <div style="display: flex; flex-direction: column; gap: 14px; max-width: 720px; margin: 0 auto;">
      <div class="feature-card" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px; flex-wrap: wrap;">
        <div style="background: var(--accent-soft); color: var(--accent); padding: 10px 18px; border-radius: var(--radius); text-align: center; min-width: 90px; border: 1px solid #c9c0f9;">
          <div style="font-size: .7rem; font-weight: 700; text-transform: uppercase;">June</div>
          <div style="font-size: 1.7rem; font-weight: 700; line-height: 1.1;">15</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;">AI Agent Workflow Masterclass</h3>
          <p style="font-size: .88rem; color: var(--text-mid);">Live walkthrough detailing multi-step cognitive agent orchestration, state management, and memory storage.</p>
        </div>
      </div>
      <div class="feature-card" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px; flex-wrap: wrap;">
        <div style="background: var(--accent-soft); color: var(--accent); padding: 10px 18px; border-radius: var(--radius); text-align: center; min-width: 90px; border: 1px solid #c9c0f9;">
          <div style="font-size: .7rem; font-weight: 700; text-transform: uppercase;">July</div>
          <div style="font-size: 1.7rem; font-weight: 700; line-height: 1.1;">12</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;">Semantic Search & Fine-Tuning</h3>
          <p style="font-size: .88rem; color: var(--text-mid);">Advanced webinar exploring vector storage systems, retrieval-augmented generation (RAG), and context tuning.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contact Us -->
<section class="contact section-pad" id="contact" style="background: var(--white);">
  <div class="container">
    <div class="contact-wrapper">
      <div class="contact-info">
        <p class="overline" style="margin-bottom: 10px;">Contact Us</p>
        <h2>Partner With Us</h2>
        <p>Outline your operational challenges and technical requirements. Our engineering team reviews each project inquiry within 24 hours.</p>
        <div class="contact-detail">
          <div class="icon-circle">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
          </div>
          <span><a href="mailto:rimshawagle14@gmail.com" style="color: inherit; text-decoration: none;">rimshawagle14@gmail.com</a></span>
        </div>
        <div class="contact-detail">
          <div class="icon-circle">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <span><a href="tel:+15550190" style="color: inherit; text-decoration: none;">+1 (555) 0190</a></span>
        </div>
        <div class="contact-detail">
          <div class="icon-circle">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2;"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><path d="M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
          </div>
          <span>Remote — Global Services</span>
        </div>
        <div class="contact-detail">
          <div class="icon-circle">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <span>Engineering Response within 24 Hours</span>
        </div>
        <?php if (!isLoggedIn()): ?>
          <div style="margin-top: 24px; padding: 16px; background: var(--accent-soft); border: 1px solid #c9c0f9; border-radius: var(--radius);">
            <p style="font-size: .85rem; color: var(--accent); font-weight: 600; margin-bottom: 10px;">Authentication Required</p>
            <p style="font-size: .82rem; color: var(--text-mid); margin-bottom: 14px;">Please sign in to submit technical inquiries. Account registration is free.</p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
              <a href="register.php" class="btn btn-outline btn-sm">Create Account</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="contact-form">
        <?php if (isLoggedIn()): ?>
          <h3>Submit a Project Inquiry</h3>

          <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom: 18px;"><?= e($flash['message']) ?></div>
          <?php endif; ?>

          <form action="submit_inquiry.php" method="POST" id="homeInquiryForm">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-grid">
              <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" placeholder="Jane Doe" value="<?= e($_SESSION['username']) ?>" required>
              </div>
              <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="jane@company.com" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="+1 (555) 0199">
              </div>
              <div class="form-group">
                <label for="company">Company Name</label>
                <input type="text" id="company" name="company" placeholder="Your Company">
              </div>
              <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" placeholder="United States">
              </div>
              <div class="form-group">
                <label for="job_title">Job Title *</label>
                <input type="text" id="job_title" name="job_title" placeholder="Product Manager" required>
              </div>
              <div class="form-group full-width">
                <label for="details">Project Details / Technical Requirements *</label>
                <textarea id="details" name="details" placeholder="Describe the operational challenge, model requirements, data schema, timeline, and security constraints..." required></textarea>
              </div>
              <div class="form-group full-width">
                <button type="submit" class="btn btn-primary btn-submit">Send Inquiry</button>
              </div>
            </div>
          </form>
        <?php else: ?>
          <div style="text-align: center; padding: 48px 24px;">
            <svg viewBox="0 0 24 24" style="width:48px; height:48px; stroke:var(--text-light); fill:none; stroke-width:2; margin-bottom:16px; display:inline-block;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">Inquiry Submission Restricted</h3>
            <p style="color: var(--text-mid); font-size: .92rem; margin-bottom: 24px;">Registered accounts are required to post operational project inquiries.</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
              <a href="login.php" class="btn btn-primary">Sign In</a>
              <a href="register.php" class="btn btn-outline">Register</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions. All rights reserved. &nbsp;|&nbsp; <a href="login.php">Sign In</a> &nbsp;|&nbsp; <a href="register.php">Register</a></p>
  </div>
</footer>

<!-- Chatbot -->
<button class="chat-toggle" id="chatToggle" aria-label="Open chat">
  <svg class="icon-chat" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/></svg>
  <svg class="icon-close" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
</button>

<div class="chatbox" id="chatbox">
  <div class="chatbox-header">
    <div class="avatar">AI</div>
    <div class="info">
      <h4>AI Assistant</h4>
      <p>● Online</p>
    </div>
  </div>
  <div class="chatbox-messages" id="chatMessages">
    <div class="msg bot">Hi! 👋 Ask me anything about our services, pricing, or how we can help your team.</div>
  </div>
  <div class="chatbox-input">
    <input type="text" id="chatInput" placeholder="Type a message..." autocomplete="off">
    <button id="chatSend" aria-label="Send">
      <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
    </button>
  </div>
</div>

<script>
document.getElementById('hamburger').addEventListener('click', function(){
  document.getElementById('navLinks').classList.toggle('open');
});

const chatToggle = document.getElementById('chatToggle');
const chatbox    = document.getElementById('chatbox');
chatToggle.addEventListener('click', () => {
  chatbox.classList.toggle('open');
  chatToggle.classList.toggle('open');
});

const chatMessages = document.getElementById('chatMessages');
const chatInput    = document.getElementById('chatInput');
const chatSend     = document.getElementById('chatSend');

function appendMsg(text, sender) {
  const div = document.createElement('div');
  div.className = 'msg ' + sender;
  div.textContent = text;
  chatMessages.appendChild(div);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

async function sendMessage() {
  const text = chatInput.value.trim();
  if (!text) return;
  appendMsg(text, 'user');
  chatInput.value = '';
  try {
    const res  = await fetch('chat_handler.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message: text }) });
    const data = await res.json();
    appendMsg(data.reply || 'Sorry, I could not process that.', 'bot');
  } catch {
    appendMsg('Connection error. Please try again.', 'bot');
  }
}

chatSend.addEventListener('click', sendMessage);
chatInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });
</script>

<!-- Content Modal -->
<div id="contentModal" class="modal-backdrop" onclick="closeContentModal(event)">
  <div class="modal-card" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeContentModal(event)">&times;</button>
    <div id="modalMedia" class="modal-media"></div>
    <div class="modal-body">
      <span id="modalCategory" class="overline" style="color: var(--accent); margin-bottom: 8px; display: none;"></span>
      <h3 id="modalTitle" style="font-family: 'DM Serif Display', serif; font-size: 1.8rem; font-weight: 400; margin-bottom: 12px; color: var(--text);"></h3>
      <div id="modalContent" style="font-size: 0.95rem; color: var(--text-mid); line-height: 1.6; white-space: pre-wrap;"></div>
    </div>
  </div>
</div>

<script>
function openContentModal(type, title, content, mediaHtml, category) {
    var modal = document.getElementById('contentModal');
    document.getElementById('modalMedia').innerHTML = mediaHtml || '';
    var cat = document.getElementById('modalCategory');
    if (category) { cat.innerText = category; cat.style.display = 'inline-block'; }
    else { cat.style.display = 'none'; }
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalContent').innerText = content;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeContentModal(event) {
    if (event) event.preventDefault();
    document.getElementById('contentModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeContentModal();
});
</script>

</body>
</html>
