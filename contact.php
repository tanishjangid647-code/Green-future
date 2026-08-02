<?php
$page_title = "Contact & Support";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    set_flash('success', 'Thank you! Your message has been sent to Green Future HQ.');
    header('Location: ' . base_url('contact.php'));
    exit;
}
?>

<div class="container py-5">
  <div class="row g-5">
    <!-- Contact Info & Form -->
    <div class="col-lg-6">
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2">Get in Touch</span>
      <h2 class="fw-bold mb-3">Contact Organization HQ</h2>
      <p class="text-muted mb-4">Have questions about corporate CSR tree plantation drives, school campaigns, or volunteer partnerships? Drop us a message.</p>

      <form action="<?php echo base_url('contact.php'); ?>" method="POST" class="glass-card p-4 rounded-4">
        <div class="mb-3">
          <label class="form-label font-weight-semibold">Your Full Name *</label>
          <input type="text" name="name" class="form-control" placeholder="Deepak Kumar" required>
        </div>
        <div class="mb-3">
          <label class="form-label font-weight-semibold">Email Address *</label>
          <input type="email" name="email" class="form-control" placeholder="deepak@example.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label font-weight-semibold">Subject</label>
          <input type="text" name="subject" class="form-control" placeholder="CSR Partnership / Volunteer Inquiry" required>
        </div>
        <div class="mb-4">
          <label class="form-label font-weight-semibold">Message</label>
          <textarea name="message" class="form-control" rows="4" placeholder="How can we assist your green mission?" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary-green w-100 py-2">
          <i class="fas fa-paper-plane me-2"></i> Send Message
        </button>
      </form>
    </div>

    <!-- FAQ Accordion -->
    <div class="col-lg-6">
      <h3 class="fw-bold mb-3"><i class="fas fa-question-circle text-success me-2"></i> Frequently Asked Questions</h3>
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item rounded-3 mb-2 border">
          <h2 class="accordion-header">
            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
              How are trees tracked after plantation?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body small text-muted">
              Every sapling is assigned a unique QR code tag synced with GPS coordinates. Local volunteer teams conduct bi-weekly health inspections and upload growth photo logs to your dashboard.
            </div>
          </div>
        </div>

        <div class="accordion-item rounded-3 mb-2 border">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              How do I download my plantation certificate?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body small text-muted">
              Once your campaign drive is marked completed by organizers, a digital QR-verified certificate is automatically rendered in your 'Certificates' tab.
            </div>
          </div>
        </div>

        <div class="accordion-item rounded-3 mb-2 border">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              Can corporations organize CSR drives?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body small text-muted">
              Yes! We provide dedicated corporate CSR dashboard portals, custom branding on certificates, and bulk tree tracking metrics.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
