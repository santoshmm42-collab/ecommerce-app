<?php
$page_title = "Contact Us - ShopHub";
include 'includes/header.php';
?>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Contact Us</h1>
                    <p class="lead text-muted">We'd love to hear from you! Send us a message and we'll respond as soon as possible</p>
                </div>
                
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $name = isset($_POST["name"]) ? sanitize($_POST["name"]) : '';
                    $email = isset($_POST["email"]) ? sanitize($_POST["email"]) : '';
                    $message = isset($_POST["message"]) ? sanitize($_POST["message"]) : '';
                    
                    if (!empty($name) && !empty($email) && !empty($message)) {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
                        echo "<i class='fas fa-check-circle me-2'></i>";
                        echo "Thank you for contacting us! We'll get back to you soon.";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                        echo "</div>";
                    } else {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
                        echo "<i class='fas fa-exclamation-circle me-2'></i>";
                        echo "Please fill in all required fields.";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                        echo "</div>";
                    }
                }
                ?>
                
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <h4 class="mb-4" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Send us a Message</h4>
                                
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Your Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Your Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <select class="form-select" id="subject" name="subject">
                                                <option value="">Select Subject</option>
                                                <option value="order">Order Related</option>
                                                <option value="product">Product Query</option>
                                                <option value="payment">Payment Issue</option>
                                                <option value="technical">Technical Support</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Your Message *</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Tell us how we can help you..."></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Contact Information
                                </h5>
                                <div class="contact-info">
                                    <p class="mb-3">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <strong>Email:</strong><br>
                                        <a href="mailto:info@shophub.com">info@shophub.com</a>
                                    </p>
                                    <p class="mb-3">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <strong>Phone:</strong><br>
                                        <a href="tel:+919876543210">+91 98765 43210</a>
                                    </p>
                                    <p class="mb-3">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Address:</strong><br>
                                        ShopHub Headquarters<br>
                                        123, MG Road, Brigade Road<br>
                                        Bangalore, Karnataka 560001<br>
                                        India
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">
                                    <i class="fas fa-clock text-primary me-2"></i>Business Hours
                                </h5>
                                <div class="business-hours">
                                    <p class="mb-2"><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</p>
                                    <p class="mb-2"><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                                    <p class="mb-0"><strong>Sunday:</strong> Closed</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">
                                    <i class="fas fa-share-alt text-primary me-2"></i>Follow Us
                                </h5>
                                <div class="social-links">
                                    <a href="#" class="btn btn-outline-primary btn-sm me-2 mb-2">
                                        <i class="fab fa-facebook-f"></i> Facebook
                                    </a>
                                    <a href="#" class="btn btn-outline-info btn-sm me-2 mb-2">
                                        <i class="fab fa-twitter"></i> Twitter
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm me-2 mb-2">
                                        <i class="fab fa-instagram"></i> Instagram
                                    </a>
                                    <a href="#" class="btn btn-outline-primary btn-sm mb-2">
                                        <i class="fab fa-linkedin-in"></i> LinkedIn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="text-center mb-4" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Frequently Asked Questions</h3>
                
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">How can I track my order?</h6>
                                <p class="card-text text-muted small">Once your order is shipped, you'll receive a tracking number via email. You can use this to track your package on our website.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">What is your return policy?</h6>
                                <p class="card-text text-muted small">We offer a 7-day return policy on most items. Products must be unused and in original packaging.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Do you ship internationally?</h6>
                                <p class="card-text text-muted small">Currently, we ship within India. We're working on international shipping and will update soon.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">How can I cancel my order?</h6>
                                <p class="card-text text-muted small">You can cancel your order within 24 hours of placement. Contact our support team for assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
