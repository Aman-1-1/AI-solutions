<?php
/**
 * ============================================
 * AI-Solutions — Homepage
 * ============================================
 */
require_once __DIR__ . '/config.php';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="AI-Solutions leverages artificial intelligence to deliver software solutions, virtual assistants, and rapid prototyping for modern industries.">
    <title>AI-Solutions — Intelligent Software &amp; AI Consulting</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────── -->
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="#" class="nav-logo">AI-Solutions<span>.</span></a>
    <div class="nav-links" id="navLinks">
      <a href="#services">Services</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
      <?php if (isAdmin()): ?>
        <a href="dashboard.php">Admin Dashboard</a>
        <a href="logout.php" class="nav-cta">Logout</a>
      <?php elseif (isUser()): ?>
        <a href="user_dashboard.php">My Dashboard</a>
        <a href="logout.php" class="nav-cta">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="#contact" class="nav-cta">Request Demo</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- ── Hero ────────────────────────────────────── -->
<section class="hero container" id="hero">
  <div class="hero-badge"><span class="dot"></span> Now accepting new clients</div>
  <h1>Build Smarter with<br><span class="gradient-text">AI-Powered Solutions</span></h1>
  <p>We combine cutting-edge artificial intelligence with expert engineering to deliver software solutions, virtual assistants, and rapid prototyping that transform your business.</p>
  <div class="hero-buttons">
    <a href="#contact" class="btn btn-primary">Get Started →</a>
    <a href="#services" class="btn btn-outline">Our Services</a>
  </div>
</section>

<!-- ── Stats ───────────────────────────────────── -->
<section class="stats">
  <div class="container stats-grid">
    <div class="stat-item"><h3>50+</h3><p>Projects Delivered</p></div>
    <div class="stat-item"><h3>98%</h3><p>Client Satisfaction</p></div>
    <div class="stat-item"><h3>12</h3><p>Industries Served</p></div>
    <div class="stat-item"><h3>24/7</h3><p>AI Support</p></div>
  </div>
</section>

<!-- ── Services ────────────────────────────────── -->
<section class="features section-pad" id="services">
  <div class="container">
    <div class="section-header">
      <p class="overline">What We Do</p>
      <h2>Our Core Services</h2>
      <p>End-to-end AI solutions tailored to your industry and scale.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🤖</div>
        <h3>Custom AI Software</h3>
        <p>Bespoke machine-learning pipelines, NLP engines, and computer vision systems built to solve your unique business challenges.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💬</div>
        <h3>Virtual Assistants</h3>
        <p>Intelligent conversational agents that handle customer queries, bookings, and support — available around the clock.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3>Rapid Prototyping</h3>
        <p>From concept to clickable prototype in days, not months. We validate ideas fast with AI-accelerated design and development.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">📊</div>
        <h3>Data Analytics</h3>
        <p>Transform raw data into actionable insights with intelligent dashboards, predictive models, and automated reporting.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔗</div>
        <h3>API Integration</h3>
        <p>Seamlessly connect AI capabilities into your existing tech stack with robust, well-documented API solutions.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3>AI Consulting</h3>
        <p>Strategic guidance on AI adoption, model selection, and implementation roadmaps from experienced practitioners.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── About ───────────────────────────────────── -->
<section class="section-pad" id="about" style="border-top:1px solid var(--border-glass)">
  <div class="container text-center">
    <div class="section-header">
      <p class="overline">Who We Are</p>
      <h2>Pioneering AI for Every Industry</h2>
      <p>AI-Solutions is a forward-thinking startup that leverages artificial intelligence to provide software solutions, virtual assistants, and prototyping services. Our mission is to democratise AI — making powerful, intelligent technology accessible to businesses of all sizes across diverse industries.</p>
    </div>
  </div>
</section>

