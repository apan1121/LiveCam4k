<template>
    <div v-if="cssLoaded && LiveCamInfo" class="live-cam-page">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" :class="{ scroll }">
            <div class="container">
                <ul class="navbar-nav">
                    <li class="nav-item live-cam-page-back"
                        :title="$t('LiveCamPage.Back')"
                        @click="goBack"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"
                        :title="$t('LiveCamPage.Share')"
                        @click="shareUrl"
                    >
                        <i class="fas fa-share-alt"></i>
                    </li>
                    <li v-if="ReportUrl" class="nav-item">
                        <a :href="ReportUrl"
                            target="_blank"
                            :title="$t('LiveCamPage.Report')"
                        >
                            <i class="far fa-paper-plane"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <live-cam-info-box
            :key="`live_cam_info_${LiveCamKey}`"
            :live-cam-key="LiveCamKey"
        >
        </live-cam-info-box>
    </div>
</template>
<script>
import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import { linkRegister } from 'lib/common/util';
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from './store/index';



export default {
    components: {
        LiveCamInfoBox: () => import('components/LiveCamInfoBox/main.vue'),
    },
    filters: {},
    mixins: [LiveCam4kPageMixin],
    props: {
        LiveCamKey: {
            type: String,
            default: '',
        },
    },
    data(){
        return {
            cssLoaded: false,
        };
    },
    computed: {
        ...mapGetters([
            'LiveCamListFlag',
            'LiveCamList',
            'PageSetting_scrollTop',
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
        ReportUrl(){
            let url = '';
            if (!!this.LiveCamInfo) {
                url = this.$t('LiveCamPage.ReportUrl', { liveCamKey: this.LiveCamInfo.key });
            }
            return url;
        },
        scroll(){
            return this.PageSetting_scrollTop > 0;
        },
    },
    watch: {
        LiveCamInfo: {
            immediate: true,
            deep: true,
            handler(newVal){
                if (newVal === false) {
                    this.$router.push(this.prevRoute);
                } else {
                    this.setTitle();
                }
            },
        },
    },
    beforeCreate(){
        if (!this.$store.state[module_name]) {
            this.$store.registerModule(module_name, module_store);
        }
    },
    created(){
        const that = this;
        linkRegister.register([
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: '/dist/css/page/live-cam-page.css',
                onload(){
                    that.cssLoaded = true;
                },
            },
        ]);
    },
    mounted(){
        this.setPageTitle(this.$t('Menu.LiveCam'));
        this.setTitle();
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({
            setShareUrlInfo: 'setShareUrlInfo',
        }),
        setTitle(){
            if (!!this.LiveCamInfo) {
                let title = '';
                if (!!this.LiveCamInfo && !!this.LiveCamInfo.video) {
                    title = this.LiveCamInfo.video.title.substr(0, 30);
                }
                this.setPageTitle(`${this.$t('Menu.LiveCam')}: ${title}`);
            }
        },
        goBack(){
            if (this.prevRoute) {
                this.$router.push(this.prevRoute);
            }
        },
        shareUrl(){
            const params = {
                title: document.title,
                url: window.location.href,
            };
            this.setShareUrlInfo(params);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>