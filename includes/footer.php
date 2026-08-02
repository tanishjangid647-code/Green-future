<!-- Carbon Calculator Modal -->
<div class="modal fade" id="carbonCalcModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-calculator me-2"></i> Carbon Footprint & Tree Offset Calculator</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="carbon-calc-form">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Weekly Car Driving (KM)</label>
              <input type="number" id="calc-km" class="form-control rounded-3" placeholder="e.g. 150" value="100">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Monthly Electricity (kWh)</label>
              <input type="number" id="calc-kwh" class="form-control rounded-3" placeholder="e.g. 250" value="200">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Flights per Year</label>
              <input type="number" id="calc-flights" class="form-control rounded-3" placeholder="e.g. 2" value="2">
            </div>
          </div>
          <button type="submit" class="btn btn-primary-green w-100 mt-4 py-2">
            <i class="fas fa-chart-line me-1"></i> Calculate Footprint & Required Trees
          </button>
        </form>
        <div id="carbon-calc-result"></div>
      </div>
    </div>
  </div>
</div>

<!-- Floating AI Chatbot Widget -->
<div id="chatbot-widget">
  <button id="chat-toggle-btn" class="chat-btn" title="Ask AI Eco Assistant">
    <i class="fas fa-robot"></i>
  </button>
  <div id="chat-box-container" class="chat-box d-none">
    <div class="chat-header">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-leaf text-warning fs-5"></i>
        <div>
          <h6 class="mb-0 fw-bold">EcoBot Assistant</h6>
          <small class="text-white-50">Online • Plantation Expert</small>
        </div>
      </div>
      <button id="chat-close-btn" class="btn-close btn-close-white small"></button>
    </div>
    <div id="chat-messages" class="chat-body">
      <div class="chat-msg bot">
        Hello! I am your AI Eco Assistant. How can I help you plant, track, or choose native trees today?
      </div>
    </div>
    <div class="p-3 border-top bg-white d-flex gap-2">
      <input type="text" id="chat-input" class="form-control form-control-sm rounded-pill" placeholder="Type a message...">
      <button id="chat-send-btn" class="btn btn-primary-green btn-sm rounded-circle px-3"><i class="fas fa-paper-plane"></i></button>
    </div>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-lg-4 col-md-6">
        <h5 class="text-white fw-bold mb-3"><i class="fas fa-tree text-success me-2"></i> Green Future</h5>
        <p class="small text-muted">Smart Tree Plantation & Campaign Management System empowering citizens, NGOs, and government organizations to build a carbon-neutral planet through digital tracking.</p>
        <div class="d-flex gap-2 mt-3">
          <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-twitter"></i></a>
          <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-instagram"></i></a>
          <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <h6 class="text-white fw-bold mb-3">Quick Links</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?php echo base_url('campaigns.php'); ?>"><i class="fas fa-chevron-right text-success me-1"></i> Active Drives</a></li>
          <li class="mb-2"><a href="<?php echo base_url('trees.php'); ?>"><i class="fas fa-chevron-right text-success me-1"></i> Tree Tracking</a></li>
          <li class="mb-2"><a href="<?php echo base_url('leaderboard.php'); ?>"><i class="fas fa-chevron-right text-success me-1"></i> Leaderboard</a></li>
          <li class="mb-2"><a href="<?php echo base_url('gallery.php'); ?>"><i class="fas fa-chevron-right text-success me-1"></i> Photo Gallery</a></li>
          <li class="mb-2"><a href="#" data-bs-toggle="modal" data-bs-target="#carbonCalcModal"><i class="fas fa-calculator text-warning me-1"></i> CO₂ Calculator</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="text-white fw-bold mb-3">Contact HQ</h6>
        <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt text-success me-2"></i> Green Future Foundation, Sector 4, Bandra Kurla Complex, Mumbai, MH</p>
        <p class="small text-muted mb-2"><i class="fas fa-envelope text-success me-2"></i> contact@greenfuture.org</p>
        <p class="small text-muted mb-2"><i class="fas fa-phone text-success me-2"></i> +91 (022) 2890-4500</p>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="text-white fw-bold mb-3">Green Headquarters</h6>
        <div class="rounded-3 overflow-hidden shadow-sm" style="height: 140px;">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.792473489115!2d72.8687!3d19.0660!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c8e14a84976d%3A0x6a05e26b1c4e951!2sBKC%20Mumbai!5e0!3m2!1sen!2sin!4v1700000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      </div>
    </div>
    <div class="border-top border-secondary pt-4 text-center small text-muted">
      <p class="mb-0">&copy; <?php echo date('Y'); ?> Green Future Inc. All rights reserved. Built for environmental impact.</p>
    </div>
  </div>
</footer>

<!-- JS Script Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/weather-ai.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/carbon-calc.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/chatbot.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/chart-config.js'); ?>"></script>
</body>
</html>