<!-- ── Contact Form ───────────────────────────── -->
<section class="contact section-pad" id="contact">
  <div class="container">
    <div class="contact-wrapper">
      <div class="contact-info">
        <p class="overline" style="margin-bottom:12px">Get In Touch</p>
        <h2>Request a Demo or Consultation</h2>
        <p>Fill in the form and our team will get back to you within 24 hours with a tailored proposal.</p>
        <div class="contact-detail"><div class="icon-circle">📧</div><span>hello@ai-solutions.io</span></div>
        <div class="contact-detail"><div class="icon-circle">📍</div><span>Global — Remote First</span></div>
        <div class="contact-detail"><div class="icon-circle">🕐</div><span>Mon – Fri, 9AM – 6PM</span></div>
      </div>
      <form class="contact-form" action="submit_inquiry.php" method="POST" id="inquiryForm">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <?php if ($flash): ?>
          <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" placeholder="Jane Doe" value="<?= isLoggedIn() ? e($_SESSION['username']) : '' ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" placeholder="jane@company.com" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="+1 234 567 890">
          </div>
          <div class="form-group">
            <label for="company">Company</label>
            <input type="text" id="company" name="company" placeholder="Acme Corp">
          </div>
          <div class="form-group full-width">
            <label for="country">Country</label>
            <input type="text" id="country" name="country" placeholder="United States">
          </div>
          <div class="form-group full-width">
            <label for="details">Project / Demo Details *</label>
            <textarea id="details" name="details" placeholder="Tell us about your project, requirements, or the demo you'd like to see…" required></textarea>
          </div>
          <div class="form-group full-width">
            <button type="submit" class="btn btn-primary btn-submit">Submit Inquiry →</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- ── Footer ─────────────────────────────────── -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions. All rights reserved. | <a href="login.php">Portal</a></p>
  </div>
</footer>

<!-- ── Chatbot UI ─────────────────────────────── -->
<button class="chat-toggle" id="chatToggle" aria-label="Open chatbot">
  <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/><path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/></svg>
</button>
<div class="chatbox" id="chatbox">
  <div class="chatbox-header">
    <div class="avatar">AI</div>
    <div class="info">
      <h4>AI-Solutions Assistant</h4>
      <p>● Online</p>
    </div>
  </div>
  <div class="chatbox-messages" id="chatMessages">
    <div class="msg bot">Hello! 👋 I'm the AI-Solutions assistant. How can I help you today? Ask me about our services, pricing, or anything else!</div>
  </div>
  <div class="chatbox-input">
    <input type="text" id="chatInput" placeholder="Type your message…" autocomplete="off">
    <button id="chatSend" aria-label="Send message">
      <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
    </button>
  </div>
</div>

<script>
/* ── Mobile Nav Toggle ─────────────────────── */
document.getElementById('hamburger').addEventListener('click', function(){
  document.getElementById('navLinks').classList.toggle('open');
});

/* ── Chatbot Toggle ────────────────────────── */
const chatToggle = document.getElementById('chatToggle');
const chatbox    = document.getElementById('chatbox');
chatToggle.addEventListener('click', () => {
  chatbox.classList.toggle('open');
  chatToggle.classList.toggle('active');
});

/* ── Chatbot Messaging ─────────────────────── */
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

function showTyping() {
  const div = document.createElement('div');
  div.className = 'msg bot typing';
  div.id = 'typingIndicator';
  div.innerHTML = '<div class="dots"><span></span><span></span><span></span></div>';
  chatMessages.appendChild(div);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function removeTyping() {
  const el = document.getElementById('typingIndicator');
  if (el) el.remove();
}

async function sendMessage() {
  const text = chatInput.value.trim();
  if (!text) return;
  appendMsg(text, 'user');
  chatInput.value = '';
  showTyping();

  try {
    const res = await fetch('chat_handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text })
    });
    const data = await res.json();
    removeTyping();
    appendMsg(data.reply || 'Sorry, I could not process that.', 'bot');
  } catch (err) {
    removeTyping();
    appendMsg('Connection error. Please try again later.', 'bot');
  }
}

chatSend.addEventListener('click', sendMessage);
chatInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });
</script>
</body>
</html>
