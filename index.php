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
  <div class="container">
    <div class="hero-badge"><span class="dot"></span> Now Accepting New Clients</div>
    <h1>Smart AI Solutions for <span>Growing Teams</span></h1>
    <p>We build AI assistants, custom software, and cognitive tools that help your business work faster and smarter.</p>
    <div class="hero-buttons">
      <a href="#contact" class="btn btn-primary">Get in Touch</a>
      <a href="#services" class="btn btn-outline">See Services</a>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="stats">
  <div class="container stats-grid">
    <div class="stat-item"><h3>50+</h3><p>Projects Delivered</p></div>
    <div class="stat-item"><h3>30+</h3><p>Happy Clients</p></div>
    <div class="stat-item"><h3>99%</h3><p>Uptime Guarantee</p></div>
    <div class="stat-item"><h3>24h</h3><p>Support Response</p></div>
  </div>
</section>

<!-- Services -->
<section class="features section-pad" id="services">
  <div class="container">
    <div class="section-header">
      <p class="overline">Our Services</p>
      <h2>What We Build for You</h2>
      <p>From AI assistants to full software systems — we handle complex technology so you don't have to.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🤖</div>
        <h3>AI Virtual Assistant</h3>
        <p>Deploy smart chatbots and AI agents that answer questions, handle tasks, and serve your customers around the clock with accuracy.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💻</div>
        <h3>Software Development</h3>
        <p>Custom web apps, APIs, and backend systems built to scale. We design, develop, and deploy production-grade software for your team.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3>Prototyping Solutions</h3>
        <p>Need to validate an idea fast? We turn concepts into working prototypes in days — so you can test, iterate, and move forward quickly.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🧩</div>
        <h3>Consulting Services</h3>
        <p>Get clear, practical guidance on AI strategy, tool selection, and system architecture from engineers who have built it themselves.</p>
      </div>
    </div>
  </div>
</section>

<!-- About / Company Intro -->
<section class="section-pad" id="about" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container text-center">
    <div class="section-header">
      <p class="overline">About Us</p>
      <h2>Built by Engineers, for Business</h2>
      <p>AI-Solutions is a team of software engineers and AI specialists focused on building practical, reliable systems. We don't sell buzzwords — we build tools that actually work and save your team real time.</p>
    </div>
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 8px;">
      <a href="#contact" class="btn btn-primary">Work With Us</a>
      <a href="#services" class="btn btn-outline">Our Services</a>
    </div>
  </div>
</section>

<!-- Past Projects -->
<section class="features section-pad" id="projects" style="background: var(--white);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Past Work</p>
      <h2>Projects & Case Studies</h2>
      <p>A selection of real projects we've delivered across different industries.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">📦</div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Logistics</p>
        <h3>Supply Chain Optimizer</h3>
        <p>Built a routing agent for a logistics company that reduced fuel costs by 22% by intelligently scheduling distribution routes in real time.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔍</div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Finance</p>
        <h3>Compliance Search Engine</h3>
        <p>Deployed a semantic search system that automatically analyzes financial compliance logs, saving hundreds of manual auditing hours each month.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🛒</div>
        <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Retail</p>
        <h3>Customer Support Assistant</h3>
        <p>Integrated a virtual assistant that now handles 85% of support requests automatically, drastically reducing response times and staff workload.</p>
      </div>
    </div>
  </div>
</section>

