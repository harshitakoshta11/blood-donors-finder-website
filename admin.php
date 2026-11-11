<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Admin Panel - All Donors</h1>
        <a href="index.php" class="btn">Back to Home</a>
    </div>
    <div class="container">
    <table border="1" style="width:90%; margin:20px auto; background:white; border-collapse:collapse; text-align:center;">
        <tr style="background:#d32f2f; color:white;">
            <th>ID</th><th>Name</th><th>Blood Group</th><th>City</th><th>Phone</th><th>Action</th>
        </tr>
        <?php
        $sql = "SELECT * FROM donors ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)){
            echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['name']."</td>
            <td>".$row['blood_group']."</td>
            <td>".$row['city']."</td>
            <td>".$row['phone']."</td>
            <td><a href='delete.php?id=".$row['id']."' style='color:red;'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>
    </div>
</body>
</html>