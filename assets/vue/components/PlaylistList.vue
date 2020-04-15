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

    import { getAllPlaylists, getSongsInPlaylist } from "@/api/playlists";

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
              return this.$store.getters.playlists;
            }
        },
        methods: {
            async fetchPlaylists() {
                const {data} = await getAllPlaylists();
                await this.$store.dispatch('setPlaylists', data.content.items)
            },
            async getSongsForPlaylist(playlistID) {
                const {data} = await getSongsInPlaylist(playlistID);
                await this.$store.dispatch('setSongsForPlaylist',
                    {
                        playlistID,
                        'songs': data.content.items
                });
            }
        }
    }
</script>

<style scoped>

</style>
