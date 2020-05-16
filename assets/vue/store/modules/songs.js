import {getSavedSongs} from "@/api/songs";

export const namespaced = true;

export const state = {
    songs: [],
};
export const mutations = {
    SET_SONGS(state, songs) {
        state.songs = songs;
    }
};
export const actions = {
    setSongs({ commit }, songs) {
        commit('SET_SONGS', songs);
    },
    getSavedSongs({ commit }) {
        return getSavedSongs({})
            .then((resp) => {
                if (resp.data.content && resp.data.content.items) {
                    commit('SET_SONGS', resp.data.content.items);
                }
            })
    }
};
export const getters = {
    songs: state => {
        return state.songs;
    },
    songsLoaded: state => {
        return state.songs.length > 0;
    }
};
