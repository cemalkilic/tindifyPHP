import httpClient from './httpClient';

const ENDPOINT = '/playlists';

// All endpoints related to playlists

const getAllPlaylists = () => httpClient.get(ENDPOINT);
const getSongsInPlaylist = (playlistID) => httpClient.get(ENDPOINT + "/" + playlistID + "/songs");

export {
    getAllPlaylists,
    getSongsInPlaylist
}
