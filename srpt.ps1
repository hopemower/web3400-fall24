#!/usr/bin/env pwsh
# Hope Mower
# Lab 7 - PowerShell Search and Report
# CS 3030 - Scripting Languages

# Step 1: Check if folder path is provided
if ($args.Count -ne 1) {
    Write-Host "Usage: srpt.ps1 FOLDER"
    exit 1
}

# Step 2: Initialize variables
$folder = $args[0]
$start_time = Get-Date
$todaysDate = & date
$hostname = & hostname
$directories = 0
$fileCnt = 0
$symLinks = 0
$oldFiles = 0
$largeFiles = 0
$graphicsFiles = 0
$temporaryFiles = 0
$executableFiles = 0
$totalSize = 0

# Step 3: Gather data using Get-ChildItem once
$items = Get-ChildItem -Recurse -Path $folder

# Step 4: Iterate over each item
foreach ($item in $items) {
    if ($item.PSIsContainer) {
        $directories++
    } elseif ($item.Attributes -match 'ReparsePoint') {
        $symLinks++
    } else {
        $fileCnt++
        if ($item.LastWriteTime -lt (Get-Date).AddDays(-365)) {
            $oldFiles++
        }
        if ($item.Length -gt 500000) {
            $largeFiles++
        }
        if ($item.Name -match '\.jpg$|\.gif$|\.bmp$') {
            $graphicsFiles++
        }
        if ($item.Name -match '\.o$') {
            $temporaryFiles++
        }
        if ($item.Name -match '\.bat$|\.ps1$|\.exe$') {
            $executableFiles++
        }
        $totalSize += $item.Length
    }
}

# Step 5: Print the report header
Write-Host "SearchReport $hostname $folder $todaysDate"

# Step 6: Print the report
$executionTime = (New-TimeSpan -Start $start_time -End (Get-Date)).TotalSeconds
Write-Host ("Execution time {0:N0}" -f $executionTime)
Write-Host ("Directories {0:N0}" -f $directories)
Write-Host ("Files {0:N0}" -f $fileCnt)
Write-Host ("Sym links {0:N0}" -f $symLinks)
Write-Host ("Old files {0:N0}" -f $oldFiles)
Write-Host ("Large files {0:N0}" -f $largeFiles)
Write-Host ("Graphics files {0:N0}" -f $graphicsFiles)
Write-Host ("Temporary files {0:N0}" -f $temporaryFiles)
Write-Host ("Executable files {0:N0}" -f $executableFiles)
Write-Host ("TotalFileSize {0:N0}" -f $totalSize)

# Step 7: Exit with success
exit 0
