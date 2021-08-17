<template>
    <div v-if="LiveCamInfo" class="live-cam-page">
        <div class="live-cam-page-back" @click="goBack">
            <i class="fas fa-chevron-left"></i>
        </div>

        <live-cam-info-box
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


linkRegister.register([
    {
        rel: 'stylesheet',
        type: 'text/css',
        href: '/dist/css/page/live-cam-page.css',
    },
]);


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
        return {};
    },
    computed: {
        ...mapGetters([
            'LiveCamListFlag',
            'LiveCamList',
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
    },
    watch: {
        LiveCamInfo: {
            immediate: true,
            deep: true,
            handler(newVal){
                if (newVal === false) {
                    this.$router.push(this.prevRoute);
                } else {
                    const title = this.LiveCamInfo.video.title.substr(1, 30);
                    this.setPageTitle(`${this.$t('Menu.LiveCam')}: ${title}`);
                }
            },
        },
    },
    beforeCreate(){
        if (!this.$store.state[module_name]) {
            this.$store.registerModule(module_name, module_store);
        }
    },
    created(){},
    mounted(){
        this.setPageTitle(this.$t('Menu.LiveCam'));
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        goBack(){
            if (this.prevRoute) {
                this.$router.push(this.prevRoute);
            }
        },
    },
};
</script>
<style lang="scss" scoped>
</style>