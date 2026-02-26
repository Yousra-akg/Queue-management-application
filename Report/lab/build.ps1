# Pandoc Build Script
# This script converts Page1.md, Page2.md, and Page3.md into a single DOCX file.

$outputFile = "Report.docx"
$inputFiles = "Page1.md", "Page2.md", "Page3.md"

Write-Host "Converting $inputFiles to $outputFile..."

pandoc $inputFiles -o $outputFile

if ($LASTEXITCODE -eq 0) {
    Write-Host "Successfully created $outputFile" -ForegroundColor Green
} else {
    Write-Host "Error: Pandoc conversion failed. Make sure Pandoc is installed." -ForegroundColor Red
}
