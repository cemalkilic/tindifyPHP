import httpClient from './httpClient';

const ENDPOINT = '/songs';

// All endpoints related to songs

const getSavedSongs = ({limit, offset}) => {
    limit = limit || 20;
    offset = offset || 0;

    return httpClient.get(ENDPOINT + "/saved" + "?limit=" + limit + "&offset=" + offset);
};

const getRecommendedSongs = ({limit, offset}) => {
    limit = limit || 20;
    offset = offset || 0;

    return httpClient.get(ENDPOINT + "/recommendations" + "?limit=" + limit + "&offset=" + offset);
};

export {
    getSavedSongs,
    getRecommendedSongs
}
