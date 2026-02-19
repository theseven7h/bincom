<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Bincom Election Results'); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #1a5276;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar {
            background-color: #1a5276 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff !important;
        }

        .page-header {
            background: linear-gradient(135deg, #1a5276 0%, #2980b9 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .card-header {
            background-color: #1a5276;
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 0.9rem 1.25rem;
        }

        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .form-select:focus, .form-control:focus {
            border-color: #1a5276;
            box-shadow: 0 0 0 0.2rem rgba(26,82,118,0.25);
        }

        .btn-primary {
            background-color: #1a5276;
            border-color: #1a5276;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #154360;
            border-color: #154360;
        }

        .result-table th {
            background-color: #eaf0fb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }

        .result-table .total-row {
            background-color: #1a5276;
            color: white;
            font-weight: 700;
        }

        .badge-party {
            font-size: 0.85rem;
            padding: 0.4em 0.8em;
            background-color: #eaf0fb;
            color: #1a5276;
            border-radius: 6px;
            font-weight: 600;
        }

        .spinner-overlay {
            display: none;
            text-align: center;
            padding: 1.5rem;
            color: #1a5276;
        }

        .results-section {
            display: none;
        }

        footer {
            background-color: #1a5276;
            color: rgba(255,255,255,0.7);
            padding: 1.2rem 0;
            margin-top: 3rem;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-bar-chart-fill me-2"></i>Bincom Election Results
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('polling-unit.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('polling-unit.index')); ?>">
                        <i class="bi bi-search me-1"></i>Polling Unit Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('lga-results.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('lga-results.index')); ?>">
                        <i class="bi bi-table me-1"></i>LGA Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('new-result.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('new-result.index')); ?>">
                        <i class="bi bi-plus-circle me-1"></i>Add Results
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <h1 class="h3 mb-1"><?php echo $__env->yieldContent('page-title'); ?></h1>
        <p class="mb-0 opacity-75"><?php echo $__env->yieldContent('page-subtitle'); ?></p>
    </div>
</div>

<div class="container pb-5">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</div>

<footer>
    <div class="container">
        &copy; <?php echo e(date('Y')); ?> Bincom Election Results System &mdash; 2011 Nigerian General Elections Data
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/app.blade.php ENDPATH**/ ?>