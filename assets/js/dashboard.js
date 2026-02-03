// ===========================
// CrawlTool - Dashboard JS
// ===========================

console.log("Dashboard JS Loaded");

// Crawl Form Handler
const crawlForm = document.getElementById("crawlForm");
if (crawlForm) {
  crawlForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const resultSection = document.getElementById("resultSection");
    const output = document.getElementById("consoleOutput");
    const btn = document.getElementById("btnStart");
    const statusBox = document.getElementById("statusBox");
    const progressStatus = document.getElementById("progressStatus");

    // Show result section
    resultSection.classList.remove("d-none");
    output.textContent = "🚀 Starting crawl process...\n";
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Crawling...';
    
    if (progressStatus) {
      progressStatus.textContent = "Running";
      progressStatus.className = "badge bg-warning pulse";
    }

    if (statusBox) {
      statusBox.innerHTML = '<div class="d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2"></span> Crawling in progress...</div>';
    }

    // Use EventSource for real-time logs
    const params = new URLSearchParams(formData).toString();
    const eventSource = new EventSource(CrawlTool.api.streamCrawl(params));

    eventSource.onmessage = function (e) {
      try {
        const data = JSON.parse(e.data);
        
        if (data.message) {
          // Format log message
          let msg = data.message;
          if (msg.includes('ERROR') || msg.includes('❌')) {
            output.innerHTML += `<span class="log-error">${msg}</span>\n`;
          } else if (msg.includes('SUCCESS') || msg.includes('✅')) {
            output.innerHTML += `<span class="log-success">${msg}</span>\n`;
          } else if (msg.includes('WARNING')) {
            output.innerHTML += `<span class="log-warning">${msg}</span>\n`;
          } else {
            output.textContent += msg + "\n";
          }
          output.scrollTop = output.scrollHeight;

          // Detect if Specs are ready via signal
          if (data.message.startsWith("check_specs:")) {
            const projectName = data.message.split(":")[1].trim();
            if (window.renderSpecsEditor) {
              setTimeout(() => window.renderSpecsEditor(projectName), 500);
            }
          }
        }

        if (data.status === "done") {
          eventSource.close();
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-rocket-takeoff"></i> Start Analysis & Crawl';
          
          if (progressStatus) {
            progressStatus.textContent = "Completed";
            progressStatus.className = "badge bg-success";
          }
          
          if (statusBox) {
            statusBox.innerHTML = '<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i>Crawl completed successfully!</div>';
          }

          output.innerHTML += `<span class="log-success">✅ Crawl completed!</span>\n`;
          
          // Refresh sidebar stats
          if (typeof refreshStats === 'function') refreshStats();
        }

        if (data.status === "error") {
          eventSource.close();
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-rocket-takeoff"></i> Start Analysis & Crawl';
          
          if (progressStatus) {
            progressStatus.textContent = "Failed";
            progressStatus.className = "badge bg-danger";
          }
          
          if (statusBox) {
            statusBox.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>${data.message || 'Crawl failed'}</div>`;
          }
        }
      } catch (err) {
        output.textContent += e.data + "\n";
      }
    };

    eventSource.onerror = function () {
      eventSource.close();
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-rocket-takeoff"></i> Start Analysis & Crawl';
      output.innerHTML += `<span class="log-warning">[Connection Closed]</span>\n`;
      
      if (progressStatus) {
        progressStatus.textContent = "Disconnected";
        progressStatus.className = "badge bg-secondary";
      }
    };
  });
}

// Load recent projects on dashboard
async function loadRecentProjects() {
  const container = document.getElementById('recentProjectsList');
  if (!container) return;

  try {
    const response = await fetch('/api/stats');
    const data = await response.json();

    if (data.recent_projects && data.recent_projects.length > 0) {
      container.innerHTML = data.recent_projects.map(p => `
        <a href="/projects/${p.name}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
          <div>
            <i class="bi bi-folder me-2 text-primary"></i>
            <span>${p.name}</span>
          </div>
          <small class="text-muted">${p.size_formatted}</small>
        </a>
      `).join('');
    } else {
      container.innerHTML = '<li class="list-group-item bg-transparent text-muted text-center">No projects yet</li>';
    }
  } catch (err) {
    container.innerHTML = '<li class="list-group-item bg-transparent text-danger">Failed to load</li>';
  }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
  loadRecentProjects();
});
