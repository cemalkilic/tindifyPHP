import Vue from 'vue'
import Router from 'vue-router'

import PlaylistList from '@/components/PlaylistList'
import SwipeableCards from '@/components/SwipeableCards'
import Home from '@/components/Home'

Vue.use(Router);

export default new Router({
    routes: [
        {
          path: '/',
          name: 'Home',
          component: Home
        },
        {
            path: '/playlists',
            name: 'playlists',
            component: PlaylistList
        },
        {
            path: '/cards',
            name: 'cards',
            component: SwipeableCards
        }
    ]
})
