<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLS VDIT Smart Analytics Dashboard</title>
    
    <!-- Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Power BI Embed Script -->
    <script src="https://npmcdn.com/es6-promise@3.2.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/powerbi-client@2.19.0/dist/powerbi.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2A2D37;
            --secondary-color: #5D616D;
            --accent-color: #4A90E2;
            --bg-color: #F5F6FA;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-color);
        }

        /* Side Panel Styles */
        .side-panel {
            width: 250px;
            background-color: var(--primary-color);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .side-panel h2 {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .side-panel ul li a i {
           margin-right: 12px;
           font-size: 18px;
           width: 24px;
           text-align: center;
        }
        
        .side-panel ul li a.active,
        .side-panel ul li a:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-color);
        }

        .header {
            background-color: var(--primary-color);
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .dashboard-header {
            background: white;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Power BI Dashboard Styles */
        .powerbi-container {
            width: 100%;
            min-height: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .powerbi-container:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        #powerbi-dashboard {
            width: 100%;
            height: 650px;
            border: none;
        }
        
        /* Dashboard Controls */
        .dashboard-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .dashboard-filter {
            flex: 1;
            min-width: 200px;
        }
        
        /* Info Boxes */
        .info-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .info-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .info-title {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .info-change {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .info-change.up {
            color: #28a745;
        }
        
        .info-change.down {
            color: #dc3545;
        }
        
        /* Loading State */
        .loading-state {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
            background: white;
            border-radius: 15px;
        }
        
        .spinner {
            width: 3rem;
            height: 3rem;
            color: var(--accent-color);
        }
        
        .logout-link {
            text-align: right;
            margin: 10px 0;
        }

        .logout-link a {
            color: #fff;
            text-decoration: none;
            background-color: #ff4d4d;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: inline-block;
        }

        .logout-link a:hover {
            background-color: #e04545;
            transform: translateY(-2px);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .side-panel {
                width: 70px;
                overflow: hidden;
            }
            
            .side-panel h2,
            .side-panel ul li a span {
                display: none;
            }
            
            .side-panel ul li a {
                justify-content: center;
                padding: 12px 5px;
            }
            
            .side-panel ul li a i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .main-content {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            #powerbi-dashboard {
                height: 500px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .side-panel {
                width: 100%;
                min-height: auto;
                flex-direction: row;
                padding: 10px;
            }
            
            .side-panel h2 {
                display: none;
            }
            
            .side-panel ul {
                display: flex;
                justify-content: space-around;
                margin: 0;
            }
            
            .side-panel ul li {
                margin: 0;
            }
            
            .side-panel ul li a {
                padding: 10px;
                flex-direction: column;
                font-size: 0.7rem;
            }
            
            .side-panel ul li a i {
                margin-right: 0;
                margin-bottom: 5px;
                font-size: 16px;
            }
            
            .main-content {
                padding-bottom: 20px;
            }
            
            #powerbi-dashboard {
                height: 400px;
            }
        }

        @media (max-width: 576px) {
            .header h1 {
                font-size: 1.5rem;
            }
            
            .info-value {
                font-size: 1.5rem;
            }
            
            #powerbi-dashboard {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <!-- Side Panel -->
    <div class="side-panel">
        <h2>KLS VDIT</h2>
        <ul>
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-building"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="buildings.php">
                <i class="fas fa-tachometer-alt"></i>
                    <span>Building</span>
                </a>
            </li>
            <li>
                <a href="camera.php">
                <i class="fa fa-video-camera" aria-hidden="true"></i>
                    <span>Camera</span>
                </a>
            </li>
            <li>
                <a href="analytics.php" class="active">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Network for Energy and Internet of Things</h1>
            <div class="logout-link">
                <a href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <div class="dashboard-header">
            <h3><i class="fas fa-chart-bar"></i> Energy Analytics Dashboard</h3>
            <p class="text-muted">View real-time and historical energy consumption data</p>
            
            <div class="dashboard-controls mt-3">
                <div class="dashboard-filter">
                    <label for="time-filter" class="form-label">Time Period</label>
                    <select class="form-select" id="time-filter">
                        <option value="today">Today</option>
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="dashboard-filter">
                    <label for="building-filter" class="form-label">Building</label>
                    <select class="form-select" id="building-filter">
                        <option value="all" selected>All Buildings</option>
                        <option value="building1">Main Building</option>
                        <option value="building2">Engineering Block</option>
                        <option value="building3">Science Complex</option>
                    </select>
                </div>
                <div class="dashboard-filter">
                    <label for="metric-filter" class="form-label">Metric</label>
                    <select class="form-select" id="metric-filter">
                        <option value="power" selected>Power Consumption (kW)</option>
                        <option value="energy">Energy Usage (kWh)</option>
                        <option value="cost">Energy Cost</option>
                        <option value="efficiency">Energy Efficiency</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Power BI Dashboard Container -->
        <div class="powerbi-container">
            <div id="powerbi-dashboard"></div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="info-box">
                    <div class="info-title">Current Power Demand</div>
                    <div class="info-value" id="current-power">24.7 kW</div>
                    <div class="info-change up">
                        <i class="fas fa-arrow-up"></i> 12% from yesterday
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <div class="info-title">Today's Consumption</div>
                    <div class="info-value" id="today-consumption">187 kWh</div>
                    <div class="info-change down">
                        <i class="fas fa-arrow-down"></i> 8% from average
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <div class="info-title">Monthly Cost</div>
                    <div class="info-value" id="monthly-cost">₹8,245</div>
                    <div class="info-change up">
                        <i class="fas fa-arrow-up"></i> 15% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Power BI Embed Code -->
    <script>
        // Initialize AOS animations
        AOS.init();
        
        // Configuration for Power BI embedding
        // In a production environment, you should get these values from your server
        const embedConfig = {
            type: 'dashboard',
            tokenType: 'Embed',
            accessToken: '<?php echo getEmbedToken(); ?>', // You need to implement this function
            embedUrl: 'https://app.powerbi.com/dashboardEmbed?dashboardId=YOUR_DASHBOARD_ID',
            id: 'YOUR_DASHBOARD_ID',
            settings: {
                filterPaneEnabled: true,
                navContentPaneEnabled: true,
                background: 'transparent'
            }
        };

        // Embed the dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardContainer = document.getElementById('powerbi-dashboard');
            
            // Show loading state
            dashboardContainer.innerHTML = `
                <div class="loading-state">
                    <div class="spinner-border spinner" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Embed the dashboard after a short delay (simulating token fetch)
            setTimeout(function() {
                try {
                    const dashboard = powerbi.embed(dashboardContainer, embedConfig);
                    
                    // Handle dashboard events
                    dashboard.on('loaded', function() {
                        console.log('Dashboard loaded successfully');
                    });
                    
                    dashboard.on('error', function(event) {
                        console.error('Power BI error:', event.detail);
                        dashboardContainer.innerHTML = `
                            <div class="loading-state">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Failed to load dashboard. Please try again later.
                                </div>
                            </div>
                        `;
                    });
                    
                    // Handle filter changes from UI
                    document.getElementById('time-filter').addEventListener('change', function(e) {
                        applyDashboardFilter(dashboard, 'time', e.target.value);
                    });
                    
                    document.getElementById('building-filter').addEventListener('change', function(e) {
                        applyDashboardFilter(dashboard, 'building', e.target.value);
                    });
                    
                    document.getElementById('metric-filter').addEventListener('change', function(e) {
                        applyDashboardFilter(dashboard, 'metric', e.target.value);
                    });
                    
                } catch (error) {
                    console.error('Embedding error:', error);
                    dashboardContainer.innerHTML = `
                        <div class="loading-state">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> Error initializing dashboard. Please check your configuration.
                            </div>
                        </div>
                    `;
                }
            }, 1000);
        });
        
        // Function to apply filters to the dashboard
        function applyDashboardFilter(dashboard, filterType, value) {
            let filter;
            
            switch(filterType) {
                case 'time':
                    // Example of time filter - adjust based on your dashboard
                    const now = new Date();
                    let startDate, endDate = now;
                    
                    if (value === 'today') {
                        startDate = new Date();
                        startDate.setHours(0, 0, 0, 0);
                    } else if (value === 'week') {
                        startDate = new Date();
                        startDate.setDate(now.getDate() - 7);
                    } else if (value === 'month') {
                        startDate = new Date();
                        startDate.setMonth(now.getMonth() - 1);
                    } else if (value === 'year') {
                        startDate = new Date();
                        startDate.setFullYear(now.getFullYear() - 1);
                    }
                    
                    filter = {
                        $schema: "http://powerbi.com/product/schema#basic",
                        target: {
                            table: "EnergyData",
                            column: "Timestamp"
                        },
                        operator: "In",
                        values: [startDate, endDate]
                    };
                    break;
                    
                case 'building':
                    filter = {
                        $schema: "http://powerbi.com/product/schema#basic",
                        target: {
                            table: "EnergyData",
                            column: "Building"
                        },
                        operator: "In",
                        values: value === 'all' ? [] : [value]
                    };
                    break;
                    
                case 'metric':
                    // This would typically be handled by changing the visible visuals
                    // rather than applying a filter
                    console.log(`Metric changed to ${value}`);
                    return;
            }
            
            dashboard.setFilters([filter])
                .then(function() {
                    console.log(`Filter applied: ${filterType}=${value}`);
                })
                .catch(function(error) {
                    console.error(`Error applying filter:`, error);
                });
        }
        
        // Simulate real-time data updates
        setInterval(function() {
            // In a real application, you would fetch this data from your API
            const randomChange = (Math.random() * 2 - 1).toFixed(1);
            const currentPower = (24.7 + parseFloat(randomChange)).toFixed(1);
            document.getElementById('current-power').textContent = `${currentPower} kW`;
            
            const consumptionChange = (Math.random() * 10 - 5).toFixed(0);
            const todayConsumption = 187 + parseInt(consumptionChange);
            document.getElementById('today-consumption').textContent = `${todayConsumption} kWh`;
            
            const costChange = (Math.random() * 1000 - 500).toFixed(0);
            const monthlyCost = 8245 + parseInt(costChange);
            document.getElementById('monthly-cost').textContent = `₹${monthlyCost.toLocaleString()}`;
        }, 5000);
    </script>
</body>
</html>