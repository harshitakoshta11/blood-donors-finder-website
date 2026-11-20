<?php
require 'db.php';

$selectedGroup = $_GET['blood_group'] ?? '';
$searchCity    = trim($_GET['city'] ?? '');
$isRegistered  = isset($_GET['registered']) && $_GET['registered'] == '1';
$sql = "SELECT id, name, blood_group, city, area, phone, last_donated FROM donors WHERE available = 1";
$params = [];

if ($selectedGroup !== '') {
    $sql .= " AND blood_group = ?";
    $params[] = $selectedGroup;
}

if ($searchCity !== '') {
    $sql .= " AND city LIKE ?";
    $params[] = "%$searchCity%";
}

$sql .= " ORDER BY created_at DESC LIMIT 6";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$donors = $result->fetch_all(MYSQLI_ASSOC);

$totalResult = $conn->query("SELECT COUNT(*) FROM donors WHERE available = 1");
$totalActiveDonors = $totalResult->fetch_row()[0];
$requestsResult = $conn->query("SELECT * FROM blood_requests ORDER BY created_at DESC LIMIT 6");
$requests = $requestsResult ? $requestsResult->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeTech | Blood Donor Finder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <div class="container nav-flex">
            <a href="index.php" class="logo">
                <svg width="32" height="32" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 6C32 6 12 28.5 12 42C12 53.0457 20.9543 62 32 62C43.0457 62 52 53.0457 52 42C52 28.5 32 6 32 6Z" fill="#DC2626"/>
                    <path d="M14 42H23L27 32L33 50L38 38H50" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Life<strong>Tech</strong></span>
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="find _donors.php">Find Donors</a></li>
                    <li><a href="#compatibility">Compatibility</a></li>
                    <a href="request.php" style="background:#dc2626; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:bold; margin-right:10px;">🩸 Request Blood</a>
                    
                    <li><a href="register.php" class="btn-outline">Become a Donor</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero" id="search">
        <div class="container hero-content">
            <span class="badge">24/7 Emergency Blood Network</span>
            <h1>Every Drop Saves a Life. Find a Donor Near You.</h1>
            <p>Connect directly with verified, voluntary blood donors in seconds during medical emergencies.</p>
<!-- AWARENESS NOTE - FIXED -->
<div style="background:#fef3c7; border:2px solid #f59e0b; border-left:8px solid #f59e0b; border-radius:12px; padding:16px 18px; display:flex; gap:12px; align-items:flex-start; text-align:left !important; width:100%; max-width:100%; box-sizing:border-box; margin-top:20px;">
  <div style="font-size:28px; line-height:1; flex-shrink:0;">⚠️</div>
  <div style="flex:1; min-width:0; text-align:left;">
    <div style="font-size:17px; font-weight:800; color:#92400e; margin-bottom:8px; text-align:left;">Important Awareness Note for Donors & Patients - Please Read Carefully</div>
    <ul style="margin:0; padding-left:18px; color:#78350f; font-size:14.5px; line-height:1.7; text-align:left; list-style:disc;">
      <li><b>Blood Donation is always FREE.</b> Never pay any money to any person, agent, or hospital for blood.</li>
      <li><b>Beware of Fraud Calls.</b> If anyone asks for money, advance payment, transport charge, or OTP for blood, do not pay. It is a fraud.</li>
      <li><b>Always Verify.</b> Before donating, confirm the patient name, hospital name, and doctor's requirement directly with the hospital.</li>
      <li><b>Do Not Start Donation Until You Receive Proof.</b> Do not start the donation process until the patient / family shares the Hospital's Urgent Requirement Form for Blood on WhatsApp. If they cannot share the hospital form, do not proceed.</li>
      <li><b>Donate Only at Authorized Hospitals / Blood Banks.</b> Do not donate at any unknown or private location.</li>
      <li><b>Do Not Share OTP / Bank Details.</b> LifeTech never asks for your OTP, UPI PIN, or bank details.</li>
      <li><b>If You Suspect Fraud,</b> immediately stop contact and report to the hospital authority.</li>
    </ul>
    <div style="margin-top:8px; font-size:13px; font-weight:700; color:#92400e; text-align:left;">Stay Safe - Your One Donation Can Save a Life, But Your Awareness Can Save You From Fraud.</div>
  </div>
