# Pandoc PDF Build Script
# This script converts Page1.md, Page2.md, and Page3.md into a single PDF file.
# Note: Pandoc requires a PDF engine (like wkhtmltopdf, Prince, or LaTeX) to generate PDFs.

$outputFile = "Report.pdf"
$inputFiles = "Page1.md", "Page2.md", "Page3.md"

Write-Host "Converting $inputFiles to $outputFile..."

# Using --pdf-engine=wkhtmltopdf as a common choice, or letting pandoc decide.
# If you have LaTeX installed, it will work automatically. 
# If not, you might need to install wkhtmltopdf.
pandoc $inputFiles -o $outputFile

if ($LASTEXITCODE -eq 0) {
    Write-Host "Successfully created $outputFile" -ForegroundColor Green
} else {
    Write-Host "Error: Pandoc conversion failed." -ForegroundColor Red
    Write-Host "Tip: Pandoc requires a PDF engine (like MikTeX or wkhtmltopdf) to create PDFs." -ForegroundColor Yellow
}
