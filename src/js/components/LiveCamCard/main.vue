<template>
    <div class="live-cam-card" :class="{ preview }">
        <router-link :to="{ name: 'LiveCamPage', params: { LiveCamKey: liveCamKey } }">
            <div class="live-cam-card-wrapper" :class="{
                live: video.live,
            }
            " :statistics-type="StatisticsType"
                @mouseover="setHoverPreview(true)"
                @mouseleave="setHoverPreview(false)"
            >
                <div class="live-cam-thumb-video" :data-loading="$t('Video.loading')">
                    <template v-if="preview">
                        <youtube-player
                            :key="`live_cam_card_video_${video.youtube_id}`"
                            :youtube-id="video.youtube_id"
                            :thumbnail="video.thumbnail.url"
                            :embed="embed"
                            :controls="false"
                        ></youtube-player>
                    </template>
                </div>

                <div class="live-cam-thumb lazyload" :data-src="thumb_img">
                    <i class="icon far fa-play-circle"></i>
                </div>


                <div rel="video" class="live-cam-statistics" @click.stop.prevent="changeStatisticsType">
                    <span class="statistic-item" :title="$t('Video.view_count') + ': ' + StatisticsFormat.view_count">
                        <i class="fas fa-eye"></i>
                        {{ carryFormatter(StatisticsFormat.view_count) }}
                    </span>
                    <span class="statistic-item" :title="$t('Video.like_count') + ': ' + StatisticsFormat.like_count">
                        <i class="fas fa-thumbs-up"></i>
                        {{ carryFormatter(StatisticsFormat.like_count) }}
                    </span>
                </div>

                <div rel="weather" class="live-cam-statistics" @click.stop.prevent="changeStatisticsType">
                    <span class="statistic-item" :title="$t('Weather.temp') + ': ' + StatisticsFormat.temp">
                        <i class="fas fa-thermometer-half"></i>
                        {{ StatisticsFormat.temp }}
                    </span>
                    <span class="statistic-item" :title="$t('Weather.humidity') + ': ' + StatisticsFormat.humidity + '%'">
                        <i class="fas fa-tint"></i>
                        {{ StatisticsFormat.humidity }} %
                    </span>
                    <span class="statistic-item" :title="$t('Weather.weather_status') + ': ' + $t(`WeatherStatus.${StatisticsFormat.weather_status}`)">
                        <i class="icon" :class="WeatherIcon[StatisticsFormat.weather_icon]"></i>
                    </span>
                </div>
                <div ref="title" class="live-cam-title"
                    :class="{ ellipsis: !hoverTitle }"
                    @mouseover="setHoverTitle(true)"
                    @mouseleave="setHoverTitle(false)"
                    v-text="video.title"></div>
            </div>
        </router-link>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister, string } from 'lib/common/util';
import CalUnits from 'lib/common/mixins/CalUnits';
import { module_name, module_store } from './store/index';


linkRegister.register([
    {
        rel: 'stylesheet',
        type: 'text/css',
        href: '/dist/css/page/components/live-cam-card.css',
    },
]);

// import { module_name, module_store } from './store/index';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {
        YoutubePlayer: () => import('components/YoutubePlayer/main.vue'),
    },
    filters: {},
    mixins: [CalUnits],
    props: {
        liveCamKey: {
            type: String,
            default: '',
        },
        thumbnail: {
            type: [Boolean, Object],
            default: false,
        },
        title: {
            type: String,
            default: '',
        },
        video: {
            type: Object,
            default: () => {},
        },
        weather: {
            type: Object,
            default: () => {},
        },
        embed: {
            type: [Number, String],
            default: 0,
        },
    },
    data(){
        return {
            hoverPreview: false,
            hoverTitle: false,
            preview: false,
        };
    },
    computed: {
        ...mapGetters({
            StatisticsType: `${module_name}/StatisticsType`,
            WeatherIcon: 'WeatherIcon',
            PageSetting_device: 'PageSetting_device',
        }),
        isMobile(){
            return ['Android', 'iOS'].indexOf(this.PageSetting_device);
        },
        thumb_img(){
            let thumb_img = '';
            if (!!this.video.thumbnail && !!this.video.thumbnail.url) {
                thumb_img = this.video.thumbnail.url;
            }

            return thumb_img;
        },
        StatisticsFormat(){

            return {
                view_count: this.video.statistics.view_count,
                like_count: this.video.statistics.like_count,
                dislike_count: this.video.statistics.dislike_count,
                favorite_count: this.video.statistics.favorite_count,
                comment_count: this.video.statistics.comment_count,

                temp: this.transTemp(this.weather.temp, 0),
                humidity: this.weather.humidity,
                wind_speed: this.weather.wind_speed,

                weather_icon: this.weather.weather_icon,
                weather_status: this.weather.weather_status,
            };
        },
    },
    watch: {
    },
    beforeCreate(){
        const module_name_array = module_name.split('/');
        if (!this.$store.hasModule([module_name_array[0]])) {
            this.$store.registerModule([module_name_array[0]], { state: { }, mutations: { }, getter: { }, action: {}, namespaced: true });
        }

        if (!this.$store.hasModule(module_name_array)) {
            this.$store.registerModule(module_name_array, module_store);
        }
    },
    created(){},
    mounted(){
        $('body').trigger('lazyImg');
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({
            changeStatisticsType: `${module_name}/changeStatisticsType`,
        }),
        carryFormatter: string.carryFormatter,
        setHoverPreview(bool){
            if (!!parseInt(this.embed) && 1) {
                clearTimeout(this.setHoverPreviewTimer);
                if (bool) {
                    this.setHoverPreviewTimer = setTimeout(() => {
                        this.preview = true;
                    }, 100);
                } else {
                    this.preview = false;
                }
                this.hoverPreview = bool;
            }
        },
        setHoverTitle(bool){
            clearInterval(this.setHoverTitleTimer);
            if (bool) {
                this.hoverTitle = true;
                this.setHoverTitleTimer = setInterval(() => {
                    this.$refs.title.scrollLeft += 1;
                }, 20);
            } else {
                this.hoverTitle = false;
                this.$refs.title.scrollLeft = 0;
            }
        },
    },
};
</script>
<style lang="scss" scoped>
</style>