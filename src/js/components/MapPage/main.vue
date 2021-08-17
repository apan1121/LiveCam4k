<template>
    <div>
        <div id="map" class="map"></div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister, string, localStorage, jsVars } from 'lib/common/util';
import L from 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.locatecontrol';
import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';
// import 'leaflet.locatecontrol/dist/L.Control.Locate.mapbox.min.css';

import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import CalUnits from 'lib/common/mixins/CalUnits';
import { module_name, module_store } from './store/index';


// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    mixins: [LiveCam4kPageMixin, CalUnits],
    props: {},
    data(){
        return {
            initFlag: false,
            localFlag: false,
            localStorageFlag: false,
            moveFlag: false,
            map: null,
        };
    },
    computed: {
        ...mapGetters([
            'CurrentPosition',
        ]),
        ...mapGetters(module_name, ['LiveCamList', 'currentPoint']),
        locale(){
            return this.$i18n.locale;
        },
    },
    watch: {
        initFlag(){
            this.init();
        },
        CurrentPosition: {
            immediate: true,
            deep: true,
            handler(){
                const that = this;

                if (!!that.map && !!this.CurrentPosition && this.CurrentPosition.status === 'success') {
                    const { lat, lng } = this.CurrentPosition;
                    that.setYourPoint(lat, lng, !that.localStorageFlag);
                }
            },
        },
        LiveCamList: {
            immediate: true,
            handler(){
                if (!!this.map && 1) {
                    this.setLiveCamPoint();
                }
            },
        },
    },
    beforeCreate(){
        const module_name_array = module_name.split('/');
        if (!this.$store.hasModule([module_name_array[0]])) {
            this.$store.registerModule([module_name_array[0]], { state: {}, mutations: {}, getter: {}, action: {}, namespaced: true });
        }

        if (!this.$store.hasModule(module_name_array)) {
            this.$store.registerModule(module_name_array, module_store);
        }
    },
    created(){
        const that = this;
        linkRegister.register([
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css',
                onload: () => {
                    that.initFlag = true;
                },
            },
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: '/dist/css/page/map-page.css',
            },
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: '/dist/css/page/components/live-cam-card.css',
            },
        ]);
    },
    mounted(){
        this.setPageTitle(this.$t('Menu.Map'));
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({
            setCurrentPoint: `${module_name}/setCurrentPoint`,
        }),
        carryFormatter: string.carryFormatter,
        init(){
            const that = this;
            setTimeout(() => {
                that.map = L.map('map', {
                    // zoomControl: false,
                }).fitWorld();
                const mapPath = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                // const mapPath = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibGl2ZWNhbTRrIiwiYSI6ImNrc2FuN2JnODA5cnEyd3MyanZtbmJldGoifQ.Wsh6Un3PV_8eUA7EgecozA';
                L.tileLayer(mapPath, {
                    maxZoom: 18,
                    attribution: '  ',
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                }).addTo(that.map);


                const { query } = this.$route;

                const localStorageLatLng = localStorage.get('MapLatLng', false);


                if (typeof query.lat !== 'undefined' && typeof query.lng !== 'undefined') {
                    console.log(11111);
                    that.$router.replace({ name: 'MapPage' });
                    that.map.setView(new L.LatLng(query.lat, query.lng), that.currentPoint.zoom);
                } else if (!!localStorageLatLng && 1) {
                    console.log(22222);
                    that.localStorageFlag = true;
                    const { lat, lng, zoom } = localStorageLatLng;
                    console.log(lat, lng, zoom);
                    that.map.setView(new L.LatLng(lat, lng), (zoom || 13));
                    if (!!that.CurrentPosition && that.CurrentPosition.status === 'success') {
                        const { lat, lng } = that.CurrentPosition;
                        this.setYourPoint(lat, lng);
                    }
                } else if (!!that.CurrentPosition && that.CurrentPosition.status === 'success') {
                    console.log(33333);
                    const { lat, lng } = that.CurrentPosition;
                    this.setYourPoint(lat, lng, that.currentPoint.zoom);
                } else {
                    console.log(44444);
                    that.map.setView(new L.LatLng(that.currentPoint.lat, that.currentPoint.lng), that.currentPoint.zoom);
                }

                if (that.LiveCamList.length > 0) {
                    that.setLiveCamPoint();
                }

                that.map.on('move', () => {
                    const { lat, lng } = that.map.getCenter();
                    const zoom = that.map.getZoom();
                    that.moveCurrentPoint(lat, lng, zoom);
                });
                that.map.on('zoomend', () => {
                    const { lat, lng } = that.map.getCenter();
                    const zoom = that.map.getZoom();
                    that.moveCurrentPoint(lat, lng, zoom);
                });
            }, 300);
        },
        setYourPoint(lat, lng, goTo = false){
            const that = this;
            // that.map.setView([lat, lng], 13);
            const radius = 12;

            L.marker(new L.LatLng(lat, lng)).addTo(that.map)
                .bindPopup(that.$t('MapPage.YourCurrentPosition'));


            if (!that.moveFlag && goTo) {
                that.map.setView(new L.LatLng(lat, lng), 13);
            }


            if (!that.localFlag) {
                that.localFlag = true;
                L.control.locate({
                    icon: 'fas fa-map-marker-alt',
                }).addTo(that.map);
            }
        },
        setLiveCamPoint(){
            const that = this;
            clearTimeout(that.setLiveCamPointTimer);
            that.setLiveCamPointTimer = setTimeout(() => {
                const ASSETS_HOST = jsVars.get('ASSETS_HOST');
                const greenIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41],
                });
                const markers = L.markerClusterGroup();

                that.LiveCamList.forEach((LiveCamInfo) => {
                    if (!!LiveCamInfo.gps && !!LiveCamInfo.gps.lat && !!LiveCamInfo.gps.lng) {
                        const { url } = LiveCamInfo.video.thumbnail;
                        const { lat, lng } = LiveCamInfo.gps;
                        const { key, youtube_id, video, weather } = LiveCamInfo;
                        const StatisticsFormat = {
                            view_count: 0,
                            like_count: 0,
                            dislike_count: 0,
                            favorite_count: 0,
                            comment_count: 0,
                            ...video.statistics,

                            temp: weather.temp,
                            humidity: weather.humidity,
                            wind_speed: weather.wind_speed,
                        };
                        const view_count = string.carryFormatter(StatisticsFormat.view_count);
                        const like_count = string.carryFormatter(StatisticsFormat.like_count);

                        const temp = that.transTemp(StatisticsFormat.temp, 0);
                        const { humidity, wind_speed } = StatisticsFormat;

                        const route_obj = that.$router.matcher.match({ name: 'LiveCamPage', params: { LiveCamKey: LiveCamInfo.key } });
                        const liveCamPageUrl = `${ASSETS_HOST}#${route_obj.fullPath}`;
                        const html = `
                            <a href='${liveCamPageUrl}'>
                                <div class="live-cam-card video" statistics-type="video">
                                    <div class="live-cam-thumb lazyload" data-src="${url}">
                                        <i class="icon far fa-play-circle"></i>
                                    </div>
                                    <div rel='video' class="live-cam-statistics">
                                        <span class="statistic-item">
                                            <i class="fas fa-eye"></i>
                                            ${view_count}
                                        </span>
                                        <span class="statistic-item">
                                            <i class="fas fa-thumbs-up"></i>
                                            ${like_count}
                                        </span>
                                    </div>
                                    <div rel='weather' class="live-cam-statistics">
                                        <span class="statistic-item">
                                            <i class="fas fa-thermometer-half"></i>
                                            ${temp}
                                        </span>
                                        <span class="statistic-item">
                                            <i class="fas fa-tint"></i>
                                            ${humidity}
                                        </span>
                                        <span class="statistic-item">
                                            <i class="fas fa-wind"></i>
                                            ${wind_speed}
                                        </span>
                                    </div>
                                    <div class="live-cam-title ellipsis">${video.title}</div>
                                </div>
                            </a>
                        `;
                        const marker = L.marker(new L.LatLng(lat, lng), { icon: greenIcon })
                            .bindPopup(html)
                            .on('click', () => {
                                that.map.panTo(new L.LatLng(LiveCamInfo.gps.lat, LiveCamInfo.gps.lng));

                                $('.leaflet-popup-content-wrapper').find('.icon').off('click').on('click', (e) => {
                                    e.stopPropagation();
                                    e.preventDefault();
                                    that.$router.push({ name: 'LiveCamPage', params: { LiveCamKey: LiveCamInfo.key } });
                                });
                                const type = ['video', 'weather'];
                                $('.leaflet-popup-content-wrapper').find('.live-cam-statistics').off('click').on('click', function(e){
                                    e.stopPropagation();
                                    const liveCamCard = $(this).parents('.live-cam-card');
                                    let statisticsType = liveCamCard.attr('statistics-type');
                                    let statisticsIndex = type.indexOf(statisticsType);
                                    statisticsIndex = (statisticsIndex + 1) % type.length;
                                    statisticsType = type[statisticsIndex];
                                    liveCamCard.attr('statistics-type', statisticsType);
                                });

                                $('body').trigger('lazyImg');
                            });
                        markers.addLayer(marker);
                    }
                });

                that.map.addLayer(markers);
            }, 100);
        },
        moveCurrentPoint(lat, lng, zoom){
            localStorage.set('MapLatLng', { lat, lng, zoom });
            this.setCurrentPoint({ lat, lng, zoom });
            this.moveFlag = true;
        },
    },
};
</script>
<style lang="scss" scoped>
</style>