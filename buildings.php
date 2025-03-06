<?php
session_start();

// Initialize session data
if (!isset($_SESSION['smart_home'])) {
    $_SESSION['smart_home'] = [
        'rooms' => [
            'Main Bedroom' => [
                'temperature' => 25,
                'humidity' => 8,
                'devices' => [
                    'Air Conditioner' => 'Discovery HD',
                    'Television' => 'On',
                    'Door Lock' => 'UNLOCKED',
                    'Curtain' => 'CLOSED',
                    'Windows' => 'Closed'
                ],
                'preset' => 'Custom Preset 01'
            ],
            'Bathroom' => [
                'temperature' => 22,
                'humidity' => 13,
                'devices' => []
            ],
            'Kitchen' => [
                'temperature' => 24,
                'humidity' => 11,
                'devices' => []
            ]
        ],
        'current_room' => 'Main Bedroom',
        'media' => [
            'station' => 'LIBERTY FM',
            'volume' => 60
        ]
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_room'])) {
        $_SESSION['smart_home']['current_room'] = $_POST['room'];
    }
    if (isset($_POST['toggle_device'])) {
        $room = $_POST['room'];
        $device = $_POST['device'];
        $_SESSION['smart_home']['rooms'][$room]['devices'][$device] = 
            $_SESSION['smart_home']['rooms'][$room]['devices'][$device] === 'On' ? 'Off' : 'On';
    }
}

$data = $_SESSION['smart_home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home Dashboard</title>
    
    <!-- Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2A2D37;
            --secondary-color: #5D616D;
            --accent-color: #4A90E2;
            --bg-color: #F5F6FA;
        }

        body {
            background: var(--bg-color);
            font-family: 'Inter', sans-serif;
        }

        .dashboard-header {
            background: white;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .room-card {
            background: white;
            border-radius: 15px;
            transition: transform 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .room-card.active {
            border-color: var(--accent-color);
            transform: scale(1.02);
        }

        .device-pill {
            background: #F0F3F8;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .media-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        .temperature-display {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .humidity-badge {
            background: #E8F0FE;
            color: var(--accent-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .preset-mode {
            background: var(--accent-color);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .search-box {
            background: #F5F6FA;
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4">
        <!-- Header -->
        <header class="dashboard-header mb-4 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 fw-bold mb-0">SMART HOME</h1>
                <div class="search-box w-50">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" class="border-0 bg-transparent w-90 ms-2" 
                           placeholder="Search room">
                </div>
            </div>
        </header>

        <!-- Room Cards -->
        <div class="row g-4 mb-5">
            <?php foreach ($data['rooms'] as $room => $details): ?>
                <div class="col-md-4">
                    <div class="room-card p-4 <?= $data['current_room'] === $room ? 'active' : '' ?>"
                         onclick="location.href='?room=<?= urlencode($room) ?>'">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h5 fw-bold mb-0"><?= $room ?></h3>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <div class="text-muted small">TEMPERATURE</div>
                                <div class="temperature-display"><?= $details['temperature'] ?>°c</div>
                            </div>
                            <div>
                                <div class="text-muted small">HUMIDITY</div>
                                <div class="humidity-badge"><?= $details['humidity'] ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Selected Room Details -->
        <?php $current = $data['rooms'][$data['current_room']]; ?>
        <div class="row g-4" data-aos="fade-up">
            <div class="col-md-8">
                <div class="bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 fw-bold"><?= $data['current_room'] ?></h3>
                        <div class="preset-mode"><?= $current['preset'] ?? 'Default Mode' ?></div>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($current['devices'] as $device => $status): ?>
                            <div class="col-md-6">
                                <div class="device-pill d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="status-indicator bg-<?= $status === 'On' ? 'success' : 'secondary' ?>"></span>
                                        <?= $device ?>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="room" value="<?= $data['current_room'] ?>">
                                        <input type="hidden" name="device" value="<?= $device ?>">
                                        <button type="submit" name="toggle_device" 
                                                class="btn btn-sm <?= $status === 'On' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                            <?= $status ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Climate Info -->
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-3">
                    <div class="text-center mb-4">
                        <div class="temperature-display">25°c</div>
                        <div class="text-muted">OCTOBER 30, 2016</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>TEMPERATURE</span>
                            <span>25°c</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>HUMIDITY</span>
                            <span>08%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Controls -->
        <div class="media-controls">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold"><?= $data['media']['station'] ?></div>
                    <div class="text-muted small">Audio System</div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="temperature-display">25°c</div>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary rounded-circle"><i class="fas fa-minus"></i></button>
                        <button class="btn btn-outline-secondary rounded-circle"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            offset: 120,
            once: true
        });
    </script>
</body>
</html>
