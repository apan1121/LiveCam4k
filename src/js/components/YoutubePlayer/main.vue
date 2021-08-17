<template>
    <div class="youtube-player-box">
        <template v-if="parseInt(embed)">
            <div id="player"
                class="youtube-player"
                :data-src="thumbnail"
            ></div>
        </template>
        <template v-else>
            <a :href="`https://youtu.be/${youtubeId}`" target="_blank">
                <div class="youtube-thumbnail lazyload"
                    :data-src="thumbnail"
                >
                    <i class="icon far fa-play-circle"></i>
                </div>
            </a>
        </template>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from './store/index';

import 'jquery.mb.ytplayer';
import YTPlayer from 'yt-player';

// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';
let YoutubePlayerObj = null;

export default {
    components: {},
    filters: {},
    props: {
        youtubeId: {
            type: String,
            default: '',
        },
        thumbnail: {
            type: String,
            default: '',
        },
        embed: {
            type: [Number, String],
            default: 0,
        },
    },
    data(){
        return {
            player: null,
            youtube_api_loaded: false,
        };
    },
    computed: {
        ...mapGetters([
        ]),
    },
    watch: {
        youtubeId: {
            immediate: true,
            handler(){
                this.$nextTick(() => {
                    this.init();
                });
            },
        },
    },
    beforeCreate(){
        const that = this;
        if (!this.$store.state[module_name]) {
            this.$store.registerModule(module_name, module_store);
        }

        if (document.querySelectorAll('script[src="https://www.youtube.com/iframe_api"]').length > 0) {
            that.youtube_api_loaded = true;
        } else {
            window.onYouTubeIframeAPIReady = function(){
                that.youtube_api_loaded = true;
            };

            // 2. This code loads the IFrame Player API code asynchronously.
            const tag = document.createElement('script');

            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }
    },
    created(){},
    mounted(){
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        init(){
            const that = this;
            clearTimeout(that.initTimer);
            that.initTimer = setTimeout(() => {
                if (parseInt(that.embed) === 1) {
                    that.player = new window.YT.Player('player', {
                        videoId: that.youtubeId,
                        playerVars: {
                            autoplay: 1,
                            controls: 1,
                            showinfo: 0,
                            modestbranding: 0,
                            rel: 0,
                        },
                        events: {
                            onReady: that.onPlayerReady,
                            // onPlaybackQualityChange: onPlayerPlaybackQualityChange,
                            // onStateChange: onPlayerStateChange,
                            // onError: onPlayerError,
                        },
                    });
                    YoutubePlayerObj = that.player;
                } else {
                    $('body').trigger('lazyImg');
                }
            }, 1000);
        },
        onPlayerReady(){
            YoutubePlayerObj.setVolume(0);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>