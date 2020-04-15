import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        songs: [],
        playlists: [],
        selectedPlaylist: 'placeholderID'
    },
    mutations: {
        SET_PLAYLISTS(state, playlists) {
            state.playlists = playlists;
        },
        SET_SONGS(state, songs) {
            state.songs = songs;
        },
        SET_SELECTED_PLAYLIST(state, playlistID) {
            state.selectedPlaylist = playlistID;
        }
    },
    actions: {
        setPlaylists({ commit }, playlists) {
            commit('SET_PLAYLISTS', playlists);
        },
        setSongsForPlaylist(state, {playlistID, songs}) {
            state.commit('SET_SELECTED_PLAYLIST', playlistID);
            state.commit('SET_SONGS', songs);
        }
    },
    getters: {
        playlists: state => {
            return state.playlists;
        },
        songs: state => {
            return state.songs;
        },
        songsLoaded: state => {
            return state.songs.length > 0;
        },
        currentPlaylistID: state => {
            return state.selectedPlaylist;
        }
    }
})
