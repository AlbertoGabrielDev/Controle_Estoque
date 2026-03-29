const fs = require('fs');

let content = fs.readFileSync('resources/js/Pages/Dashboard/SalesDashboard.vue', 'utf8');

if (!content.includes('useI18n')) {
    content = content.replace("import { computed, ref, watch } from 'vue'", "import { computed, ref, watch } from 'vue'\nimport { useI18n } from 'vue-i18n'");
    content = content.replace("const props = defineProps", "const { t } = useI18n()\n\nconst props = defineProps");
}

content = content.replace("'Faturamento (R$)'", "t('Revenue (R$)')");
content = content.replace("'Quantidade'", "t('Quantity')");
content = content.replace("'Faturamento por Unidade (R$)'", "t('Revenue by Unit (R$)')");
content = content.replace("`Faturamento ${props.monthly.year ?? ''} (R$)`", "`\\${t('Revenue')} ${props.monthly.year ?? ''} (R$)`");

content = content.replace('title="Dashboard de Vendas"', ':title="$t(\'Sales Dashboard\')"');
content = content.replace('>Usuario:<', '>{{ $t(\'User\') }}:<');
content = content.replace('<option :value="\'\'">Todos</option>', '<option :value="\'\'">{{ $t(\'All\') }}</option>');
content = content.replace('>De<', '>{{ $t(\'From\') }}<');
content = content.replace('>Até<', '>{{ $t(\'To\') }}<');
content = content.replace('Aplicar\n', '{{ $t(\'Apply\') }}\n');
content = content.replace('Limpar\n', '{{ $t(\'Clear\') }}\n');
content = content.replace('>Atalhos:<', '>{{ $t(\'Shortcuts\') }}:<');
content = content.replace('Últimos 7\n        dias', '{{ $t(\'Last 7 days\') }}');
content = content.replace('Últimos 30\n        dias', '{{ $t(\'Last 30 days\') }}');
content = content.replace('Este\n        mês', '{{ $t(\'This month\') }}');
content = content.replace('>Hoje<', '>{{ $t(\'Today\') }}<');
content = content.replace('>Dashboard de Vendas<', '>{{ $t(\'Sales Dashboard\') }}<');
content = content.replace('Unidade:', '{{ $t(\'Unit\') }}:');
content = content.replace('>Vendas (intervalo)<', '>{{ $t(\'Sales (interval)\') }}<');
content = content.replace('>Contagem de vendas no intervalo<', '>{{ $t(\'Sales count in interval\') }}<');
content = content.replace('>Faturamento <strong>bruto</strong><', ' v-html="$t(\'Gross Revenue html\')"><');
content = content.replace('>Soma(preco_venda * quantidade)<', '>{{ $t(\'Sum(sale_price * quantity)\') }}<');
content = content.replace('>Faturamento <strong>líquido</strong><', ' v-html="$t(\'Net Revenue html\')"><');
content = content.replace('>Bruto - devolucoes - descontos - impostos<', '>{{ $t(\'Gross - returns - discounts - taxes\') }}<');
content = content.replace('Impostos:', '{{ $t(\'Taxes\') }}:');
content = content.replace('>Lucro (intervalo)<', '>{{ $t(\'Profit (interval)\') }}<');
content = content.replace('>(preco_venda - custo_unit) * qtd<', '>{{ $t(\'(sale_price - unit_cost) * qty\') }}<');
content = content.replace('>Vendas diárias<', '>{{ $t(\'Daily sales\') }}<');
content = content.replace('>Pedidos por status<', '>{{ $t(\'Orders by status\') }}<');
content = content.replace('>Vendas por unidade<', '>{{ $t(\'Sales by unit\') }}<');
content = content.replace('>Top 5 produtos (faturamento)<', '>{{ $t(\'Top 5 products (revenue)\') }}<');
content = content.replace('>Faturamento mensal<', '>{{ $t(\'Monthly revenue\') }}<');

fs.writeFileSync('resources/js/Pages/Dashboard/SalesDashboard.vue', content);
