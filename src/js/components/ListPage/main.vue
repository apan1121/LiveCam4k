<template>
    <div>
        <h2 class="page-title-header">
            {{ $t("Menu.List") }}
        </h2>

        <template v-for="LiveCamGroup in LiveCamGroupList">
            <live-cam-group-box
                :key="LiveCamGroup.local"
                :title="$t(`LocalName.${LiveCamGroup.local}`)"
            >
                <div class="row">
                    <template v-for="LiveCamInfo in LiveCamGroup.list">
                        <div :key="LiveCamInfo.key" class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <live-cam-card
                                v-bind="LiveCamInfo"
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
        ...mapGetters(module_name, [
            'LiveCamGroupList',
        ]),
    },
    watch: {
    },
    beforeCreate(){
        const module_name_array = module_name.split('/');
        if (!this.$store.hasModule([module_name_array[0]])) {
            this.$store.registerModule([module_name_array[0]], { state: { a: '' }, mutations: { a: () => {} }, getter: { b: () => {} }, action: {}, namespaced: true });
        }

        if (!this.$store.hasModule(module_name_array)) {
            console.log('here', module_name_array);
            this.$store.registerModule(module_name_array, module_store);
        }
    },
    created(){},
    mounted(){
        this.setPageTitle(this.$t('Menu.List'));
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
    },
};
</script>
<style lang="scss" scoped>
</style>