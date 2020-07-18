import { getAllPlaylists, getSongsInPlaylist, addTindifyPlaylistSong, getPlaylistRecommendations } from "@/api/playlists";

export const namespaced = true;

export const state = {
        playlists: [],
        selectedPlaylist: 'placeholderID'
};
export const mutations = {
    SET_PLAYLISTS(state, playlists) {
        // Append elem if it does not already exist in the store
        playlists.forEach((item) => {
            if (state.playlists.indexOf(item) === -1) {
                state.playlists.push(item);
            }
        });
    },
    SET_SELECTED_PLAYLIST(state, playlistID) {
        state.selectedPlaylist = playlistID;
    }
};
export const actions = {
    fetchPlaylists({ commit }, payload ) {
        return getAllPlaylists(payload)
            .then((resp) => {
                if (resp.data.content && resp.data.content.items) {
                    commit('SET_PLAYLISTS', resp.data.content.items);
                }
            })
    },
    fetchSongsForPlaylist({ commit, dispatch }, playlistID) {
        if (playlistID === "likedSongs") {
            return dispatch('songs/getSavedSongs', {}, {root: true}).then(() => commit('SET_SELECTED_PLAYLIST', playlistID));
        }

        return getSongsInPlaylist(playlistID)
            .then((resp) => {
                if (resp.data.content && resp.data.content.items) {
                    commit('SET_SELECTED_PLAYLIST', playlistID);
                    dispatch('songs/setSongs', resp.data.content.items, {root: true});
                }
            })
    },
    fetchPlaylistRecommendations({ commit, dispatch }, playlistID) {
        return getPlaylistRecommendations(playlistID)
            .then((resp) => {
                if (resp.data.content && resp.data.content.items) {
                    commit('SET_SELECTED_PLAYLIST', playlistID);
                    dispatch('songs/setSongs', resp.data.content.items, {root: true});
                }
            })
    },
    addTindifyPlaylistSong({ commit }, songID) {
        return addTindifyPlaylistSong(songID)
            .then((resp) => {
               console.log("Song added, res: ", resp);
            });
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
