<?php
// Include the analytics functions
require_once 'analytics.php';

// Get the latest counts
$install_count = get_install_count();
$usage_count = get_usage_count();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geo IRL Weather - Usage Statistics</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #1a202c;
            margin-bottom: 1.5rem;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        .stat-item {
            padding: 1rem 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }
        .stat-item h2 {
            font-size: 1.125rem;
            color: #4a5568;
            margin: 0 0 0.5rem 0;
        }
        .stat-item p {
            font-size: 2.25rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Usage Statistics</h1>
        <div class="stats">
            <div class="stat-item">
                <h2>Total Installations</h2>
                <p><?php echo number_format($install_count); ?></p>
            </div>
            <div class="stat-item">
                <h2>Total API Usage</h2>
                <p><?php echo number_format($usage_count); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
