const fs = require('fs');
const path = require('path');

// Read the PRO icons JS file
const jsContent = fs.readFileSync('./package/dist/index.esm.js', 'utf8');

// Regular expression to match icon definitions
const iconRegex = /(\w+)=\{name:"(\w+)",svg:'([^']+)',viewBox:"([^"]+)"/g;

let match;
let count = 0;
const outputDir = './lineicons-pro-svg';

// Create output directory
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

console.log('🔍 Extracting LineIcons PRO SVG files...\n');

// Extract all icons
while ((match = iconRegex.exec(jsContent)) !== null) {
    const [, varName, iconName, svgContent, viewBox] = match;
    
    // Convert PascalCase to kebab-case
    const fileName = iconName
        .replace(/([a-z])([A-Z])/g, '$1-$2')
        .replace(/([A-Z])([A-Z][a-z])/g, '$1-$2')
        .toLowerCase();
    
    // Create full SVG file
    const svgFile = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${viewBox}" fill="none">
${svgContent.replace(/\{color\}/g, 'currentColor').replace(/<\/?>/g, '')}
</svg>`;
    
    // Write to file
    fs.writeFileSync(path.join(outputDir, `${fileName}.svg`), svgFile);
    count++;
    
    // Progress indicator
    if (count % 500 === 0) {
        console.log(`   Extracted ${count} icons...`);
    }
}

console.log(`\n✅ Successfully extracted ${count} SVG icons to ${outputDir}/`);
console.log('\nNext steps:');
console.log('1. Copy icons to Laravel: cp -r lineicons-pro-svg public/vendor/lineicons');
console.log('2. Clear caches: php artisan config:clear && php artisan view:clear');
