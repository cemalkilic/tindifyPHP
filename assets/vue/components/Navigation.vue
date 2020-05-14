<template>
    <div>
        <h2>Tindify</h2>
        <nav>
            <router-link class="spacing" v-for="routes in links"
                         :key="routes.id"
                         :to="`${routes.page}`">{{routes.text}}
            </router-link>
        </nav>
    </div>
</template>

<script>
    export default {
        name: 'Navigation',
        data() {
            return {
                links: [
                    {
                        id: 1,
                        text: 'Home',
                        page:'/'
                    },
                    {
                        id: 1,
                        text: 'Playlists',
                        page:'/playlists'
                    }
                ]
            }
        }
    }
</script>
<style>
    .spacing {
        margin-right: 10px;
    }
</style>
