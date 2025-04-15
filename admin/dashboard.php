<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../assets/connection/connection.php'); 


if (!isset($_SESSION['aid'])) {
    header("Location: ../quest/login.php");
    exit;
}




$searchTerm = $_GET['search'] ?? '';
$searchParam = "%$searchTerm%";
$bakersQuery = "SELECT * FROM bakers WHERE name LIKE $searchParam";
$usersQuery = "SELECT * FROM users WHERE name LIKE $searchParam";
if(!empty($searchTerm)){
    $usersResult=$conn->query($usersQuery);
    $bakersResul=$conn->query($bakersQuery);
}
$countQuery = "
    SELECT 
        (SELECT COUNT(*) FROM users) AS userCount,
        (SELECT COUNT(*) FROM bakers) AS bakerCount,
        (SELECT COUNT(*) FROM orders) AS orderCount,
        (SELECT COUNT(*) FROM products) AS productCount
";

$countResult = $conn->query($countQuery);
$row = $countResult->fetch_assoc();


$userCount = $row['userCount'];
$bakerCount = $row['bakerCount'];
$orderCount = $row['orderCount'];
$productCount = $row['productCount'];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #2c2c2c;
            color: #fff;
        }
        nav {
            height: 88px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        nav a.navbar {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
        }
        nav img {
            height: 70px;
        }
        #navAdmin {
            font-size: 30px;
            margin-left: 10px;
        }
        .dashboard-container {
            display: flex;
        }
        .sidebar {
            width: 400px;
            background-color: #333;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            font-size: 25px;
        }
        .sidebar a:hover {
            color: #fff;
        }
        .content {
            flex: 1;
            padding: 30px;
        }
        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .stat-box {
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 200px;
        }
        .chart-container {
            width: 100%;
            max-width: 700px;
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="nav_container">
    <nav>
        <a class="navbar" href="#"><img src="/sugared/PROJECT/assets/img/logo.png" alt="SUGARED"><span id="navAdmin">Admin</span></a>
    </nav>
</div>

<div class="dashboard-container">
   
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="./manage_users.php">Manage Users</a>
        <a href="./manage_bakers.php">Manage Bakers</a>
        <a href="./reports.php">Reports</a>
        <a href="./login.php">Add new Admin</a>
    </div>

   
    <div class="content">
        <h1>Admin Dashboard</h1>
        
        
        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p id="userCount">--</p>
            </div>
            <div class="stat-box">
                <h3>Total Bakers</h3>
                <p id="bakerCount">--</p>
            </div>
            <div class="stat-box">
                <h3>Total Orders</h3>
                <p id="orderCount">--</p>
            </div>
            <div class="stat-box">
                <h3>Total Products</h3>
                <p id="productCount">--</p>
            </div>
        </div>

        <div class="chart-container">
            <h3>User and Baker Distribution</h3>
            <canvas id="userBakerChart"></canvas>
        </div>
    </div>
</div>

<script>
   
    let userCount = <?php echo $userCount; ?>;   
    let bakerCount = <?php echo $bakerCount; ?>;  
    let orderCount = <?php echo $orderCount; ?>; 
    let productCount = <?php echo $productCount; ?>;

    document.getElementById('userCount').textContent = userCount;
    document.getElementById('bakerCount').textContent = bakerCount;
    document.getElementById('orderCount').textContent = orderCount;
    document.getElementById('productCount').textContent = productCount;

    const ctx1 = document.getElementById('userBakerChart').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Users', 'Bakers'],
            datasets: [{
                data: [userCount, bakerCount],
                backgroundColor: ['#4e79a7', '#e15759'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>

</body>
</html>
