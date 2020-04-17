<template>
    <div id="app">
        <PlaylistList />
        <swipeable-cards
                v-if="this.$store.getters['songs/songsLoaded']"
                :key="this.$store.getters['playlists/currentPlaylistID']"
        />
    </div>
</template>

<script>
    import SwipeableCards from './components/SwipeableCards.vue'
    import PlaylistList from "@/components/PlaylistList";
    export default {
        name: 'app',
        components: {PlaylistList, SwipeableCards }
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
</style>
