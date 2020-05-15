import httpClient from './httpClient';

const ENDPOINT = '/playlists';

// All endpoints related to playlists

const getAllPlaylists = ({limit, offset}) => {
    limit = limit || 5;
    offset = offset || 0;

    return httpClient.get(ENDPOINT + "?limit=" + limit + "&offset=" + offset);
};
const getSongsInPlaylist = (playlistID) => httpClient.get(ENDPOINT + "/" + playlistID + "/songs");

export {
    getAllPlaylists,
    getSongsInPlaylist
}
