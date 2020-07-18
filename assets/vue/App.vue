<template>
    <div id="app">
        <Navigation></Navigation>
        <router-view class="fullWidth"/>
    </div>
</template>

<script>

    import Navigation from '@/components/Navigation'
    export default {
        name: 'app',
        components: { Navigation }
    }
</script>

<style>
    body {
        margin: 0;
        z-index: 0;
    }
    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
    }
    .fullWidth {
        max-width: 100%;
    }
</style>
