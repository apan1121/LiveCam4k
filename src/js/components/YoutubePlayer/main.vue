<template>
    <div class="youtube-player-box">
        <template v-if="parseInt(embed)">
            <div :id="`player_${playerId}`"
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
import { string } from 'lib/common/util';
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from './store/index';
// import 'jquery.mb.ytplayer';
import 'yt-player';

// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

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
        autoplay: {
            type: Boolean,
            default: true,
        },
        controls: {
            type: Boolean,
            default: true,
        },
        disablekb: {
            type: Boolean,
            default: false,
        },
    },
    data(){
        return {
            playerId: '',
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

        this.playerId = string.getRandomString(5);

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
    destroyed(){
        if (!!this.player && typeof this.player.stopVideo === 'function') {
            this.player.stopVideo();
            this.player.destroy();
        }
    },
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        init(){
            const that = this;
            clearTimeout(that.initTimer);

            that.initTimer = setTimeout(() => {

                /**
                 * YT 還沒載入好，不要執行
                 */
                if (!(typeof window.YT === 'object' && typeof window.YT.Player === 'function')) {
                    that.init();
                    return false;
                }


                if (parseInt(that.embed) === 1) {
                    console.log({
                        videoId: that.youtubeId,
                        playerVars: {
                            autoplay: that.autoplay ? 1 : 0,
                            controls: that.controls ? 1 : 0,
                            disablekb: that.disablekb ? 1 : 0,
                            showinfo: 0,
                            modestbranding: 0,
                            rel: 0,
                            playsinline: 1,
                        },
                    });
                    that.player = new window.YT.Player(`player_${that.playerId}`, {
                        videoId: that.youtubeId,
                        playerVars: {
                            autoplay: that.autoplay ? 1 : 0,
                            controls: that.controls ? 1 : 0,
                            disablekb: that.disablekb ? 1 : 0,
                            showinfo: 0,
                            modestbranding: 0,
                            rel: 0,
                            playsinline: 1,
                        },
                        events: {
                            onReady(event){
                                event.target.setVolume(0);
                                if (that.autoplay) {
                                    setTimeout(() => {
                                        if (event.target.getCurrentTime() === 0) {
                                            $(window).one('focus', () => {
                                                event.target.setVolume(0);
                                                event.target.playVideo();
                                            });
                                            $('body').one('touch click', () => {
                                                event.target.setVolume(0);
                                                event.target.playVideo();
                                            });
                                        }
                                    }, 1000);
                                }
                            },
                            onStateChange(event){
                                if (event.data === 1 && !!event.target && typeof event.target.setVolume === 'function') {
                                    event.target.setVolume(0);
                                }
                            },
                            // onPlaybackQualityChange: onPlayerPlaybackQualityChange,
                            // onStateChange: onPlayerStateChange,
                            // onError: onPlayerError,
                        },
                    });
                    window.YoutubePlayerObj = that.player;
                } else {
                    $('body').trigger('lazyImg');
                }
                return true;
            }, 100);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>