<!-- Reviews -->
<section class="features section-pad" id="reviews" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Testimonials</p>
      <h2>What Clients Say</h2>
      <p>Honest feedback from the teams we've worked with.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div style="color: #f59e0b; margin-bottom: 14px; font-size: 1rem;">★★★★★</div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"AI-Solutions delivered our prototype in under two weeks. Clean code, clear communication, and it actually worked on day one."</p>
        <p style="font-weight: 700; font-size: .9rem;">Sarah Jenkins</p>
        <p style="font-size: .78rem; color: var(--text-light);">CTO, NexaFlow</p>
      </div>
      <div class="feature-card">
        <div style="color: #f59e0b; margin-bottom: 14px; font-size: 1rem;">★★★★★</div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"The custom search database they built lets our team query thousands of records instantly. It changed how we do compliance entirely."</p>
        <p style="font-weight: 700; font-size: .9rem;">Marcus Vance</p>
        <p style="font-size: .78rem; color: var(--text-light);">VP of Operations, Global Logistics</p>
      </div>
      <div class="feature-card">
        <div style="color: #f59e0b; margin-bottom: 14px; font-size: 1rem;">★★★★★</div>
        <p style="font-size: .95rem; color: var(--text-mid); line-height: 1.7; margin-bottom: 18px; font-style: italic;">"The virtual assistant handles 85% of our tickets now. Our support team is finally able to focus on real issues. Highly recommend."</p>
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
      <p>Practical articles on AI, software, and how we approach building systems.</p>
    </div>
    <div class="features-grid">
      <?php if (!empty($blogs)): ?>
        <?php foreach (array_slice($blogs, 0, 3) as $post): ?>
          <div class="feature-card" style="padding: 0; overflow: hidden;">
            <div style="background: var(--bg-soft); padding: 32px; text-align: center; font-size: 2.5rem; border-bottom: 1px solid var(--border);"><?= e($post['icon']) ?></div>
            <div style="padding: 24px;">
              <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;"><?= e($post['category']) ?></p>
              <h3 style="margin-bottom: 10px;"><?= e($post['title']) ?></h3>
              <p style="font-size: .88rem; color: var(--text-mid); margin-bottom: 16px;"><?= e($post['content']) ?></p>
              <a href="blog_detail.php?id=<?= $post['id'] ?>" style="font-size: .85rem; font-weight: 600; color: var(--accent);">Read more →</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--text-light); padding: 40px 0;">No articles posted yet.</p>
      <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 32px;">
      <a href="blog.php" class="btn btn-outline" style="border-radius: 20px; padding: 10px 24px;">View All Articles →</a>
    </div>
  </div>
</section>

<!-- Gallery -->
<section class="features section-pad" id="gallery" style="background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Gallery</p>
      <h2>Events & Team Activities</h2>
      <p>A look at our team events, workshops, and company activities.</p>
    </div>
    <div class="features-grid">
      <?php if (!empty($gallery)): ?>
        <?php foreach (array_slice($gallery, 0, 4) as $item): ?>
          <?php
            // Map color theme to background/text colors
            $bg_color = '#ede9ff';
            $text_color = 'var(--accent)';
            if ($item['color_theme'] === 'yellow') {
                $bg_color = '#fef7e6';
                $text_color = 'var(--yellow)';
            } elseif ($item['color_theme'] === 'green') {
                $bg_color = '#e6f7ee';
                $text_color = 'var(--green)';
            } elseif ($item['color_theme'] === 'red') {
                $bg_color = '#fdeaea';
                $text_color = 'var(--red)';
            }
          ?>
          <div class="feature-card" style="padding: 0; overflow: hidden; text-align: center;">
            <div style="height: 160px; background: <?= $bg_color ?>; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
              <span style="font-size: 2rem;"><?= e($item['icon']) ?></span>
              <strong style="font-size: .95rem; color: <?= $text_color ?>;"><?= e($item['title']) ?></strong>
            </div>
            <p style="padding: 14px; font-size: .85rem; color: var(--text-mid);"><?= e($item['description']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--text-light); padding: 40px 0;">No gallery items added yet.</p>
      <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 32px;">
      <a href="gallery.php" class="btn btn-outline" style="border-radius: 20px; padding: 10px 24px;">View Full Gallery →</a>
    </div>
  </div>
</section>

