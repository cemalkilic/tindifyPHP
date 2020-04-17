<template>
    <section class="container">
        <div class="fixedgnu general license header">
            <i class="material-icons" @click="index = 0">refresh</i>
            <span>Tindify</span>
            <i class="material-icons">tune</i>
        </div>
        <div
                v-if="current"
                class="fixed fixed--center"
                style="z-index: 3"
                :class="{ 'transition': isVisible }">
            <Vue2InteractDraggable
                    v-if="isVisible"
                    :interact-out-of-sight-x-coordinate="500"
                    :interact-max-rotation="15"
                    :interact-x-threshold="200"
                    :interact-y-threshold="200"
                    :interact-event-bus-events="interactEventBus"
                    interact-block-drag-down
                    @draggedRight="emitAndNext('match')"
                    @draggedLeft="emitAndNext('reject')"
                    @draggedUp="emitAndNext('skip')"
                    class="rounded-borders card card--one">
                <div style="height: 100%">
                    <img
                            :src="current.albumImage"
                            class="rounded-borders"/>
                    <div class="text">
                        <h2>{{current.songName}}, <span>{{current.artistName}}</span></h2>
                    </div>
                </div>
            </Vue2InteractDraggable>
        </div>
        <div
                v-if="next"
                class="rounded-borders card card--two fixed fixed--center"
                style="z-index: 2">
            <div style="height: 100%">
                <img
                        :src="next.albumImage"
                        class="rounded-borders"/>
                <div class="text">
                    <h2>{{next.songName}}, <span>{{next.artistName}}</span></h2>
                </div>
            </div>
        </div>
        <div
                v-if="index + 2 < cards.length"
                class="rounded-borders card card--three fixed fixed--center"
                style="z-index: 1">
            <div style="height: 100%">
            </div>
        </div>
        <div class="footer fixed">
            <div class="btn btn--decline" @click="reject">
                <BIconXCircle width="100%" height="100%"/>
            </div>
            <div class="btn btn--skip" @click="toggleAudio()">
                <BIconPlayFill v-show="paused" width="100%" height="100%"/>
                <BIconPauseFill v-show="playing" width="100%" height="100%"/>
            </div>
            <div class="btn btn--like" @click="match">
                <BIconHeart width="100%" height="100%"/>
            </div>
        </div>
    </section>
