const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, '../Modules');

const findFiles = (dir, ext, fileList = []) => {
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const filePath = path.join(dir, file);
    if (fs.statSync(filePath).isDirectory()) {
      findFiles(filePath, ext, fileList);
    } else if (filePath.endsWith(ext) && file.includes('Form.vue')) {
      fileList.push(filePath);
    }
  }
  return fileList;
};

const forms = findFiles(srcDir, '.vue');

let modifiedCount = 0;
const newTranslations = {};

for (const file of forms) {
  let content = fs.readFileSync(file, 'utf-8');
  let original = content;

  // 1. Labels
  content = content.replace(/<label(.*?)>([^<{]+)<\/label>/g, (match, attrs, text) => {
    const trimmed = text.trim();
    if (!trimmed || trimmed.includes('{{') || trimmed.includes('&')) return match;
    newTranslations[trimmed] = true;
    return `<label${attrs}>{{ $t('${trimmed}') }}</label>`;
  });

  // 2. Select option "Selecione" or "Todos os departamentos"
  content = content.replace(/<option value="">([^<{]+)<\/option>/g, (match, text) => {
    const trimmed = text.trim();
    if (!trimmed || trimmed.includes('{{')) return match;
    newTranslations[trimmed] = true;
    return `<option value="">{{ $t('${trimmed}') }}</option>`;
  });

  // 3. Buttons (like "Selecionar arquivo")
  content = content.replace(/<button([^>]*)>([^<{]+)<\/button>/g, (match, attrs, text) => {
    const trimmed = text.trim();
    if (!trimmed || trimmed.includes('{{') || trimmed.includes('$t(')) return match;
    newTranslations[trimmed] = true;
    return `<button${attrs}>{{ $t('${trimmed}') }}</button>`;
  });

  // 4. Static Placeholders
  content = content.replace(/([^\:])placeholder="([^"]+)"/g, (match, prefix, text) => {
    newTranslations[text] = true;
    return `${prefix}:placeholder="$t('${text}')"`;
  });

  // 5. Special case: :placeholder="editing ? '...' : ''"
  content = content.replace(/([^\:])placeholder="editing \? '([^']+)' : ''"/g, (match, prefix, text) => {
    newTranslations[text] = true;
    return `${prefix}placeholder="editing ? $t('${text}') : ''"`;
  });

  if (content !== original) {
    fs.writeFileSync(file, content, 'utf-8');
    modifiedCount++;
  }
}

console.log('Modified files:', modifiedCount);
console.log('Found strings to translate:', JSON.stringify(Object.keys(newTranslations), null, 2));
