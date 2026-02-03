<?php
// Projects List Template
$currentPage = 'projects';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
    <h1 class="h2"><i class="bi bi-folder me-2"></i>All Projects</h1>
    <div class="btn-toolbar">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Crawl
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchProjects" placeholder="Search projects...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="sortProjects">
                    <option value="modified_desc">Recently Modified</option>
                    <option value="modified_asc">Oldest First</option>
                    <option value="name_asc">Name A-Z</option>
                    <option value="name_desc">Name Z-A</option>
                    <option value="size_desc">Largest First</option>
                </select>
            </div>
            <div class="col-md-5 text-end">
                <span class="text-muted small" id="projectCount">Loading...</span>
            </div>
        </div>
    </div>
</div>

<!-- Projects Grid -->
<div class="row g-4" id="projectsGrid">
    <!-- Loading skeleton -->
    <div class="col-md-6 col-lg-4">
        <div class="project-card">
            <div class="skeleton" style="height: 24px; width: 60%; margin-bottom: 10px;"></div>
            <div class="skeleton" style="height: 14px; width: 40%; margin-bottom: 15px;"></div>
            <div class="d-flex gap-2">
                <div class="skeleton" style="height: 20px; width: 50px;"></div>
                <div class="skeleton" style="height: 20px; width: 50px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div class="empty-state d-none" id="emptyState">
    <i class="bi bi-folder-x d-block"></i>
    <h4>No Projects Found</h4>
    <p>Start by creating your first crawl mission.</p>
    <a href="/" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Crawl
    </a>
</div>

<!-- Project Detail Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="projectModalTitle">Project Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="projectModalBody">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-danger btn-delete-project" data-project="">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let allProjects = [];

document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
    
    // Search
    document.getElementById('searchProjects').addEventListener('input', filterProjects);
    document.getElementById('sortProjects').addEventListener('change', filterProjects);
});

