// ===========================
// CrawlTool - Main Application JS
// ===========================

const CrawlTool = {
    // API Endpoints
    api: {
        stats: '/api/stats',
        projects: '/api/projects',
        project: (name) => `/api/projects/${name}`,
        projectLog: (name) => `/api/projects/${name}/log`,
        deleteProject: (name) => `/api/projects/${name}`,
        crawl: '/api/crawl',
        getSpecs: (project) => `index.php?action=get_specs&project=${project}`,
        saveSpecs: 'index.php?action=save_check_specs',
        streamCrawl: (params) => `index.php?action=stream_crawl&${params}`,
    },

    // Initialize
    init() {
        this.loadSidebarStats();
        this.setupEventListeners();
        console.log('CrawlTool initialized');
    },

    // Load sidebar stats
    async loadSidebarStats() {
        try {
            const response = await fetch(this.api.stats);
            const data = await response.json();
            
            if (data.error) {
                console.warn('Stats error:', data.error);
                return;
            }

            const container = document.getElementById('sidebarStats');
            if (container) {
                container.innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${data.total_projects || 0}</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${data.total_size_formatted || '0 B'}</div>
                        <div class="stat-label">Total Size</div>
                    </div>
                `;
            }
        } catch (err) {
            console.warn('Failed to load stats:', err);
        }
    },

    // Setup global event listeners
    setupEventListeners() {
        // Handle all delete buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-project')) {
                const btn = e.target.closest('.btn-delete-project');
                const projectName = btn.dataset.project;
                if (projectName) {
                    this.confirmDeleteProject(projectName);
                }
            }
        });
    },

    // Confirm and delete project
    async confirmDeleteProject(name) {
        if (!confirm(`Are you sure you want to delete project "${name}"? This cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(this.api.deleteProject(name), {
                method: 'DELETE'
            });
            const data = await response.json();

            if (data.status === 'success') {
                this.showToast('Project deleted successfully', 'success');
                // Reload page or remove element
                if (typeof loadProjects === 'function') {
                    loadProjects();
                } else {
                    location.reload();
                }
            } else {
                this.showToast('Failed to delete: ' + data.message, 'danger');
            }
        } catch (err) {
            this.showToast('Error: ' + err.message, 'danger');
        }
    },

    // Show toast notification
    showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const id = 'toast_' + Date.now();
        const bgClass = {
            success: 'bg-success',
            danger: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        }[type] || 'bg-secondary';

        const iconClass = {
            success: 'bi-check-circle-fill',
            danger: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        }[type] || 'bi-bell';

        const html = `
            <div id="${id}" class="toast show" role="alert">
                <div class="toast-header ${bgClass} text-white">
                    <i class="bi ${iconClass} me-2"></i>
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        // Auto remove after 5s
        setTimeout(() => {
            const toast = document.getElementById(id);
            if (toast) toast.remove();
        }, 5000);
    },

    // Format bytes
    formatBytes(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    },

    // Format date
    formatDate(timestamp) {
        return new Date(timestamp * 1000).toLocaleString();
    }
};

// Global refresh function
function refreshStats() {
    CrawlTool.loadSidebarStats();
    CrawlTool.showToast('Stats refreshed', 'info');
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    CrawlTool.init();
});
