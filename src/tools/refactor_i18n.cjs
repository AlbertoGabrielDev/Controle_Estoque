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

const baseDir = path.join(__dirname, '..');

const vueFiles = [
    ...walk(path.join(baseDir, 'resources', 'js', 'Pages')),
    ...walk(path.join(baseDir, 'Modules')),
    ...walk(path.join(baseDir, 'resources', 'js', 'Components')),
    ...walk(path.join(baseDir, 'resources', 'js', 'Layouts'))
].filter(f => fs.existsSync(f));

const replaceMap = {
    '>Cadastrar<': '>{{ $t(\'Create\') }}<',
    'Cadastrar\\s+<': '{{ $t(\'Create\') }} <',
    '>Editar<': '>{{ $t(\'Edit\') }}<',
    'Editar\\s+<': '{{ $t(\'Edit\') }} <',
    '>Salvar<': '>{{ $t(\'Save\') }}<',
    'Salvar\\s+<': '{{ $t(\'Save\') }} <',
    '>Voltar<': '>{{ $t(\'Back\') }}<',
    'Voltar\\s+<': '{{ $t(\'Back\') }} <',
    '>Remover<': '>{{ $t(\'Remove\') }}<',
    '>Excluir<': '>{{ $t(\'Delete\') }}<',
    '>Ações<': '>{{ $t(\'Actions\') }}<',
    "title: 'Ações'": "title: t('Actions')",
    "title: 'Acoes'": "title: t('Actions')",
    "title: 'Status'": "title: t('Status')",
    "title: 'Ativo'": "title: t('Active')",
    "title: 'Inativo'": "title: t('Inactive')",
    '>Ativo<': '>{{ $t(\'Active\') }}<',
    '>Inativo<': '>{{ $t(\'Inactive\') }}<',
    '>Cancelar<': '>{{ $t(\'Cancel\') }}<',
    'title: \'Código\'': 'title: t(\'Code\')',
    'title: \'Nome\'': 'title: t(\'Name\')',
    'title: \'Descrição\'': 'title: t(\'Description\')',
    'placeholder="Buscar"': ':placeholder="$t(\'Search\')"',
};

let modifiedCount = 0;

vueFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf-8');
    let modified = false;

    // Apply exact string replacements
    for (const [search, replace] of Object.entries(replaceMap)) {
        if (content.includes(search)) {
            // Using split.join to replace all occurrences
            content = content.split(search).join(replace);
            modified = true;
        }
    }

    // Advanced: replace hardcoded dtColumns action titles (e.g. { data: 'acoes', title: '...'})
    // But manual maps above handle most.

    // If we modified something, ensure the script has useI18n
    if (modified) {
        if (!content.includes('useI18n')) {
            // Check if there is a <script setup> block
            if (content.includes('<script setup>')) {
                // Determine if we need to add { t } = useI18n() (if script uses t('...'))
                const needsTConstant = content.includes("t('");
                
                let importStmt = `import { useI18n } from 'vue-i18n'\n`;
                let initStmt = needsTConstant ? `\nconst { t } = useI18n()\n` : '';

                content = content.replace(
                    '<script setup>', 
                    `<script setup>\n${importStmt}${initStmt}`
                );
            }
        }
        
        fs.writeFileSync(file, content, 'utf-8');
        modifiedCount++;
    }
});

console.log(`Refactored ${modifiedCount} Vue files successfully.`);