async function loadProjects() {
    try {
        const response = await fetch('/api/projects');
        allProjects = await response.json();
        
        if (allProjects.error) {
            console.error(allProjects.error);
            return;
        }
        
        renderProjects(allProjects);
    } catch (err) {
        console.error('Failed to load projects:', err);
        document.getElementById('projectsGrid').innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">Failed to load projects: ${err.message}</div>
            </div>
        `;
    }
}

function filterProjects() {
    const search = document.getElementById('searchProjects').value.toLowerCase();
    const sort = document.getElementById('sortProjects').value;
    
    let filtered = allProjects.filter(p => 
        p.name.toLowerCase().includes(search)
    );
    
    // Sort
    filtered.sort((a, b) => {
        switch (sort) {
            case 'modified_desc': return b.modified_at - a.modified_at;
            case 'modified_asc': return a.modified_at - b.modified_at;
            case 'name_asc': return a.name.localeCompare(b.name);
            case 'name_desc': return b.name.localeCompare(a.name);
            case 'size_desc': return b.size - a.size;
            default: return 0;
        }
    });
    
    renderProjects(filtered);
}

function renderProjects(projects) {
    const grid = document.getElementById('projectsGrid');
    const emptyState = document.getElementById('emptyState');
    const countEl = document.getElementById('projectCount');
    
    countEl.textContent = `${projects.length} project(s)`;
    
    if (projects.length === 0) {
        grid.classList.add('d-none');
        emptyState.classList.remove('d-none');
        return;
    }
    
    grid.classList.remove('d-none');
    emptyState.classList.add('d-none');
    
    grid.innerHTML = projects.map(p => `
        <div class="col-md-6 col-lg-4">
            <div class="project-card fade-in" onclick="showProjectDetail('${p.name}')">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="project-name">
                        <i class="bi bi-folder-fill text-primary me-2"></i>${p.name}
                    </div>
                    <span class="badge bg-secondary">${p.size_formatted}</span>
                </div>
                
                <div class="project-meta mb-3">
                    <i class="bi bi-clock me-1"></i>${p.modified_at_formatted}
                </div>
                
                <div class="project-badges d-flex flex-wrap gap-1">
                    ${p.specs_count > 0 ? `<span class="badge bg-success">${p.specs_count} specs</span>` : ''}
                    ${p.has_config ? '<span class="badge bg-info">Config</span>' : ''}
                    ${p.has_assets ? '<span class="badge bg-warning text-dark">Assets</span>' : ''}
                    ${p.has_sql ? '<span class="badge bg-purple">SQL</span>' : ''}
                </div>
            </div>
        </div>
    `).join('');
}

async function showProjectDetail(name) {
    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
    const modalTitle = document.getElementById('projectModalTitle');
    const modalBody = document.getElementById('projectModalBody');
    const deleteBtn = document.querySelector('#projectModal .btn-delete-project');
    
    modalTitle.textContent = name;
    deleteBtn.dataset.project = name;
    
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <span class="spinner-border"></span>
            <p class="mt-2 text-muted">Loading project details...</p>
        </div>
    `;
    
    modal.show();
    
    try {
        const response = await fetch(`/api/projects/${name}`);
        const project = await response.json();
        
        if (project.error) {
            modalBody.innerHTML = `<div class="alert alert-danger">${project.error}</div>`;
            return;
        }
        
        modalBody.innerHTML = `
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Project Info</h6>
                    <table class="table table-sm">
                        <tr><td class="text-muted">Path</td><td><code>${project.path}</code></td></tr>
                        <tr><td class="text-muted">Size</td><td>${project.size_formatted}</td></tr>
                        <tr><td class="text-muted">Modified</td><td>${project.modified_at_formatted}</td></tr>
                        <tr><td class="text-muted">Specs</td><td>${project.specs_count} types detected</td></tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Generated Files</h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${project.has_config ? '<span class="badge bg-info"><i class="bi bi-file-code me-1"></i>Config</span>' : ''}
                        ${project.has_assets ? '<span class="badge bg-warning text-dark"><i class="bi bi-images me-1"></i>Assets</span>' : ''}
                        ${project.has_sql ? '<span class="badge bg-purple"><i class="bi bi-database me-1"></i>SQL</span>' : ''}
                        ${project.log_exists ? '<span class="badge bg-secondary"><i class="bi bi-file-text me-1"></i>Logs</span>' : ''}
                    </div>
                </div>
                
                ${Object.keys(project.specs || {}).length > 0 ? `
                <div class="col-12">
                    <h6 class="text-muted mb-3">Detected Types</h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${Object.entries(project.specs).map(([name, spec]) => `
                            <span class="badge bg-${spec.base}">${name} (${spec.base})</span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
                
                <div class="col-12">
                    <h6 class="text-muted mb-3">Actions</h6>
                    <div class="d-flex gap-2">
                        <a href="/?project=${project.name}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>Re-crawl
                        </a>
                        <button class="btn btn-sm btn-outline-info" onclick="viewLog('${project.name}')">
                            <i class="bi bi-terminal me-1"></i>View Log
                        </button>
                        <a href="/output/${project.name}/" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-folder2-open me-1"></i>Browse Files
                        </a>
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        modalBody.innerHTML = `<div class="alert alert-danger">Failed to load: ${err.message}</div>`;
    }
}

async function viewLog(name) {
    const modalBody = document.getElementById('projectModalBody');
    
    try {
        const response = await fetch(`/api/projects/${name}/log`);
        const data = await response.json();
        
        modalBody.innerHTML = `
            <div class="mb-3">
                <button class="btn btn-sm btn-outline-secondary" onclick="showProjectDetail('${name}')">
                    <i class="bi bi-arrow-left me-1"></i>Back to Details
                </button>
            </div>
            <pre class="console-output" style="height: 400px; overflow: auto;">${data.log || 'No logs available'}</pre>
        `;
    } catch (err) {
        modalBody.innerHTML = `<div class="alert alert-danger">Failed to load log: ${err.message}</div>`;
    }
}
</script>
