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
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$donors = $stmt->fetchAll();

$totalDonorsStmt = $pdo->query("SELECT COUNT(*) FROM donors WHERE available = 1");
$totalActiveDonors = $totalDonorsStmt->fetchColumn();

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

            <form action="find _donors.php" method="GET" class="search-box">
                <select name="blood_group" id="bloodGroupFilter">
                    <option value="">All Blood Groups</option>
                    <?php 
                    $groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                    foreach ($groups as $group) {
                        $selected = ($selectedGroup === $group) ? 'selected' : '';
                        echo "<option value=\"$group\" $selected>$group</option>";
                    }
                    ?>
                </select>
                <input type="text" name="city" id="cityFilter" placeholder="Enter city (e.g., Jabalpur, Bhopal)..." value="<?= htmlspecialchars($searchCity) ?>">
                <button type="submit" class="btn-primary">Search Donors</button>
            </form>
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

            <div class="section-header">
                <h2>Available Blood Donors</h2>
                <a href="find _donors.php" style="color: #dc2626; font-weight: 600; text-decoration: none;">View All Donors -></a>
            </div>

            <div class="donor-grid" id="donorGrid">
                <?php if (empty($donors)): ?>
                    <p class="no-results">No donors available right now.</p>
                <?php else: ?>
                    <?php foreach ($donors as $donor): 
                        $cleanPhone = preg_replace('/[^0-9]/', '', $donor['phone']);
                        $waText = urlencode("Hello " . $donor['name'] . ", I found your contact on LifeTech. We urgently need " . $donor['blood_group'] . " blood in " . $donor['city'] . ". Are you available to donate?");
                    ?>
                        <div class="donor-card">
                            <div class="blood-badge"><?= htmlspecialchars($donor['blood_group']) ?></div>
                            <h3><?= htmlspecialchars($donor['name']) ?></h3>
                            <p class="city"><?= htmlspecialchars(($donor['area'] ? $donor['area'] . ', ' : '') . $donor['city']) ?></p>
                            <p class="meta">
                                Last donated: <?= $donor['last_donated'] ? date('d-m-Y', strtotime($donor['last_donated'])) : 'First-time donor' ?>
                            </p>
                            <div class="action-buttons">
                                <a href="tel:<?= htmlspecialchars($donor['phone']) ?>" class="btn-call">Call</a>
                                <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waText ?>" target="_blank" rel="noopener" class="btn-whatsapp">WhatsApp</a>
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