<template>
    <b-list-group>
        <b-list-group-item
                v-for="playlist in playlists"
                :key="playlist.id"
                @click="goCards(playlist.id)"
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

    import store from '../store/store';

    export default {
        name: "PlaylistList",
        components: {
            'b-list-group': BListGroup,
            'b-list-group-item': BListGroupItem,
            'b-img' : BImg
        },
        data() {
            return {
                offset: 0,
                limit: 5
            }
        },
        beforeRouteEnter(to, from, next) {
            if (store.getters['playlists/playlists'].length > 0) {
                // we have already some playlists, continue to route
                next();
            } else {
                // get playlists for the user initially
                store.dispatch('playlists/fetchPlaylists', {}).then(() => {
                    next();
                });
            }
        },
        mounted() {
            this.handleScroll();
        },
        computed: {
            playlists() {
              return this.$store.getters['playlists/playlists'];
            }
        },
        methods: {
            goCards(playlistID) {
                this.$router.push({ name: 'cards', params: { playlistID: playlistID }})
            },
            handleScroll () {
                window.onscroll = async () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow) {
                        this.offset += 5;
                        await this.$store.dispatch('playlists/fetchPlaylists', {limit: this.limit, offset: this.offset})
                    }
                };
            },
        }
    }
</script>

<style scoped>

</style>
