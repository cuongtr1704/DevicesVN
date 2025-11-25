<?php 
$breadcrumbs = [
    ['label' => 'About Us', 'url' => '']
];
?>

<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?php echo asset('css/about.css'); ?>">

<div class="container my-5">
    <!-- About Section -->
    <section class="about-intro mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">About DevicesVN</h1>
                <p class="lead">
                    Your trusted destination for premium laptops, gaming devices, phones, and accessories in Vietnam.
                </p>
                <p>
                    Since our establishment, DevicesVN has been committed to providing high-quality technology products 
                    with exceptional customer service. We partner with leading brands to bring you the latest innovations 
                    in technology at competitive prices.
                </p>
                <p>
                    Our mission is to make technology accessible to everyone, whether you're a professional, gamer, 
                    student, or tech enthusiast. We believe in quality, authenticity, and customer satisfaction above all.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="<?= asset('images/TVDHBK.jpg') ?>" 
                     alt="DevicesVN Store" 
                     class="img-fluid rounded shadow"
                     onerror="this.src='<?= asset('images/no-image.svg') ?>'">
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-us mb-5">
        <h2 class="text-center mb-4">Why Choose DevicesVN?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h4>100% Authentic</h4>
                    <p>All products are sourced directly from authorized distributors with full warranty.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-truck fa-3x text-primary"></i>
                    </div>
                    <h4>Fast Delivery</h4>
                    <p>Quick delivery across Vietnam with real-time tracking.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Our customer service team is always ready to help you.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-award fa-3x text-primary"></i>
                    </div>
                    <h4>Best Prices</h4>
                    <p>Competitive pricing with regular promotions and deals.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-undo fa-3x text-primary"></i>
                    </div>
                    <h4>Easy Returns</h4>
                    <p>Hassle-free 15-day return policy for your peace of mind.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-tools fa-3x text-primary"></i>
                    </div>
                    <h4>Warranty Service</h4>
                    <p>Comprehensive warranty coverage and repair services.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Policies -->
    <section class="policies-section mb-5">
        <h2 class="text-center mb-4">Our Policies</h2>
        <div class="accordion" id="policiesAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#shipping">
                        <i class="fas fa-shipping-fast me-2"></i> Shipping Policy
                    </button>
                </h2>
                <div id="shipping" class="accordion-collapse collapse show" data-bs-parent="#policiesAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li>Free shipping for orders over 5,000,000 VND</li>
                            <li>Standard delivery: 2-5 business days</li>
                            <li>Express delivery available in major cities</li>
                            <li>Real-time tracking for all orders</li>
                            <li>Signature required for high-value items</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#returns">
                        <i class="fas fa-undo me-2"></i> Return & Exchange Policy
                    </button>
                </h2>
                <div id="returns" class="accordion-collapse collapse" data-bs-parent="#policiesAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li>15-day return period from delivery date</li>
                            <li>Products must be in original condition with packaging</li>
                            <li>Free return shipping for defective items</li>
                            <li>Exchange or refund options available</li>
                            <li>Easy return process through our website</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#warranty">
                        <i class="fas fa-shield-alt me-2"></i> Warranty Policy
                    </button>
                </h2>
                <div id="warranty" class="accordion-collapse collapse" data-bs-parent="#policiesAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li>All products come with manufacturer warranty</li>
                            <li>Extended warranty options available</li>
                            <li>Authorized service centers nationwide</li>
                            <li>Quick warranty claim process</li>
                            <li>Replacement devices during repair period</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy">
                        <i class="fas fa-user-shield me-2"></i> Privacy Policy
                    </button>
                </h2>
                <div id="privacy" class="accordion-collapse collapse" data-bs-parent="#policiesAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li>Your data is encrypted and secure</li>
                            <li>We never share your information with third parties</li>
                            <li>Secure payment processing</li>
                            <li>Cookies used only for better experience</li>
                            <li>You can request data deletion anytime</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#payment">
                        <i class="fas fa-credit-card me-2"></i> Payment Methods
                    </button>
                </h2>
                <div id="payment" class="accordion-collapse collapse" data-bs-parent="#policiesAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li>Credit/Debit cards (Visa, MasterCard, JCB)</li>
                            <li>Bank transfer</li>
                            <li>Cash on delivery (COD)</li>
                            <li>E-wallets (Momo, ZaloPay, VNPay)</li>
                            <li>Installment plans available for select products</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info mb-5">
        <h2 class="text-center mb-4">Visit Our Stores</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <h4><i class="fas fa-map-marker-alt text-primary"></i> Hanoi Store</h4>
                    <p class="mb-2"><strong>Address:</strong> 123 Nguyen Trai, Thanh Xuan, Hanoi</p>
                    <p class="mb-2"><strong>Phone:</strong> 024-1234-5678</p>
                    <p class="mb-0"><strong>Hours:</strong> Mon-Sat: 8:00 AM - 9:00 PM</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <h4><i class="fas fa-map-marker-alt text-primary"></i> Ho Chi Minh Store</h4>
                    <p class="mb-2"><strong>Address:</strong> 456 Le Loi, District 1, HCMC</p>
                    <p class="mb-2"><strong>Phone:</strong> 028-9876-5432</p>
                    <p class="mb-0"><strong>Hours:</strong> Mon-Sat: 8:00 AM - 9:00 PM</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <h4><i class="fas fa-map-marker-alt text-primary"></i> Da Nang Store</h4>
                    <p class="mb-2"><strong>Address:</strong> 789 Bach Dang, Hai Chau, Da Nang</p>
                    <p class="mb-2"><strong>Phone:</strong> 0236-111-2222</p>
                    <p class="mb-0"><strong>Hours:</strong> Mon-Sat: 8:00 AM - 9:00 PM</p>
                </div>
            </div>
        </div>
    </section>
</div>
