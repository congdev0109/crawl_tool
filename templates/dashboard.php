<?php
// Dashboard Template
$currentPage = 'dashboard';
?>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-overview-card green fade-in">
            <i class="bi bi-folder stat-icon"></i>
            <div class="stat-number" id="statProjects">-</div>
            <div class="text-muted">Total Projects</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-overview-card blue fade-in">
            <i class="bi bi-hdd stat-icon"></i>
            <div class="stat-number" id="statSize">-</div>
            <div class="text-muted">Storage Used</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-overview-card purple fade-in">
            <i class="bi bi-layers stat-icon"></i>
            <div class="stat-number" id="statSpecs">-</div>
            <div class="text-muted">Total Specs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-overview-card orange fade-in">
            <i class="bi bi-activity stat-icon"></i>
            <div class="stat-number">Active</div>
            <div class="text-muted">System Status</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
    <h1 class="h2"><i class="bi bi-plus-circle me-2"></i>New Crawl Mission</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4 fade-in">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Configuration
            </div>
            <div class="card-body">
                <form id="crawlForm">
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="projectName" name="project_name" placeholder="e.g., my-shop-clone" required>
                        <div class="form-text text-secondary">Output will be saved to <code>output/PROJECT_NAME/</code></div>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Target URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Crawl Type</label>
                            <select class="form-select" name="type">
                                <option value="full">Full Website (Assets + Data)</option>
                                <option value="assets">Assets Only (CSS, JS, Fonts)</option>
                                <option value="data">Data Only (SQL Generation)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Depth Limit</label>
                            <select class="form-select" name="depth">
                                <option value="1">Level 1 (Current Page)</option>
                                <option value="2" selected>Level 2 (Follow links once)</option>
                                <option value="3">Level 3 (Deep crawl)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="btnStart">
                        <i class="bi bi-rocket-takeoff me-2"></i> Start Analysis & Crawl
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4 fade-in">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Status
            </div>
            <div class="card-body" id="statusBox">
                <p class="text-secondary text-center mb-0">
                    <i class="bi bi-hourglass me-2"></i>Waiting for input...
                </p>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Projects</span>
                <a href="/projects" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="recentProjectsList">
                    <div class="list-group-item bg-transparent text-center text-muted py-3">
                        <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Section -->
