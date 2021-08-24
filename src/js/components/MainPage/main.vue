<template>
    <div class="live-cam-4k-page">
        <header>
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <div class="container">
                    <router-link class="navbar-brand" :to="{ name: 'ListPage'}">
                        {{ $t("WebTitle") }}
                    </router-link>
                    <ul class="navbar-nav">
                        <li class="nav-item" :class="{ active: route.name == 'ListPage'}">
                            <router-link class="nav-link" :to="{ name: 'ListPage'}">
                                <i class="icon fas fa-list"></i>
                                <span>{{ $t("Menu.List") }}</span>
                            </router-link>
                        </li>
                        <li class="nav-item" :class="{ active: route.name == 'MapPage'}">
                            <router-link class="nav-link" :to="{ name: 'MapPage'}">
                                <i class="icon fas fa-map-marked-alt"></i>
                                <span>{{ $t("Menu.Map") }}</span>
                            </router-link>
                        </li>
                        <li class="nav-item" :class="{ active: route.name == 'TravelPage'}">
                            <router-link class="nav-link" :to="{ name: 'TravelPage'}">
                                <i class="icon fas fa-plane"></i>
                                <span>{{ $t("Menu.Travel") }}</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link" @click="openLangModal">
                                <i class="icon fas fa-globe-asia"></i>
                                <span>{{ chooseLang }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="container">
            <template v-if="CurrentPosition && CurrentPosition.status !== 'success'">
                <alert-geo-box></alert-geo-box>
            </template>
            <router-view></router-view>
        </div>
        <template v-if="triggerLangModal">
            <lang-modal :trigger="triggerLangModal"></lang-modal>
        </template>

        <template v-if="ShareUrlInfo">
            <share-url-box></share-url-box>
        </template>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { detectAnyAdblocker } from 'just-detect-adblock';


const pc_min_size = 567;
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {
        LangModal: () => import('components/LangModal/main.vue'),
        AlertGeoBox: () => import('components/AlertGeoBox/main.vue'),
        ShareUrlBox: () => import('components/ShareUrlBox/main.vue'),
    },
    filters: {},
    props: {},
    data(){
        return {
            triggerLangModal: false,
        };
    },
    computed: {
        ...mapGetters([
            'CurrentPosition',
            'ShareUrlInfo',
        ]),
        route(){
            return this.$route;
        },
        chooseLang(){
            return this.$i18n.locale;
        },
    },
    watch: {
    },
    created(){},
    mounted(){
        this.init();
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({
            SetPageSetting: 'SetPageSetting',
            CheckAdBlock: 'CheckAdBlock',
        }),
        init(){
            const that = this;
            /* 偵測瀏覽器寬度決定 mode  */
            $(window).on('resize', () => {
                clearTimeout(that.windowResizeTimer);
                that.windowResizeTimer = setTimeout(() => {
                    let mode_type = 'pc';
                    const width = $('body').width();
                    if (width < pc_min_size) {
                        mode_type = 'mobile';
                    }
                    that.SetPageSetting({ mode_type, width });
                }, 100);
            }).trigger('resize');


            $(window).on('scroll', () => {
                that.SetPageSetting({ scrollTop: $(window).scrollTop() });
            });


            /* 偵測 adblocker */
            detectAnyAdblocker().then((detected) => {
                that.CheckAdBlock(detected);
            });

            that.getLiveCamList();
        },
        openLangModal(){
            this.triggerLangModal = new Date().getTime();
        },
        getLiveCamList(){
            const that = this;
            that.$store.dispatch('getLiveCamList').then(() => {
            }, () => {});
        },
    },
};
</script>
<style lang="scss" scoped>
</style>