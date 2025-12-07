<!DOCTYPE html>
<?php
// Consolidated Single Page Parallax Home
// Include the public header
include_once __DIR__ . '/../../includes/header_public.php';
?>

<style>
/* Parallax and layout styles (inline for quick iteration) */
:root{--teal:#0d9488;--dark-teal:#0b7b72;--blue:#0b5fa5;--muted:#6c757d}
body.home-spa {background:#f5f8fa}
.parallax {background-attachment: fixed; background-size: cover; background-position: center;}
.hero {min-height:60vh; display:flex; align-items:center; color:#fff}
.overlay {background: rgba(0,0,0,0.35); padding:4rem 0}
.section {padding:6rem 0}
.section.light {background:#fff}
.section.clean {background:#f8fbfc}
.services-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem}
.service-card {background:#fff; border-radius:8px; padding:1.25rem; box-shadow:0 6px 20px rgba(13,148,136,0.06); transition: transform .25s ease, box-shadow .25s ease}
.service-card:hover {transform:translateY(-8px); box-shadow:0 18px 40px rgba(13,148,136,0.12)}
.cta-btn {background:var(--teal); color:#fff}
.anchor-offset {scroll-margin-top:90px}

/* Responsive tweaks */
@media (max-width:768px){ .overlay {padding:2rem 0} .section{padding:3rem 0} .parallax{background-attachment: scroll} }
</style>

<main class="home-spa">

  <!-- Hero (Parallax) -->
  <section id="hero" class="parallax hero" style="background-image:url('<?php echo BASE_URL; ?>assets/images/hero_bg.jpg');">
    <div class="overlay w-100">
      <div class="container text-center">
        <h1 class="display-4 fw-bold" data-aos="fade-up">Welcome to E-BHM Connect</h1>
        <p class="lead text-light mb-4" data-aos="fade-up">Digitizing health services for efficiency, accuracy, and accessibility in Barangay Bacong.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="<?php echo BASE_URL; ?>?page=login-patient" class="btn btn-lg cta-btn">Patient Portal</a>
          <a href="<?php echo BASE_URL; ?>?page=home#contact" class="btn btn-lg btn-outline-light">Contact Us</a>
        </div>
        <div class="mt-4">
          <a href="<?php echo BASE_URL; ?>?page=portal_chatbot" class="btn btn-light" data-aos="fade-up">Chat with Gabby</a>
        </div>
      </div>
    </div>
  </section>

  <!-- About (clean) -->
  <section id="about" class="section light anchor-offset">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
          <h2 class="fw-bold" data-aos="fade-right">Our Mission &amp; Vision</h2>
          <p class="text-muted" data-aos="fade-right">To digitize and securely manage patient health records, streamline healthcare services, and empower Barangay Health Workers with digital tools.</p>
          <p class="text-muted" data-aos="fade-right">To create a healthier community in Bacong, Dumangas by leveraging technology for efficient, reliable, and accessible health services.</p>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="mb-3">Barangay Information</h5>
              <p><strong>Barangay Name:</strong> Bacong, Dumangas</p>
              <p><strong>Location:</strong> 10.8500° N, 122.6833° E</p>
              <p><strong>Population:</strong> 5,240 residents</p>
              <p><strong>Contact:</strong> (033) 123-4567 | baconghall@gmail.com</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Divider (parallax subtle image) -->
  <section class="parallax" style="background-image:url('<?php echo BASE_URL; ?>assets/images/divider_wave.jpg'); min-height:180px;"></section>

  <!-- Services (clean) -->
  <section id="services" class="section clean anchor-offset">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold">Our Services</h2>
        <p class="text-muted">Accessible, community-centered healthcare services.</p>
      </div>

      <div class="services-grid">
        <div class="service-card text-center" data-aos="zoom-in" data-aos-delay="100">
          <img src="<?php echo BASE_URL; ?>assets/images/service_vaccination.jpg" alt="Vaccination" class="img-fluid rounded mb-3" />
          <h5>Vaccination</h5>
          <p class="small text-muted">Immunization schedules and records.</p>
        </div>
        <div class="service-card text-center" data-aos="zoom-in" data-aos-delay="200">
          <img src="<?php echo BASE_URL; ?>assets/images/service_checkup.jpg" alt="Checkups" class="img-fluid rounded mb-3" />
          <h5>Checkups</h5>
          <p class="small text-muted">Routine health monitoring and consultations.</p>
        </div>
        <div class="service-card text-center" data-aos="zoom-in" data-aos-delay="300">
          <img src="<?php echo BASE_URL; ?>assets/images/service_maternity.jpg" alt="Maternity Care" class="img-fluid rounded mb-3" />
          <h5>Maternity Care</h5>
          <p class="small text-muted">Maternal health and pre/post-natal services.</p>
        </div>
        <div class="service-card text-center" data-aos="zoom-in" data-aos-delay="400">
          <img src="<?php echo BASE_URL; ?>assets/images/service_chronic.jpg" alt="Chronic Disease Support" class="img-fluid rounded mb-3" />
          <h5>Chronic Disease Support</h5>
          <p class="small text-muted">Management programs for chronic conditions.</p>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="?page=announcements" class="btn btn-outline-primary">Latest Updates</a>
      </div>
    </div>
  </section>

  <!-- Announcements preview (image bg) -->
  <section class="parallax" style="background-image:url('<?php echo BASE_URL; ?>assets/images/announcements_bg.jpg'); min-height:220px;">
    <div class="overlay" style="background:rgba(11,95,165,0.35)">
      <div class="container text-center text-white">
        <h3 class="fw-bold">Latest Updates</h3>
        <p class="mb-3">Stay informed with the latest announcements from the barangay health center.</p>
        <a href="?page=announcements" class="btn btn-light">View All Announcements</a>
      </div>
    </div>
  </section>

  <!-- Contact (clean) -->
  <section id="contact" class="section light anchor-offset">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold" data-aos="fade-up">Contact Us</h2>
        <p class="text-muted" data-aos="fade-up">For inquiries or assistance, reach out to us.</p>
      </div>

      <div class="row g-4 justify-content-center">
        <div class="col-md-5">
          <div class="card h-100 shadow-sm">
            <div class="card-body p-4">
              <h3 class="card-title text-primary-emphasis">Bacong Health Center</h3>
              <p class="card-text"><strong>Address:</strong> Bacong, Dumangas</p>
              <p class="card-text"><strong>Contact:</strong> (033) 123-4567</p>
              <p class="card-text"><strong>Email:</strong> healthcenter@bacong.gov</p>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="card h-100 shadow-sm">
            <div class="card-body p-4">
              <h3 class="card-title text-primary-emphasis">Barangay Hall</h3>
              <p class="card-text"><strong>Contact:</strong> (033) 987-6543</p>
              <p class="card-text"><strong>Email:</strong> barangaybacong@gmail.com</p>
              <p class="card-text mt-3">
                <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="btn btn-primary">Facebook</a>
                <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="btn btn-success">Google Maps</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php
// Include the public footer
include_once __DIR__ . '/../../includes/footer_public.php';
?>

<script>
// Initialize AOS with desired options for the home page
if (typeof AOS !== 'undefined') {
  AOS.init({ duration: 1000, once: true });
}

// Smooth scroll for in-page anchors when already on the home page
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('a[href*="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = anchor.getAttribute('href');
      if (!href) return;
      // Only intercept if the link points to the home page anchor (contains '?page=home#')
      if (href.indexOf('?page=home#') !== -1) {
        // If current URL already contains page=home, prevent navigation and smooth scroll
        if (window.location.search.indexOf('page=home') !== -1) {
          e.preventDefault();
          var hash = href.split('#')[1];
          if (!hash) return;
          var target = document.getElementById(hash);
          if (target) {
            target.scrollIntoView({behavior:'smooth', block:'start'});
          }
        }
        // else let the browser navigate to the home page + anchor
      }
    });
  });
});
</script>