<?php
require 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $blood_group  = $_POST['blood_group'] ?? '';
    $city         = trim($_POST['city'] ?? '');
    $area         = trim($_POST['area'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $last_donated = !empty($_POST['last_donated']) ? $_POST['last_donated'] : null;

    if ($name && $blood_group && $city && $phone) {
        $sql = "INSERT INTO donors (name, blood_group, city, area, phone, last_donated) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $blood_group, $city, $area, $phone, $last_donated);
$stmt->execute();
        header("Location: index.php?registered=1#results");
        exit;
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Donor | LifeTech</title>
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
                    <li><a href="find _donors.php">Find Donors</a></li>
                    <li><a href="index.php#compatibility">Compatibility</a></li>
                    <li><a href="register.php" class="btn-primary" style="color:#fff;">Become a Donor</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container register-split">
        <div class="register-info">
            <span class="badge">Join Our Lifesaving Network</span>
            <h1>Join LifeTech: Save Lives in Your Community.</h1>
            <p>Register in under 60 seconds. Patients and hospitals in your city can reach you directly via Call or WhatsApp during critical emergencies.</p>
            
        </div>

        <div class="form-card">
            <h2>Donor Registration</h2>
            <p class="form-sub">Enter your details to join the verified donor directory.</p>

            <?php if ($message): ?>
                <div class="alert"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Rahul Verma">
                    </div>
                    <div class="form-group">
                        <label>Blood Group *</label>
                        <select name="blood_group" required>
                            <option value="">Select</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $g): ?>
                                <option value="<?= $g ?>"><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" required placeholder="e.g., Jabalpur">
                    </div>
                    <div class="form-group">
                        <label>Area / Locality</label>
                        <input type="text" name="area" placeholder="e.g., Adhartal">
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone / WhatsApp Number *</label>
                    <input type="tel" name="phone" required placeholder="e.g., +91 9823456780">
                </div>

                <div class="form-group">
                    <label>Last Donation Date (Optional)</label>
                    <input type="date" name="last_donated" max="<?= date('Y-m-d') ?>">
                </div>

                <label class="checkbox-row" style="margin-bottom: 1.25rem;">
                    <input type="checkbox" required>
                    <span>I confirm I am 18+ years old and medically fit for blood donation.</span>
                </label>

                <button type="submit" class="btn-primary" style="width: 100%;">Complete Registration</button>
            </form>
        </div>
    </div>
</body>
</html>

