<template>
    <div>
        <h1>{{ message }}</h1>
        <p v-html="htmlContent"></p>
        <h5>hehe</h5>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: 'Olá, Vue.js com Laravel!',
            htmlContent: '<strong>Este é um conteúdo HTML.</strong>'
        }
    }
}
</script>