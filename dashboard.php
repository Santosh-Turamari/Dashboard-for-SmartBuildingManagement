<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>KLS VDIT Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        /* Side Panel Styles */
        .side-panel {
            width: 250px;
            background-color: #333;
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .side-panel h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .side-panel ul {
            list-style: none;
            padding: 0;
            flex: 1;
        }

        .side-panel ul li {
            margin: 15px 0;
        }

        .side-panel ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .side-panel ul li a i {
           margin-right: 10px; /* Add space between icon and text */
           font-size: 20px; /* Adjust icon size */
        }
        .side-panel ul li a:hover {
            color: #1a75ff;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #444;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .building-position {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .building-position h2 {
            margin-top: 0;
        }

        .rooms-table {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex:1;
        }

        .rooms-table h2 {
            margin-top: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f4f4f4;
        }

        .logout-link {
            text-align: right;
            margin: 10px;
        }

        .logout-link a {
            color: #fff;
            text-decoration: none;
            background-color: #ff4d4d;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Side Panel -->
    <!-- <div class="side-panel">
        <h2>Netergit</h2>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Buildings</a></li>
            <li><a href="#">User</a></li>
            <li><a href="#">Analytics</a></li>
        </ul>
    </div> -->

    <div class="side-panel">
    <h2>KLS VDIT</h2>
    <ul>
        <li>
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <!-- Dashboard Icon -->
                Dashboard
            </a>
        </li>
        <li>
            <a href="buildings.php">
                <i class="fas fa-building"></i> <!-- Buildings Icon -->
                Buildings
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-user"></i> <!-- User Icon -->
                User
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-chart-line"></i> <!-- Analytics Icon -->
                Analytics
            </a>
        </li>
    </ul>
</div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Network for Energy and Internet of Things</h1>
            <div class="logout-link">
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <!-- Building Position -->
        <div class="building-position">
            <h2>Building Position</h2>
             <!-- Google Map Embed -->
             <div class="map-container">
                    <img src="images/map.png" alt="Location" width="1200" height="300">
            </div>
        </div>

        <!-- Rooms Table -->
        <div class="rooms-table">
            <h2>Rooms Table</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Temperature</th>
                        <th>Luminosity</th>
                        <th>Humidity</th>
                        <th>eCo2</th>
                        <th>TVOC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['room']; ?></td>
                            <td><?php echo $row['temperature']; ?> &#8451;</td>
                            <td><?php echo $row['luminosity']; ?> Lux</td>
                            <td><?php echo $row['humidity']; ?>%</td>
                            <td><?php echo $row['eco2']; ?> ppm</td>
                            <td><?php echo $row['tvoc']; ?> ppb</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>