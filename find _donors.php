<?php
require 'db.php';

$selectedGroup = $_GET['blood_group'] ?? '';
$searchCity    = trim($_GET['city'] ?? '');
$onlyAvailable = isset($_GET['available']) ? (int)$_GET['available'] : 1;

$sql = "SELECT * FROM donors WHERE 1=1";
$params = [];

if ($onlyAvailable === 1) {
    $sql .= " AND available = 1";
}
if ($selectedGroup !== '') {
    $sql .= " AND blood_group = ?";
    $params[] = $selectedGroup;
}
if ($searchCity !== '') {
    $sql .= " AND (city LIKE ? OR area LIKE ?)";
    $params[] = "%$searchCity%";
    $params[] = "%$searchCity%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$donors = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Donors | LifeTech</title>
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="find _donors.php" class="active">Find Donors</a></li>
                    <li><a href="index.php#compatibility">Compatibility</a></li>
                    <li><a href="register.php" class="btn-outline">Become a Donor</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container find-layout">
        <aside class="filter-sidebar">
            <h3>Filter Donors</h3>
            <form method="GET" action="find _donors.php">
                <label class="filter-label">Blood Group</label>
                <div class="pill-grid">
                    <label class="pill <?= $selectedGroup === '' ? 'selected' : '' ?>">
                        <input type="radio" name="blood_group" value="" <?= $selectedGroup === '' ? 'checked' : '' ?>> All
                    </label>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $g): ?>
                        <label class="pill <?= $selectedGroup === $g ? 'selected' : '' ?>">
                            <input type="radio" name="blood_group" value="<?= $g ?>" <?= $selectedGroup === $g ? 'checked' : '' ?>> <?= $g ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <label class="filter-label" style="margin-top: 1.25rem;">City / Area</label>
                <input type="text" name="city" class="sidebar-input" placeholder="e.g., Jabalpur" value="<?= htmlspecialchars($searchCity) ?>">

                <label class="checkbox-row">
                    <input type="checkbox" name="available" value="1" <?= $onlyAvailable === 1 ? 'checked' : '' ?>>
                    <span>Only Available Now</span>
                </label>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Apply Filters</button>
                <a href="find _donors.php" class="reset-link">Clear Filters</a>
            </form>
        </aside>

        <main class="results-main">
            <div class="section-header">
                <h2>Showing <?= count($donors) ?> Verified Donors <?= $searchCity ? 'in ' . htmlspecialchars($searchCity) : '' ?></h2>
            </div>

            <div class="donor-grid two-col">
                <?php if (empty($donors)): ?>
                    <p class="no-results">No donors matched your filter criteria.</p>
                <?php else: ?>
                    <?php foreach ($donors as $donor): 
                        $cleanPhone = preg_replace('/[^0-9]/', '', $donor['phone']);
                        $waText = urlencode("Hello " . $donor['name'] . ", we urgently need " . $donor['blood_group'] . " blood in " . $donor['city'] . ". Found you on LifeTech.");
                    ?>
                        <div class="donor-card">
                            <div class="blood-badge"><?= htmlspecialchars($donor['blood_group']) ?></div>
                            <h3><?= htmlspecialchars($donor['name']) ?></h3>
                            <p class="city"><?= htmlspecialchars(($donor['area'] ? $donor['area'] . ', ' : '') . $donor['city']) ?></p>
                            <p class="status-avail">Available Now</p>
                            <p class="meta">Last Donated: <?= $donor['last_donated'] ? date('d/m/Y', strtotime($donor['last_donated'])) : 'New Donor' ?></p>
                            <div class="action-buttons">
                                <a href="tel:<?= htmlspecialchars($donor['phone']) ?>" class="btn-call">Call</a>
                                <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waText ?>" target="_blank" rel="noopener" class="btn-whatsapp">WhatsApp</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>

