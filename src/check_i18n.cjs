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
    ...walk(path.join(__dirname, 'resources', 'js', 'Pages')),
    ...walk(path.join(__dirname, 'Modules')),
    ...walk(path.join(__dirname, 'resources', 'js', 'Components')),
    ...walk(path.join(__dirname, 'resources', 'js', 'Layouts'))
].filter(f => fs.existsSync(f));

let untranslated = [];
let proxies = 0;

vueFiles.forEach(f => {
    const content = fs.readFileSync(f, 'utf-8');
    
    // Ignore proxy files
    if (content.match(/import Page from.*export default Page/s)) {
        proxies++;
        return;
    }

    if (!content.includes('$t(') && !content.includes('t(')) {
        untranslated.push(f.replace(__dirname, ''));
    }
});
console.log('Total untranslated actual Vue files:', untranslated.length);
console.log('Total Proxy Vue files:', proxies);
console.log('Total Vue files:', vueFiles.length);
console.log(untranslated.slice(0, 50).join('\n'));

