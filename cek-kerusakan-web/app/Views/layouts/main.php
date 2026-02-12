<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <title><?= isset($title) ? $title : 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            width: 220px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            padding-top: 1rem;
        }

        .content {
            margin-left: 240px;
            padding: 2rem;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .nav-link {
            color: #000;
            transition: background-color 0.3s, color 0.3s;
        }

        .nav-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
            border-radius: 0.375rem;
        }
    </style>
</head>

<body>
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-primary">📦 Container</h4>
        <hr>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="/dashboard">📊 Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'cari-kontainer' ? 'active' : '' ?>" href="/cari-kontainer">🔍 Cari Kontainer</a>
            </li>
        </ul>
        <hr class="mt-auto">
        <a href="/logout" class="btn btn-outline-danger">🚪 Log out</a>
    </div>

    <div class="content">
        <?= $this->renderSection('content') ?>
    </div>
</body>

</html>