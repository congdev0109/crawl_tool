# Quick review-aware commit and push script
param(
    [string]$message = "chore: update code"
)

$ignoredPatterns = @(
    '^(output|vendor|node_modules|storage|cache|logs|tmp)[/\\]',
    '^src_sample[/\\]',
    '\.log$'
)

$status = git status --porcelain
if (-not $status) {
    Write-Host "No changes to commit."
    exit 0
}

$items = @()
foreach ($line in $status) {
    if ($line.Length -lt 4) { continue }
    $path = $line.Substring(3)
    if ($path -match ' -> ') {
        $path = $path.Split(' -> ')[1]
    }
    $items += $path
}

$relevant = $items | Where-Object {
    $p = $_
    -not ($ignoredPatterns | Where-Object { $p -match $_ })
} | Select-Object -Unique

if (-not $relevant -or $relevant.Count -eq 0) {
    Write-Host "Only ignored/generated files changed. Nothing to commit."
    exit 0
}

$deleteCount = ($status | Where-Object { $_.Substring(0,2) -match 'D' }).Count
if ($relevant.Count -gt 50 -or $deleteCount -gt 20) {
    $ans = Read-Host "Large change set detected (files=$($relevant.Count), deletes=$deleteCount). Continue? (y/N)"
    if ($ans -ne 'y' -and $ans -ne 'Y') { exit 1 }
}

# Stage only relevant paths
git add -A -- $relevant

Write-Host "`nChanges to commit:" -ForegroundColor Cyan
git status --short

# Commit
git commit -m $message

# Push
Write-Host "`nPushing to GitHub..." -ForegroundColor Green
git push

Write-Host "`nDone. Repo: https://github.com/congdev0109/crawl_tool" -ForegroundColor Green
