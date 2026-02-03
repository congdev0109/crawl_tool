<?php

namespace CrawlTool\Web;

class ProjectManager
{
    private string $outputDir;

    public function __construct(?string $rootDir = null)
    {
        $this->outputDir = ($rootDir ?? dirname(__DIR__, 2)) . '/output';
    }

    /**
     * Get all projects with their stats
     */
    public function listProjects(): array
    {
        $projects = [];

        if (!is_dir($this->outputDir)) {
            return $projects;
        }

        $dirs = array_filter(glob($this->outputDir . '/*'), 'is_dir');

        foreach ($dirs as $dir) {
            $name = basename($dir);
            $projects[] = $this->getProjectInfo($name);
        }

        // Sort by modified date desc
        usort($projects, fn($a, $b) => $b['modified_at'] <=> $a['modified_at']);

        return $projects;
    }

    /**
     * Get detailed info for a single project
     */
    public function getProjectInfo(string $name): array
    {
        $path = $this->outputDir . '/' . $name;

        if (!is_dir($path)) {
            return ['error' => 'Project not found'];
        }

        $specsFile = $path . '/specs.json';
        $logFile = $path . '/crawl.log';

        $specs = file_exists($specsFile) 
            ? json_decode(file_get_contents($specsFile), true) 
            : [];

        $hasConfig = is_dir($path . '/config');
        $hasAssets = is_dir($path . '/assets');
        $hasSql = is_dir($path . '/sql');

        return [
            'name' => $name,
            'path' => $path,
            'specs_count' => count($specs),
            'specs' => $specs,
            'has_config' => $hasConfig,
            'has_assets' => $hasAssets,
            'has_sql' => $hasSql,
            'size' => $this->getDirectorySize($path),
            'size_formatted' => $this->formatBytes($this->getDirectorySize($path)),
            'modified_at' => filemtime($path),
            'modified_at_formatted' => date('Y-m-d H:i:s', filemtime($path)),
            'log_exists' => file_exists($logFile),
            'files' => $this->getProjectFiles($path),
        ];
    }

    /**
     * Get project files structure
     */
    public function getProjectFiles(string $path): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $files[] = [
                'path' => $relativePath,
                'is_dir' => $item->isDir(),
                'size' => $item->isDir() ? 0 : $item->getSize(),
                'ext' => $item->isDir() ? '' : $item->getExtension(),
            ];
        }

        return $files;
    }

    /**
     * Delete a project
     */
    public function deleteProject(string $name): bool
    {
        $path = $this->outputDir . '/' . $name;

        if (!is_dir($path)) {
            return false;
        }

        return $this->deleteDirectory($path);
    }

    /**
     * Get project log content
     */
    public function getProjectLog(string $name, int $lines = 100): string
    {
        $logFile = $this->outputDir . '/' . $name . '/crawl.log';

        if (!file_exists($logFile)) {
            return '';
        }

        $content = file_get_contents($logFile);
        $allLines = explode("\n", $content);

        if (count($allLines) > $lines) {
            return implode("\n", array_slice($allLines, -$lines));
        }

        return $content;
    }

    /**
     * Get dashboard stats
     */
    public function getStats(): array
    {
        $projects = $this->listProjects();
        $totalSize = 0;
        $totalSpecs = 0;

        foreach ($projects as $p) {
            $totalSize += $p['size'];
            $totalSpecs += $p['specs_count'];
        }

        return [
            'total_projects' => count($projects),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'total_specs' => $totalSpecs,
            'recent_projects' => array_slice($projects, 0, 5),
        ];
    }

    private function getDirectorySize(string $path): int
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }
}