<div class="row d-none" id="resultSection">
    <!-- Log Output -->
    <div class="col-12 mb-4">
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-terminal me-2"></i>Console Output</span>
                <span class="badge bg-warning" id="progressStatus">Running...</span>
            </div>
            <div class="card-body p-0">
                <pre class="console-output m-0" id="consoleOutput" style="height: 250px;"></pre>
            </div>
        </div>
    </div>

    <!-- Specs Editor -->
    <div class="col-12 d-none" id="specsEditorContainer">
        <div class="card border-primary fade-in">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Type Configuration Editor</h5>
                <button class="btn btn-sm btn-light text-primary" id="btnRefreshSpecs">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <p class="text-secondary small">Review and modify the detected configurations before generating files.</p>
                <form id="specsForm">
                    <div id="specsContainer" class="accordion mb-3">
                        <!-- Dynamic Content Will Be Loaded Here -->
                    </div>
                </form>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success fw-bold" id="btnGenConfig">
                    <i class="bi bi-check-circle-fill me-1"></i>Generate Config Files
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template for a Spec Item (Hidden) -->
<template id="specItemTemplate">
    <div class="accordion-item" data-nametype="">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="">
                <span class="badge bg-secondary me-2 base-badge">BASE</span>
                <span class="fw-bold nametype-label">NAMETYPE</span>
                <span class="ms-auto text-muted small thumb-info"></span>
            </button>
        </h2>
        <div id="" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="row g-3">
                    <!-- Basic Info -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Image Size (WxH)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control spec-w" placeholder="W">
                            <span class="input-group-text">x</span>
                            <input type="number" class="form-control spec-h" placeholder="H">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Has Gallery</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input spec-gallery" type="checkbox">
                            <label class="form-check-label small">Enable multiple images</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Has Detail Page</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input spec-detail" type="checkbox" disabled>
                            <label class="form-check-label small">Detected automatically</label>
                        </div>
                    </div>

                    <!-- Features Checkboxes -->
                    <div class="col-12">
                        <label class="form-label small fw-bold mb-2">Enabled Features</label>
                        <div class="d-flex flex-wrap gap-3 feature-checks">
                            <!-- Checkboxes injected via JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const specsContainer = document.getElementById('specsContainer');
        const specTemplate = document.getElementById('specItemTemplate');
        const btnRefreshSpecs = document.getElementById('btnRefreshSpecs');
        const btnGenConfig = document.getElementById('btnGenConfig');

        // Global state to store specs
        let currentSpecs = {};
        let lastProjectName = '';

        // Load dashboard stats
        loadDashboardStats();

        async function loadDashboardStats() {
            try {
                const response = await fetch('/api/stats');
                const data = await response.json();
                
                document.getElementById('statProjects').textContent = data.total_projects || 0;
                document.getElementById('statSize').textContent = data.total_size_formatted || '0 B';
                document.getElementById('statSpecs').textContent = data.total_specs || 0;
            } catch (err) {
                console.warn('Failed to load dashboard stats:', err);
            }
        }

        // Function to load and render specs
        window.renderSpecsEditor = function(projectName) {
            projectName = projectName || document.getElementById('projectName').value;
            if (!projectName) {
                alert("Please enter a project name or run a crawl first.");
                return;
            }

            lastProjectName = projectName;
            console.log("Loading specs for project:", projectName);
            const specsUrl = CrawlTool.api.getSpecs(projectName);

            const btnRefresh = document.getElementById('btnRefreshSpecs');
            const originalIcon = btnRefresh.innerHTML;
            btnRefresh.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btnRefresh.disabled = true;

            fetch(specsUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        CrawlTool.showToast("Error: " + data.error, 'danger');
                        console.error("Specs Error:", data.error);
                        return;
                    }

                    currentSpecs = data;
                    specsContainer.innerHTML = '';

                    const keys = Object.keys(data);
                    if (keys.length === 0) {
                        specsContainer.innerHTML = '<div class="alert alert-warning">No nametypes detected for this project yet. Try a deeper crawl.</div>';
                    }

                    document.getElementById('specsEditorContainer').classList.remove('d-none');
                    document.getElementById('specsEditorContainer').scrollIntoView({
                        behavior: 'smooth'
                    });

                    keys.forEach((nametype, index) => {
                        const spec = data[nametype];
                        const clone = specTemplate.content.cloneNode(true);

                        const collapseId = `collapseConfig${index}`;
                        const headerBtn = clone.querySelector('.accordion-button');
                        const collapseDiv = clone.querySelector('.accordion-collapse');
                        headerBtn.setAttribute('data-bs-target', `#${collapseId}`);
                        collapseDiv.id = collapseId;

                        // Base badge color
                        const baseBadge = clone.querySelector('.base-badge');
                        baseBadge.textContent = spec.base.toUpperCase();
                        baseBadge.className = `badge me-2 bg-${spec.base}`;

                        clone.querySelector('.nametype-label').textContent = nametype;
                        clone.querySelector('.thumb-info').textContent = spec.thumb_size || '';

                        // Width/Height
                        const inputW = clone.querySelector('.spec-w');
                        const inputH = clone.querySelector('.spec-h');
                        inputW.value = spec.width || 0;
                        inputH.value = spec.height || 0;

                        inputW.addEventListener('change', (e) => currentSpecs[nametype].width = parseInt(e.target.value));
                        inputH.addEventListener('change', (e) => currentSpecs[nametype].height = parseInt(e.target.value));

                        // Gallery & Detail
                        const galleryCheck = clone.querySelector('.spec-gallery');
                        const detailCheck = clone.querySelector('.spec-detail');
                        galleryCheck.checked = spec.has_gallery || false;
                        detailCheck.checked = spec.has_detail || false;
                        
                        galleryCheck.addEventListener('change', (e) => currentSpecs[nametype].has_gallery = e.target.checked);

                        // Features
                        const featureContainer = clone.querySelector('.feature-checks');
                        if (spec.features) {
                            Object.keys(spec.features).forEach(feature => {
                                const isChecked = spec.features[feature];
                                const checkId = `check_${nametype}_${feature}`;
                                const div = document.createElement('div');
                                div.className = 'form-check form-switch';
                                div.style.minWidth = '120px';
                                div.innerHTML = `
                                    <input class="form-check-input" type="checkbox" id="${checkId}" ${isChecked ? 'checked' : ''}>
                                    <label class="form-check-label small" for="${checkId}">${feature}</label>
                                `;
                                const input = div.querySelector('input');
                                input.addEventListener('change', (e) => {
                                    currentSpecs[nametype].features[feature] = e.target.checked;
                                });
                                featureContainer.appendChild(div);
                            });
                        }
                        specsContainer.appendChild(clone);
                    });
                })
                .catch(err => {
                    CrawlTool.showToast("Failed to load specs: " + err, 'danger');
                    console.error("Fetch failed:", err);
                })
                .finally(() => {
                    btnRefresh.innerHTML = originalIcon;
                    btnRefresh.disabled = false;
                });
        };

        // Refresh Button
        if (btnRefreshSpecs) {
            btnRefreshSpecs.addEventListener('click', () => {
                window.renderSpecsEditor(lastProjectName);
            });
        }

        // Handle Generation
        if (btnGenConfig) {
            btnGenConfig.addEventListener('click', () => {
                if (!currentSpecs || Object.keys(currentSpecs).length === 0) {
                    CrawlTool.showToast('No specs to generate', 'warning');
                    return;
                }

                const projectName = document.getElementById('projectName').value;
                const btn = btnGenConfig;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
                btn.disabled = true;

                fetch(CrawlTool.api.saveSpecs, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            project: projectName,
                            specs: currentSpecs
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            CrawlTool.showToast("Configs Generated Successfully!", 'success');
                        } else {
                            CrawlTool.showToast("Error: " + res.message, 'danger');
                        }
                    })
                    .catch(err => CrawlTool.showToast("Request Failed: " + err, 'danger'))
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            });
        }
    });
</script>

<?php include 'layout_footer.php'; ?>