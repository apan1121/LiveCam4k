<template>
    <div>
        <template v-for="LiveCamGroup in LiveCamGroupList">
            <live-cam-group-box
                :key="`live_cam_group_${LiveCamGroup.local}`"
                :title="$t(`LocalName.${LiveCamGroup.local}`)"
            >
                <div class="row">
                    <template v-for="LiveCamInfo in LiveCamGroup.list">
                        <div :key="LiveCamInfo.key" class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <live-cam-card
                                :key="`live_cam_card_${LiveCamInfo.key}`"
                                v-bind="LiveCamInfo"
                                :live-cam-key="LiveCamInfo.key"
                            >
                            </live-cam-card>
                        </div>
                    </template>
                </div>
            </live-cam-group-box>
        </template>

    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister } from 'lib/common/util';
import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import { module_name, module_store } from './store/index';

linkRegister.register([
    {
        rel: 'stylesheet',
        type: 'text/css',
        href: '/dist/css/page/list-page.css',
    },
]);

// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {
        LiveCamGroupBox: () => import('components/LiveCamGroupBox/main.vue'),
        LiveCamCard: () => import('components/LiveCamCard/main.vue'),
    },
    filters: {},
    mixins: [LiveCam4kPageMixin],
    props: {},
    data(){
        return {};
    },
    computed: {
        ...mapGetters([
            'PageSetting_scrollTop',
        ]),
        ...mapGetters(module_name, [
            'scrollTop',
            'LiveCamGroupList',
        ]),
    },
    watch: {
        PageSetting_scrollTop(newVal){
            const that = this;
            that.setScrollTop(newVal);
        },
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
        const that = this;
        that.setPageTitle('');
        // that.setPageTitle(this.$t('Menu.List'));
        that.$nextTick(() => {
            $(window).scrollTop(that.scrollTop);
        });
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations(module_name, [
            'setScrollTop',
        ]),
    },
};
</script>
<style lang="scss" scoped>
</style>