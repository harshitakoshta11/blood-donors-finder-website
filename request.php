<?php
include 'db.php';
$success = false;
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $patient_name = trim($_POST['patient_name']);
  $blood_group  = $_POST['blood_group'];
  $hospital     = trim($_POST['hospital']);
  $city         = trim($_POST['city']);
  $phone        = trim($_POST['phone']);
  $units        = $_POST['units'];
  $urgency      = $_POST['urgency'];

  if($patient_name == "" || $phone == ""){
    $error = "Please Patient Name aur Phone bharo.";
  } else {
    // Phone se sirf number nikalna
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $stmt = $conn->prepare("INSERT INTO blood_requests (patient_name, blood_group, hospital, city, phone, units, urgency) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("sssssis", $patient_name, $blood_group, $hospital, $city, $phone, $units, $urgency);
    if($stmt->execute()){
      $success = true;
    } else {
      $error = "Error: " . $conn->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Blood - LifeTech</title>
<style>
*{box-sizing:border-box; font-family:Arial, sans-serif;}
body{margin:0; background:#fef2f2;}
.header{background:#fff; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.logo{font-size:22px; font-weight:800; color:#111;}
.logo span{color:#dc2626;}
.header a{text-decoration:none; color:#333; font-weight:600;}
.container{max-width:600px; margin:30px auto; background:#fff; padding:25px; border-radius:16px; box-shadow:0 5px 20px rgba(0,0,0,0.08);}
h2{margin:0 0 5px 0;}
p.sub{color:#666; margin:0 0 20px 0;}
label{display:block; font-weight:700; margin:14px 0 6px 0; font-size:14px;}
input, select{width:100%; padding:12px; border:1px solid #ddd; border-radius:10px; font-size:15px; outline:none;}
input:focus, select:focus{border-color:#dc2626;}
.row{display:flex; gap:15px;}
.row > div{flex:1;}
.btn{width:100%; background:#dc2626; color:#fff; border:none; padding:14px; border-radius:10px; font-size:16px; font-weight:700; margin-top:20px; cursor:pointer;}
.success{background:#dcfce7; color:#166534; padding:12px; border-radius:10px; text-align:center; font-weight:600;}
.error{background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px; text-align:center;}
.back{display:block; text-align:center; margin-top:15px; color:#dc2626; text-decoration:none; font-weight:600;}
</style>
</head>
<body>

<div class="header">
  <div class="logo">🩸 Life<span>Tech</span></div>
  <a href="index.php">← Home</a>
</div>

<div class="container">
  <h2>🩸 Request Blood</h2>
  <p class="sub">Please Enter Correct Information.</p>

  <?php if($success): ?>
    <div class="success">✅ Request Successfully Submitted.</div>
    <a href="index.php" class="back">check your request in homepage</a>
  <?php else: ?>

  <?php if($error != "") echo "<div class='error'>$error</div>"; ?>

  <form method="POST" action="request.php">
    <label>Patient Name *</label>
    <input type="text" name="patient_name" placeholder="Ex:manoj gupta" required>

    <div class="row">
      <div>
        <label>Blood Group *</label>
        <select name="blood_group" required>
          <option value="">Select</option>
          <option>A+</option><option>A-</option>
          <option>B+</option><option>B-</option>
          <option>O+</option><option>O-</option>
          <option>AB+</option><option>AB-</option>
        </select>
      </div>
      <div>
        <label>Units Required?</label>
        <select name="units">
          <option>1</option><option>2</option><option>3</option><option>4</option>
        </select>
      </div>
    </div>

    <label>Hospital Name *</label>
    <input type="text" name="hospital" placeholder="Ex: Jabalpur Hospital" required>

    <label>City *</label>
    <input type="text" name="city" placeholder="Ex: Jabalpur" required>

    <label>Contact Phone *</label>
    <input type="tel" name="phone" placeholder="10 digit mobile number" pattern="[0-9]{10}" maxlength="10" required>

    <label>Urgency</label>
    <select name="urgency">
      <option>Urgent need</option>
      <option>Within 24 Hours</option>
      <option>Within 2-3 Days</option>
    </select>

    <button type="submit" class="btn">🚨 Submit Blood Request</button>
  </form>
  <?php endif; ?>
</div>

</body>
</html>