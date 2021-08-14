<template>
    <div>
        <div id="map" class="map"></div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister, string, localStorage } from 'lib/common/util';
import L from 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.locatecontrol';
import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';
// import 'leaflet.locatecontrol/dist/L.Control.Locate.mapbox.min.css';

import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import { module_name, module_store } from './store/index';


// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    mixins: [LiveCam4kPageMixin],
    props: {},
    data(){
        return {
            initFlag: false,
            localFlag: false,
            moveFlag: false,
            map: null,
        };
    },
    computed: {
        ...mapGetters([
            'CurrentPosition',
        ]),
        ...mapGetters(module_name, ['LiveCamList']),
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
                    that.setYourPoint(lat, lng);
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
            this.$store.registerModule([module_name_array[0]], { state: { a: '' }, mutations: { a: () => {} }, getter: { b: () => {} }, action: {}, namespaced: true });
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
        ...mapMutations({}),
        carryFormatter: string.carryFormatter,
        init(){
            const that = this;
            that.map = L.map('map', {
            }).fitWorld();
            // let mapPath = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const mapPath = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibGl2ZWNhbTRrIiwiYSI6ImNrc2FuN2JnODA5cnEyd3MyanZtbmJldGoifQ.Wsh6Un3PV_8eUA7EgecozA';
            L.tileLayer(mapPath, {
                maxZoom: 18,
                attribution: '',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
            }).addTo(that.map);


            const localStorageLatLng = localStorage.get('MapLatLng', false);

            if (!!localStorageLatLng && 1) {
                const { lat, lng } = localStorageLatLng;
                that.map.setView(new L.LatLng(lat, lng), 13);
            } else if (!!that.CurrentPosition && that.CurrentPosition.status === 'success') {
                const { lat, lng } = that.CurrentPosition;
                this.setYourPoint(lat, lng);
            } else {
                that.map.setView(new L.LatLng(25.049234259657265, 121.52369884103936), 13);
            }

            if (that.LiveCamList.length > 0) {
                that.setLiveCamPoint();
            }

            that.map.on('move', () => {
                const { lat, lng } = that.map.getCenter();
                localStorage.set('MapLatLng', { lat, lng });
                that.moveFlag = true;
            });
        },
        setYourPoint(lat, lng){
            const that = this;
            // that.map.setView([lat, lng], 13);
            const radius = 12;

            L.marker(new L.LatLng(lat, lng)).addTo(that.map)
                .bindPopup(that.$t('MapPage.YourCurrentPosition'));


            if (!that.moveFlag) {
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
                        const { url } = LiveCamInfo.thumbnail;
                        const { lat, lng } = LiveCamInfo.gps;
                        const { title } = LiveCamInfo;
                        const StatisticsFormat = {
                            view_count: 0,
                            like_count: 0,
                            dislike_count: 0,
                            favorite_count: 0,
                            comment_count: 0,
                            ...LiveCamInfo.statistics,
                        };
                        const view_count = string.carryFormatter(StatisticsFormat.view_count);
                        const like_count = string.carryFormatter(StatisticsFormat.like_count);
                        const html = `
                            <div class="live-cam-card">
                                <div class="live-cam-thumb lazyload" data-src="${url}">
                                    <i class="icon far fa-play-circle"></i>
                                </div>
                                <div class="live-cam-statistics">
                                    <span class="statistic-item">
                                        <i class="fas fa-eye"></i>
                                        ${view_count}
                                    </span>
                                    <span class="statistic-item">
                                        <i class="fas fa-thumbs-up"></i>
                                        ${like_count}
                                    </span>
                                </div>
                                <div class="live-cam-title ellipsis">${title}</div>
                            </div>
                        `;
                        const marker = L.marker(new L.LatLng(lat, lng), { icon: greenIcon })
                            .bindPopup(html)
                            .on('click', () => {
                                that.map.panTo(new L.LatLng(LiveCamInfo.gps.lat, LiveCamInfo.gps.lng));

                                $('.leaflet-popup-content-wrapper').find('.icon').on('click', () => {
                                    console.log('play', LiveCamInfo);
                                });
                                $('body').trigger('lazyImg');
                            });
                        markers.addLayer(marker);
                    }
                });

                that.map.addLayer(markers);
            }, 100);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>