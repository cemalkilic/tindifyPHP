<template>
    <div>
        <h1>Welcome to Tindify!</h1>
        <h2>
            What if listening to music were as easy as romance?
        </h2>
        <p>
            Tindify grabs a random playlist from your owned/followed playlists,<br>
            then gives you a 30 second clip to hear. If you like it, click<br>
            right (button or keyboard) and it will be added to a Spotify<br>
            playlist called Tindify on your account. Don't like the song?<br>
            Click left and move on to the next song.<br>
            You can select any of your playlists to play tracks from.<br>
        </p>

        <h2>
            Interested? Login to begin!
        </h2>

        <div class="login">
            <a class="login" href="/auth"><img :src="loginImage"/></a>
        </div>
    </div>

</template>

<script>
    const loginImage = require('../../images/login.png');
    export default {
        name: 'Home',
        data() {
            return {
                loginImage
            }
        }
    }
</script>
<style>

</style>
