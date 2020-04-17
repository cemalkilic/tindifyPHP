import { getAllPlaylists, getSongsInPlaylist } from "@/api/playlists";

export const namespaced = true;

export const state = {
        playlists: [],
        selectedPlaylist: 'placeholderID'
};
export const mutations = {
    SET_PLAYLISTS(state, playlists) {
        state.playlists = playlists;
    },
    SET_SELECTED_PLAYLIST(state, playlistID) {
        state.selectedPlaylist = playlistID;
    }
};
export const actions = {
    fetchPlaylists({ commit }) {
        return getAllPlaylists()
            .then((resp) => {
                commit('SET_PLAYLISTS', resp.data.content.items);
            })
    },
    fetchSongsForPlaylist({ commit, dispatch }, playlistID) {
        return getSongsInPlaylist(playlistID)
            .then((resp) => {
                commit('SET_SELECTED_PLAYLIST', playlistID);
                dispatch('songs/setSongs', resp.data.content.items, { root: true});
            })
    }
};
export const getters = {
    playlists: state => {
        return state.playlists;
    },
    currentPlaylistID: state => {
        return state.selectedPlaylist;
    }
};
