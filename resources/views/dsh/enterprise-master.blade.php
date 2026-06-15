<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise ERP - Carpet Manufacturing</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS for Glassmorphism -->
    <style>
        :root {
            --bg-color: #f4f6f9;
            --text-color: #333;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --navy-blue: #0A192F;
            --emerald-green: #10B981;
            --gold-accent: #F59E0B;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at top right, #e2e8f0 0%, #f4f6f9 100%);
            color: var(--text-color);
            min-height: 100vh;
        }

        /* Glassmorphism Classes */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .kpi-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--navy-blue);
        }

        .kpi-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            background: -webkit-linear-gradient(45deg, var(--navy-blue), var(--emerald-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar {
            background: var(--navy-blue);
            color: white;
            min-height: 100vh;
        }
        
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
        }
    </style>
    @yield('styles')
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar d-none d-md-block">
            <div class="p-4">
                <h4 class="text-white fw-bold"><i class="fa-solid fa-industry me-2"></i> Carpet ERP</h4>
            </div>
            <div class="px-3">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie me-2"></i> Executive</a>
                <a href="/dashboard/production" class="{{ request()->is('dashboard/production') ? 'active' : '' }}"><i class="fa-solid fa-cogs me-2"></i> Production</a>
                <a href="/dashboard/inventory" class="{{ request()->is('dashboard/inventory') ? 'active' : '' }}"><i class="fa-solid fa-boxes-stacked me-2"></i> Inventory</a>
                <a href="/dashboard/sales" class="{{ request()->is('dashboard/sales') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping me-2"></i> Sales</a>
                <a href="/dashboard/purchases" class="{{ request()->is('dashboard/purchases') ? 'active' : '' }}"><i class="fa-solid fa-truck-fast me-2"></i> Purchases</a>
                <a href="/dashboard/finance" class="{{ request()->is('dashboard/finance') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Finance</a>
                <a href="/dashboard/cost-analytics" class="{{ request()->is('dashboard/cost-analytics') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie me-2"></i> Cost Analytics</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-0">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg px-4 py-3 sticky-top">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1 fw-bold text-dark">@yield('title', 'Dashboard')</span>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary me-3 rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-plus me-1"></i> Quick Action
                        </button>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name=Admin+User" class="rounded-circle" width="40" alt="User">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- Custom Scripts -->
@yield('scripts')
</body>
</html>
