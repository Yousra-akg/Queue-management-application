# Pandoc Build Script for QCM Project (DOCX Only)
# This script converts modular Markdown files into a professional DOCX report.

$outputDocx = "QCM_Report.docx"

$inputFiles = "00_title_page.md",
"01_toc.md",
"02_preliminary.md", 
"03_contexte.md", 
"04_methode.md", 
"05_analyse.md", 
"06_technical.md", 
"07_design.md", 
"08_realisation.md", 
"09_conclusion.md"

Write-Host "--- Starting Pandoc Build for QCM (DOCX) ---" -ForegroundColor Cyan

Write-Host "Generating DOCX: $outputDocx..."
pandoc $inputFiles `
    --number-sections `
    -o $outputDocx

if ($LASTEXITCODE -eq 0) {
    Write-Host "DOCX SUCCESS: $outputDocx created." -ForegroundColor Green
}
else {
    Write-Host "BUILD FAILED: Please check for errors above." -ForegroundColor Red
}

Write-Host "--- Build Complete ---" -ForegroundColor Cyan
