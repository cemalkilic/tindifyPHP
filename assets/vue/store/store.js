import Vue from 'vue'
import Vuex from 'vuex'
import * as songs from '@/store/modules/songs'
import * as playlists from '@/store/modules/playlists'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        playlists,
        songs
    }
})
