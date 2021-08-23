<template>
    <div :key="LiveCamKey" class="live-cam-info-box">
        <div class="live-cam-player-wrapper">
            <youtube-player
                v-if="LiveCamInfo.video"
                :key="`live_cam_info_video_${LiveCamInfo.video.youtube_id}` + LiveCamInfo.video.youtube_id"
                :youtube-id="LiveCamInfo.video.youtube_id"
                :thumbnail="LiveCamInfo.video.thumbnail.url"
                :embed="LiveCamInfo.embed"
            ></youtube-player>
        </div>

        <div class="row live-cam-info-content" rel="statistic">
            <div class="col-12 col-sm-6">
                <span class="statistic-item" rel="local-info">
                    <i class="far fa-clock"></i>
                    {{ LocalDateTime }}
                </span>
            </div>
            <div class="col-12 col-sm-6 text-right">
                <span class="statistic-item" rel="statistic-info">
                    <i class="icon fas fa-eye"></i>
                    {{ carryFormatter(StatisticsFormat.view_count) }}
                </span>
                <span class="statistic-item" rel="statistic-info">
                    <i class="icon fas fa-thumbs-up"></i>
                    {{ carryFormatter(StatisticsFormat.like_count) }}
                </span>
                <span class="statistic-item" rel="statistic-info">
                    <i class="icon fas fa-thumbs-down"></i>
                    {{ carryFormatter(StatisticsFormat.dislike_count) }}
                </span>
                <span class="statistic-item" rel="statistic-info">
                    <i class="icon fas fa-comment-alt"></i>
                    {{ carryFormatter(StatisticsFormat.comment_count) }}
                </span>
            </div>
        </div>

        <h3 class="live-cam-info-content" rel="title">
            <div class="live-icon" :class="{ live: LiveCamInfo.video.live }">
                <i class="icon fas fa-circle"></i>
            </div>
            <div class="video-title">
                {{ LiveCamInfo.video.title }}
            </div>
        </h3>

        <div class="live-cam-info-content" rel="video-info" :class="{ 'detail-open': detailOpen }">
            <div class="live-cam-info-provider">
                <h4>
                    <div class="icon">
                        <i class="fab fa-youtube"></i>
                    </div>

                    <a :href="`https://www.youtube.com/channel/${LiveCamInfo.video.channel_id}`" target="_blank">
                        {{ LiveCamInfo.video.channel_title }}
                    </a>
                    <div class="icon-handler" @click="triggerDetail">
                        <i class="fas fa-caret-down"></i>
                    </div>
                </h4>
            </div>
            <div class="live-cam-info-video-detail">
                <div class="row">
                    <div class="col-12 col-md-8">
                        <div class="video-description" v-html="VideoDescription">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="video-hashtag">
                            <template v-for="(tag, tagIndex) in LiveCamInfo.video.tags">
                                <router-link :key="tagIndex" class="hashtag" :to="{ name: 'ListPage', query: {'hashtag': tag} }">
                                    #{{ tag }}
                                </router-link>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="close-description" @click="triggerDetail">
                            <i class="fas fa-caret-up"></i>
                            關閉資訊
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row weather-wrapper">
            <div class="col-12 col-sm-6 col-md-4 order-2 order-sm-1">
                <div class="live-cam-info-content" rel="weather-info">
                    <div class="live-cam-info-weather-info"
                        style="flex: 100%"
                        :title="$t('Weather.weather_status') + ': ' + $t(`WeatherStatus.${LiveCamInfo.weather.weather_status}`)"
                    >
                        <div class="live-cam-info-weather-info-icon">
                            <i :class="WeatherIcon[LiveCamInfo.weather.weather_icon]"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ $t('WeatherStatus.' + LiveCamInfo.weather.weather_status) }}
                        </div>
                    </div>
                    <div class="live-cam-info-weather-info" :title="$t('Weather.temp') + ': ' + transTemp(LiveCamInfo.weather.temp, 2)">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ transTemp(LiveCamInfo.weather.temp, 2) }}
                        </div>
                    </div>
                    <div class="live-cam-info-weather-info" :title="$t('Weather.humidity') + ': ' + LiveCamInfo.weather.humidity + '%'">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ LiveCamInfo.weather.humidity }} %
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 order-3 order-sm-2">
                <div class="live-cam-info-content" rel="weather-info">
                    <div class="live-cam-info-weather-info" :title="$t('Weather.sunrise') + ': ' + SunInfo.sunrise">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="icon icon-sunrise"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ SunInfo.sunrise }}
                        </div>
                    </div>
                    <div class="live-cam-info-weather-info" :title="$t('Weather.sunset') + ': ' + SunInfo.sunset">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="icon icon-sunset"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ SunInfo.sunset }}
                        </div>
                    </div>
                    <div class="live-cam-info-weather-info" :title="$t('Weather.wind_speed') + ': ' + transSpeed(LiveCamInfo.weather.wind_speed)">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ transSpeed(LiveCamInfo.weather.wind_speed) }}
                        </div>
                    </div>
                    <div class="live-cam-info-weather-info" :title="$t('Weather.pressure') + ': ' + LiveCamInfo.weather.pressure + 'hPa'">
                        <div class="live-cam-info-weather-info-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="live-cam-info-weather-info-content">
                            {{ LiveCamInfo.weather.pressure }} hPa
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xs-12 col-md-4 order-1 order-sm-3">
                <div class="live-cam-info-content" rel="map">
                    <div class="live-cam-info-map-square">
                        <static-map-box
                            :lat="LiveCamInfo.gps.lat"
                            :lng="LiveCamInfo.gps.lng"
                            :zoom="16"
                            :zoom-control="true"
                            :dragging="false"
                            @click-point="clickPoint"
                        ></static-map-box>
                        <h5 class="live-cam-info-map-title">
                            <i class="icon fas fa-map-marker-alt"></i>
                            {{ LiveCamInfo.local }}, {{ LiveCamInfo.city }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import CalUnits from 'lib/common/mixins/CalUnits';
import { linkRegister, string, jsVars } from 'lib/common/util';
import moment from 'moment';
import twitter from 'twitter-text';
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from './store/index';


// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';


linkRegister.register([
    {
        rel: 'stylesheet',
        type: 'text/css',
        href: '/dist/css/page/components/live-cam-info.css',
    },
]);


export default {
    components: {
        YoutubePlayer: () => import('components/YoutubePlayer/main.vue'),
        StaticMapBox: () => import('components/StaticMapBox/main.vue'),
    },
    filters: {
    },
    mixins: [CalUnits],
    props: {
        LiveCamKey: {
            type: String,
            default: '',
        },
    },
    data(){
        return {
            utc_timestamp: 0,
            detailOpen: false,
        };
    },
    computed: {
        ...mapGetters([
            'LiveCamListFlag',
            'LiveCamList',
            'WeatherIcon',
        ]),
        LiveCamInfo(){
            if (!this.LiveCamListFlag) {
                return null;
            }
            if (!!this.LiveCamList[this.LiveCamKey] && 1) {
                return this.LiveCamList[this.LiveCamKey];
            }
            return false;
        },
        StatisticsFormat(){
            return {
                view_count: this.LiveCamInfo.video.statistics.view_count,
                like_count: this.LiveCamInfo.video.statistics.like_count,
                dislike_count: this.LiveCamInfo.video.statistics.dislike_count,
                favorite_count: this.LiveCamInfo.video.statistics.favorite_count,
                comment_count: this.LiveCamInfo.video.statistics.comment_count,
            };
        },
        LocalDateTime(){
            let dateTime = '--';
            if (!!this.LiveCamInfo && 1) {
                const timestamp = this.utc_timestamp + this.LiveCamInfo.timezone.sec * 1000;
                dateTime = moment(timestamp).utc().format('YYYY-MM-DD HH:mm:ss');
            }

            return dateTime;
        },
        SunInfo(){
            // console.log(this.utc_timestamp);
            // console.log('sunrise', this.LiveCamInfo.weather.sunrise * 1000);
            // console.log('sunset', this.LiveCamInfo.weather.sunset * 1000);
            // console.log('timezone', this.LiveCamInfo.timezone.sec * 1000);
            let sunrise = this.LiveCamInfo.weather.sunrise * 1000;
            sunrise = parseInt(moment(sunrise).utc().format('x'));
            sunrise += this.LiveCamInfo.timezone.sec * 1000;
            sunrise = moment(sunrise).utc().format('HH:mm');

            let sunset = this.LiveCamInfo.weather.sunset * 1000;
            sunset = parseInt(moment(sunset).utc().format('x'));
            sunset += this.LiveCamInfo.timezone.sec * 1000;
            sunset = moment(sunset).utc().format('HH:mm');

            return { sunrise, sunset };
        },
        VideoDescription(){
            return this.formatDesciption(this.LiveCamInfo.video.description ?? '無詳細訊息');
        },
    },
    watch: {
        VideoDescription: {
            immediate: true,
            deep: true,
            handler(){
                const that = this;
                that.$nextTick(() => {
                    $(that.$el).find('.video-description')
                        .find('.hashtag')
                        .unbind('click')
                        .bind('click', function(e){
                            e.stopPropagation();
                            e.preventDefault();
                            const hashtag = $(this).attr('title').substr(1);
                            that.$router.push({ name: 'ListPage', query: { hashtag } });
                        });
                    console.log($(that.$el).find('.video-description')
                        .find('.hashtag').length);
                });
            },
        }
    },
    beforeCreate(){
        if (!this.$store.state[module_name]) {
            this.$store.registerModule(module_name, module_store);
        }
    },
    created(){},
    mounted(){
        this.utc_timestamp = parseInt(moment.utc().format('x'));
        clearInterval(this.utcTimer);
        this.utcTimer = setInterval(() => {
            this.utc_timestamp += 1000;
        }, 1000);
    },
    updated(){},
    destroyed(){
        clearInterval(this.utcTimer);
    },
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        carryFormatter: string.carryFormatter,
        triggerDetail(){
            this.detailOpen = !this.detailOpen;
        },
        clickPoint(point){
            const that = this;
            that.$router.push({ name: 'MapPage', query: point });
        },
        formatDesciption(content){
            const that = this;
            const ASSETS_HOST = jsVars.get('ASSETS_HOST');
            let html = content;
            const route_obj = that.$router.matcher.match({ name: 'LiveCamPage', query: { hashtag: '' } });
            const hashtagUrl = `${ASSETS_HOST}#${route_obj.fullPath}`;

            const options = {
                hashtagUrlBase: hashtagUrl,
            };

            html = twitter.autoLink(twitter.htmlEscape(content), options);
            html = html.replace(/\n/ig, '<br>');
            return html;
        },
    },
};
</script>
<style lang="scss" scoped>
</style>