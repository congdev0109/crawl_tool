<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrawlTool - Intelligent Website Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
                <div class="sidebar-inner p-3">
                    <a href="/" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                        <i class="bi bi-spider fs-3 me-2 text-primary"></i>
                        <span class="fs-4 fw-bold">CrawlTool</span>
                    </a>
                    <hr class="border-secondary">
                    
                    <!-- Stats Cards -->
                    <div class="stats-mini mb-3" id="sidebarStats">
                        <!-- Loaded via JS -->
                    </div>
                    
                    <hr class="border-secondary">
                    
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="/" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/projects" class="nav-link <?= ($currentPage ?? '') === 'projects' ? 'active' : '' ?>">
                                <i class="bi bi-folder me-2"></i> Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/logs" class="nav-link <?= ($currentPage ?? '') === 'logs' ? 'active' : '' ?>">
                                <i class="bi bi-terminal me-2"></i> Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/settings" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="border-secondary mt-4">
                    
                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <p class="text-muted small mb-2">Quick Actions</p>
                        <a href="/" class="btn btn-sm btn-outline-success w-100 mb-2">
                            <i class="bi bi-plus-lg me-1"></i> New Crawl
                        </a>
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="refreshStats()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Stats
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
                <?php if (isset($content)) echo $content; ?>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>

</html>