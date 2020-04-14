<template>
    <b-list-group>
        <b-list-group-item v-for="playlist in playlists" :key="playlist.id">
            <button @click="getSongsForPlaylist(playlist.id)"> click me</button>
                {{ playlist.name }} - {{ playlist.tracks.total }} songs
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    import { BListGroup, BListGroupItem } from 'bootstrap-vue';
    import 'bootstrap/dist/css/bootstrap.css'
    import 'bootstrap-vue/dist/bootstrap-vue.css'

    import { getAllPlaylists} from "@/api/playlists";

    export default {
        name: "PlaylistList",
        components: {
            'b-list-group': BListGroup,
            'b-list-group-item': BListGroupItem
        },
        data() {
            return {
                playlists: []
            }
        },
        mounted() {
            this.fetchPlaylists();
        },
        methods: {
            async fetchPlaylists() {
                const {data} = await getAllPlaylists();
                this.playlists = data.content.items;
            },
            getSongsForPlaylist(playlistID) {
                this.$root.$emit('swipeable-cards-event-first', playlistID)
            }
        }
    }
</script>

<style scoped>

</style>
