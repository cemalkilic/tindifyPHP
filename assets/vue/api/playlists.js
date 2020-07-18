import httpClient from './httpClient';

const ENDPOINT = '/playlists';

// All endpoints related to playlists

const getAllPlaylists = ({limit, offset}) => {
    limit = limit || 5;
    offset = offset || 0;

    return httpClient.get(ENDPOINT + "?limit=" + limit + "&offset=" + offset);
};
const getSongsInPlaylist = (playlistID) => httpClient.get(ENDPOINT + "/" + playlistID + "/songs");

const addTindifyPlaylistSong = (songID) => {
    return httpClient.post(ENDPOINT + "/tindify/songs", {
        songIDs: songID,
    });
};

const getPlaylistRecommendations = (playlistID) => httpClient.get(ENDPOINT + "/" + playlistID + "/recommendations");

export {
    getAllPlaylists,
    getSongsInPlaylist,
    addTindifyPlaylistSong,
    getPlaylistRecommendations
}