</div>
<!-- AWARENESS NOTE END -->
        </div>
    </section>

    <section class="stats">
        <div class="container stats-grid">
            <div class="stat-item">
                <h3><?= number_format($totalActiveDonors) ?>+</h3>
                <p>Verified Active Donors</p>
            </div>
            <div class="stat-item">
                <h3>8</h3>
                <p>Blood Groups Covered</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Direct Emergency Access</p>
            </div>
        </div>
    </section>

    <section class="donors-section" id="results">
        <div class="container">
            <?php if ($isRegistered): ?>
                <div class="success-banner">
                    Thank you! You have been successfully registered as a LifeTech blood donor.
                </div>
            <?php endif; ?>

            <section class="donors-section" id="results">
  <div class="container">

    <div class="section-header">
      <h2>🚨 Urgent Blood Requests</h2>
      <a href="request.php" style="color:#dc2626; font-weight:600;">+ Add New Request →</a>
    </div>

    <div class="donor-grid" id="donorGrid">
      <?php if (empty($requests)): ?>
        <p class="no-results">NO URGENT BLOOD REQUEST AT THE MOMENT.PLEASE CHECK AGAIN LATER.</p>
      <?php else: ?>
        <?php foreach ($requests as $req): 
          $cleanPhone = preg_replace('/[^0-9]/', '', $req['phone']);
          $waText = urlencode("Hello, maine LifeTech pe aapki blood request dekhi - " . $req['blood_group'] . " chahiye. Main help kar sakta hu.");
        ?>
          <div class="donor-card">
            <div class="blood-badge"><?= htmlspecialchars($req['blood_group']) ?></div>
            <h3><?= htmlspecialchars($req['patient_name']) ?></h3>
            <p>📍 <?= htmlspecialchars($req['hospital'] ?? $req['city']) ?>, <?= htmlspecialchars($req['city']) ?></p>
            <p style="font-size:13px; color:#777;">
              Need: <?= htmlspecialchars($req['units'] ?? '1') ?> Unit • <?= htmlspecialchars($req['urgency'] ?? 'Urgent') ?>
            </p>
            <div style="display:flex; gap:10px; margin-top:10px;">
              <a href="tel:<?= $cleanPhone ?>" class="btn-call">Call</a>
          
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

    <section class="compat-section" id="compatibility">
        <div class="container">
            <h2>Blood Type Compatibility Guide</h2>
            <p style="color: #64748b;">Quickly check which blood groups can donate to or receive from each other.</p>
            <div style="overflow-x: auto;">
                <table class="compat-table">
                    <thead>
                        <tr>
                            <th>Blood Type</th>
                            <th>Can Donate To</th>
                            <th>Can Receive From</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>O-</strong> (Universal Donor)</td><td>All Blood Types</td><td>O-</td></tr>
                        <tr><td><strong>O+</strong></td><td>O+, A+, B+, AB+</td><td>O+, O-</td></tr>
                        <tr><td><strong>A-</strong></td><td>A-, A+, AB-, AB+</td><td>A-, O-</td></tr>
                        <tr><td><strong>A+</strong></td><td>A+, AB+</td><td>A+, A-, O+, O-</td></tr>
                        <tr><td><strong>B-</strong></td><td>B-, B+, AB-, AB+</td><td>B-, O-</td></tr>
                        <tr><td><strong>B+</strong></td><td>B+, AB+</td><td>B+, B-, O+, O-</td></tr>
                        <tr><td><strong>AB-</strong></td><td>AB-, AB+</td><td>AB-, A-, B-, O-</td></tr>
                        <tr><td><strong>AB+</strong> (Universal Recipient)</td><td>AB+</td><td>All Blood Types</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> LifeTech Blood Donor Network. Built with HTML5, CSS3, JS & PHP.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>