<!-- Events -->
<section class="section-pad" id="events" style="background: var(--white);">
  <div class="container">
    <div class="section-header">
      <p class="overline">Events</p>
      <h2>Upcoming Sessions</h2>
      <p>Join our live workshops and webinars to learn how AI can work for you.</p>
    </div>
    <div style="display: flex; flex-direction: column; gap: 14px; max-width: 720px; margin: 0 auto;">
      <div class="feature-card" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px; flex-wrap: wrap;">
        <div style="background: var(--accent-soft); color: var(--accent); padding: 10px 18px; border-radius: var(--radius); text-align: center; min-width: 90px; border: 1px solid #c9c0f9;">
          <div style="font-size: .7rem; font-weight: 700; text-transform: uppercase;">June</div>
          <div style="font-size: 1.7rem; font-weight: 700; line-height: 1.1;">15</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;">AI Prototyping Masterclass</h3>
          <p style="font-size: .88rem; color: var(--text-mid);">A live, hands-on session where we build a working AI agent workflow together from scratch.</p>
        </div>
      </div>
      <div class="feature-card" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px; flex-wrap: wrap;">
        <div style="background: var(--accent-soft); color: var(--accent); padding: 10px 18px; border-radius: var(--radius); text-align: center; min-width: 90px; border: 1px solid #c9c0f9;">
          <div style="font-size: .7rem; font-weight: 700; text-transform: uppercase;">July</div>
          <div style="font-size: 1.7rem; font-weight: 700; line-height: 1.1;">12</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;">LLM Fine-Tuning Seminar</h3>
          <p style="font-size: .88rem; color: var(--text-mid);">Expert-guided seminar covering domain-specific model fine-tuning and secure offline deployment.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contact Us -->
<section class="contact section-pad" id="contact" style="border-top: 1px solid var(--border); background: var(--bg-soft);">
  <div class="container">
    <div class="contact-wrapper">
      <div class="contact-info">
        <p class="overline" style="margin-bottom: 10px;">Contact Us</p>
        <h2>Get In Touch</h2>
        <p>Tell us about your project or requirements. We read every submission and respond within 24 hours.</p>
        <div class="contact-detail"><div class="icon-circle">📧</div><span>hello@ai-solutions.io</span></div>
        <div class="contact-detail"><div class="icon-circle">📍</div><span>Remote — Serving globally</span></div>
        <div class="contact-detail"><div class="icon-circle">🕐</div><span>Response within 24 hours</span></div>
        <?php if (!isLoggedIn()): ?>
          <div style="margin-top: 24px; padding: 16px; background: var(--accent-soft); border: 1px solid #c9c0f9; border-radius: var(--radius);">
            <p style="font-size: .85rem; color: var(--accent); font-weight: 600; margin-bottom: 10px;">🔒 Sign in to submit a request</p>
            <p style="font-size: .82rem; color: var(--text-mid); margin-bottom: 14px;">You need a free account to send inquiries. It takes less than a minute.</p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
              <a href="register.php" class="btn btn-outline btn-sm">Create Account</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="contact-form">
        <?php if (isLoggedIn()): ?>
          <h3>Submit a Request</h3>

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
                <label for="details">Job Details / Requirements *</label>
                <textarea id="details" name="details" placeholder="Describe what you need help with, your timeline, and any specific requirements..." required></textarea>
              </div>
              <div class="form-group full-width">
                <button type="submit" class="btn btn-primary btn-submit">Send Request</button>
              </div>
            </div>
          </form>
        <?php else: ?>
          <div style="text-align: center; padding: 48px 24px;">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">🔒</div>
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">Sign in to Submit</h3>
            <p style="color: var(--text-mid); font-size: .92rem; margin-bottom: 24px;">Only registered users can submit inquiry requests. Create a free account to get started.</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
              <a href="login.php" class="btn btn-primary">Sign In</a>
              <a href="register.php" class="btn btn-outline">Create Account</a>
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
</body>
</html>
