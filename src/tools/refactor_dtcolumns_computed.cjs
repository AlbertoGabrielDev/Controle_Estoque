const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, '../Modules');

const findFiles = (dir, ext, fileList = []) => {
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const filePath = path.join(dir, file);
    if (fs.statSync(filePath).isDirectory()) {
      findFiles(filePath, ext, fileList);
    } else if (filePath.endsWith(ext) && file.includes('Index.vue')) {
      fileList.push(filePath);
    }
  }
  return fileList;
};

const forms = findFiles(srcDir, '.vue');

for (const file of forms) {
  let content = fs.readFileSync(file, 'utf-8');
  let original = content;

  if (content.includes('const dtColumns = [') && !content.includes('computed(() => [')) {
    // 1. Inject import { computed } if needed
    if (!content.includes("from 'vue'") || !content.includes("computed")) {
       if (content.includes("from 'vue'")) {
         content = content.replace(/import\s+{([^}]+)}\s+from\s+'vue'/, (m, p1) => {
           if (!p1.includes('computed')) return `import { computed, ${p1.trim()} } from 'vue'`;
           return m;
         });
       } else {
         content = content.replace("<script setup>", "<script setup>\nimport { computed } from 'vue'");
       }
    } else {
       if(!content.includes('computed,') && !content.includes('{ computed }') && !content.includes('computed }')) {
            content = content.replace(/import\s+{([^}]+)}\s+from\s+'vue'/, (m, p1) => `import { computed, ${p1.trim()} } from 'vue'`);
       }
    }

    // 2. Bracket balancing
    const startIdx = content.indexOf('const dtColumns = [');
    if (startIdx !== -1) {
       const arrayStart = startIdx + 'const dtColumns = '.length;
       let openBrackets = 0;
       let endIdx = -1;
       
       for (let i = arrayStart; i < content.length; i++) {
           if (content[i] === '[') openBrackets++;
           else if (content[i] === ']') {
               openBrackets--;
               if (openBrackets === 0) {
                   endIdx = i;
                   break;
               }
           }
       }
       
       if (endIdx !== -1) {
           const before = content.substring(0, startIdx);
           const inside = content.substring(arrayStart, endIdx + 1);
           const after = content.substring(endIdx + 1);
           
           content = before + 'const dtColumns = computed(() => ' + inside + ')' + after;
       }
    }
  }

  if (content !== original) {
    fs.writeFileSync(file, content, 'utf-8');
    console.log('Modified:', file);
  }
}
