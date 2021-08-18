<template>
    <div id="static-map" class="map">
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister } from 'lib/common/util';

import L from 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.locatecontrol';
import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';
// import 'leaflet.locatecontrol/dist/L.Control.Locate.mapbox.min.css';


export default {
    components: {},
    filters: {},
    props: {
        lat: {
            type: Number,
            default: 0,
        },
        lng: {
            type: Number,
            default: 0,
        },
        zoom: {
            type: Number,
            default: 0,
        },
        zoomControl: {
            type: Boolean,
            default: true,
        },
        dragging: {
            type: Boolean,
            default: true,
        },
    },
    data(){
        return {
            initFlag: false,
        };
    },
    computed: {
        ...mapGetters([
            'MapProviderUrl',
        ]),
    },
    watch: {
        initFlag(){
            this.init();
        },
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
    mounted(){},
    methods: {
        init(){
            const that = this;
            setTimeout(() => {
                that.map = L.map('static-map', {
                    zoomControl: that.zoomControl,
                }).fitWorld();


                L.tileLayer(that.MapProviderUrl, {
                    maxZoom: that.zoom,
                    attribution: '   ',
                    id: 'static-mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                }).addTo(that.map);

                that.map.setView(new L.LatLng(that.lat, that.lng), that.zoom);
                L.marker(new L.LatLng(that.lat, that.lng))
                    .on('click', () => {
                        that.$emit('click-point', new L.LatLng(that.lat, that.lng));
                    })
                    .addTo(that.map);

                if (!that.dragging) {
                    that.map.dragging.disable();
                    that.map.touchZoom.disable();
                    that.map.doubleClickZoom.disable();
                    that.map.scrollWheelZoom.disable();
                }

            }, 300);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>