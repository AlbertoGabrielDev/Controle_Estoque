const fs = require('fs');
const path = require('path');

function walk(dir) {
    if (!fs.existsSync(dir)) return [];
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else if (file.endsWith('.vue')) {
            results.push(file);
        }
    });
    return results;
}

const vueFiles = [
    ...walk(path.join(__dirname, 'Modules')),
    ...walk(path.join(__dirname, 'resources', 'js', 'Pages'))
];

let fixed = 0;

vueFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf-8');
    
    // Pattern we are looking for: '<span ...>{{ $t('Active') }}</span>'
    // Note that the single quotes around Active broke the JS string.
    
    let originalLength = content.length;
    
    content = content.replace(/'<span class="text-green-700">\{\{\s*\$t\('Active'\)\s*\}\}<\/span>'/g, '`<span class="text-green-700">${t(\'Active\')}</span>`');
    content = content.replace(/'<span class="text-gray-500">\{\{\s*\$t\('Inactive'\)\s*\}\}<\/span>'/g, '`<span class="text-gray-500">${t(\'Inactive\')}</span>`');

    if (content.length !== originalLength) {
        fs.writeFileSync(file, content, 'utf-8');
        console.log('Fixed syntax error in: ' + file);
        fixed++;
    }
});

console.log(`\nFixed ${fixed} files.`);
