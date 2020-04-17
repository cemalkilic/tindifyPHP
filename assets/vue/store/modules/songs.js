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