</template>
<script>
    import { Howl } from "howler";
    import { Vue2InteractDraggable, InteractEventBus } from 'vue2-interact'
    import {
        BIconPlayFill,
        BIconPauseFill,
        BIconXCircle,
        BIconHeart
    } from 'bootstrap-vue';

    const EVENTS = {
        MATCH: 'match',
        SKIP: 'skip',
        REJECT: 'reject'
    }
    export default {
        name: 'SwipeableCards',
        components: { Vue2InteractDraggable, BIconPlayFill, BIconPauseFill, BIconXCircle, BIconHeart},
        data() {
            return {
                isVisible: true,
                index: 0,
                interactEventBus: {
                    draggedRight: EVENTS.MATCH,
                    draggedLeft: EVENTS.REJECT,
                    draggedUp: EVENTS.SKIP
                },
                audio: {},
                paused: true
            }
        },
        mounted () {
            this.paused = true;
            this.createAudio(this.current.previewURL)
        },
        computed: {
            current() {
                return this.cards[this.index]
            },
            next() {
                return this.cards[this.index + 1]
            },
            playing() {
                return !this.paused
            },
            cards() {
                return this.$store.getters['songs/songs'];
            },
        },
        methods: {
            match() {
                InteractEventBus.$emit(EVENTS.MATCH)
            },
            reject() {
                InteractEventBus.$emit(EVENTS.REJECT)
            },
            skip() {
                InteractEventBus.$emit(EVENTS.SKIP)
            },
            toggleAudio() {
                if (this.playing) {
                    // stop the current one
                    this.audio.pause()
                    this.paused = true
                } else {
                    this.paused = false
                    this.audio.play()
                }
            },
            createAudio(path, autoplay = false, volume = 0.7) {
                const fadeTime = 1000 // milliseconds

                // If there is a song already, fade it out
                // TODO not sure about this check
                if (this.audio && this.audio.fade) {
                    this.audio.fade(volume, 0, fadeTime * 1.4);
                }

                this.audio = new Howl({
                    src: path,
                    format: ['mp3'],
                    html5: true,
                    loop: false,
                })

                if (autoplay) {
                    // start song with fade in
                    this.audio.play()
                    this.audio.fade(0, volume, fadeTime);
                }
            },
            emitAndNext(event) {
                this.$emit(event, this.index)
                setTimeout(() => this.isVisible = false, 200)
                setTimeout(() => {
                    this.index++
                    this.isVisible = true
                    this.createAudio(this.current.previewURL, this.playing)
                }, 200)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container {
        background: #eceff1;
        width: 100%;
        height: 100vh;
    }
    .header {
        width: 100%;
        height: 60vh;
        z-index: 0;
        top: 0;
        left: 0;
        color: white;
        text-align: center;
        font-style: italic;
        font-family: 'Engagement', cursive;
        background: #f953c6;
        background: -webkit-linear-gradient(to top, #b91d73, #f953c6);
        background: linear-gradient(to top, #b91d73, #f953c6);
        clip-path: polygon(0 1%, 100% 0%, 100% 76%, 0 89%);
        display: flex;
        justify-content: space-between;
        span {
            display: block;
            font-size: 4rem;
            padding-top: 2rem;
            text-shadow: 1px 1px 1px red;
        }
        i {
            padding: 24px;
        }
    }
    .footer {
        width: 77vw;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        padding-bottom: 30px;
        justify-content: center;
        align-items: center;
    }
    .btn {
        position: relative;
        width: 50px;
        height: 50px;
        padding: .2rem;
        border-radius: 50%;
        background-color: white;
        box-shadow: 0 6px 6px -3px rgba(0,0,0,0.02), 0 10px 14px 1px rgba(0,0,0,0.02), 0 4px 18px 3px rgba(0,0,0,0.02);
        cursor: pointer;
        transition: all .3s ease;
        user-select: none;
        -webkit-tap-highlight-color:transparent;
        &:active {
            transform: translateY(4px);
        }
        i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            &::before {
                content: '';
            }
            .svg-inline--fa {
                height: 3em;
                width: 3em;
            }
        }
        &--like {
            background-color: red;
            padding: .5rem;
            color: white;
            box-shadow: 0 10px 13px -6px rgba(0,0,0,.2), 0 20px 31px 3px rgba(0,0,0,.14), 0 8px 38px 7px rgba(0,0,0,.12);
            i {
                font-size: 32px;
            }
        }
        &--decline {
            color: red;
        }
        &--skip {
            color: green;
            width: 100px;
            height: 100px;
        }
    }
    .flex {
        display: flex;
        &--center {
            align-items: center;
            justify-content: center;
        }
    }
    .fixed {
        position: fixed;
        &--center {
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    }
    .rounded-borders {
        border-radius: 12px;
    }
    .card {
        width: 80vw;
        height: 60vh;
        color: white;
        img {
            object-fit: cover;
            display: block;
            width: 100%;
            height: 100%;
        }
        &--one {
            box-shadow: 0 1px 3px rgba(0,0,0,.2), 0 1px 1px rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);
        }
        &--two {
            transform: translate(-48%, -48%);
            box-shadow: 0 6px 6px -3px rgba(0,0,0,.2), 0 10px 14px 1px rgba(0,0,0,.14), 0 4px 18px 3px rgba(0,0,0,.12);
        }
        &--three {
            background: rgba(black, .8);
            transform: translate(-46%, -46%);
            box-shadow: 0 10px 13px -6px rgba(0,0,0,.2), 0 20px 31px 3px rgba(0,0,0,.14), 0 8px 38px 7px rgba(0,0,0,.12);
        }
        .text {
            position: absolute;
            bottom: 0;
            width: 100%;
            background:black;
            background:rgba(0,0,0,0.5);
            border-bottom-right-radius: 12px;
            border-bottom-left-radius: 12px;
            text-indent: 20px;
            span {
                font-weight: normal;
            }
        }
    }
    .transition {
        animation: appear 200ms ease-in;
    }
    @keyframes appear {
        from {
            transform: translate(-48%, -48%);
        }
        to {
            transform: translate(-50%, -50%);
        }
    }
</style>
