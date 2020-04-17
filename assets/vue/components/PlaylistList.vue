<template>
    <b-list-group>
        <b-list-group-item
                v-for="playlist in playlists"
                :key="playlist.id"
                @click="getSongsForPlaylist(playlist.id)"
                button
        >
            <b-img fluid thumbnail left :src="playlist.images[0].url" :alt="playlist.name" width="160"></b-img>
                {{ playlist.name }} - {{ playlist.tracks.total }} songs
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    import { BImg, BListGroup, BListGroupItem } from 'bootstrap-vue';
    import 'bootstrap/dist/css/bootstrap.css'
    import 'bootstrap-vue/dist/bootstrap-vue.css'

    export default {
        name: "PlaylistList",
        components: {
            'b-list-group': BListGroup,
            'b-list-group-item': BListGroupItem,
            'b-img' : BImg
        },
        mounted() {
            this.fetchPlaylists();
        },
        computed: {
            playlists() {
              return this.$store.getters['playlists/playlists'];
            }
        },
        methods: {
            async fetchPlaylists() {
                await this.$store.dispatch('playlists/fetchPlaylists')
            },
            async getSongsForPlaylist(playlistID) {
                await this.$store.dispatch('playlists/fetchSongsForPlaylist', playlistID);
            }
        }
    }
</script>

<style scoped>

</style>
