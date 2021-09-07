<template>
    <div v-if="cssLoaded" id="travel">
        <template v-if="start_point === false">
            <choose-start-point-box @sumbit="sumbit"></choose-start-point-box>
        </template>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import { linkRegister, string } from 'lib/common/util';

import { module_name, module_store } from './store/index';

import ChooseStartPointBox from './components/ChooseStartPointBox.vue';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {
        ChooseStartPointBox,
    },
    filters: {},
    mixins: [LiveCam4kPageMixin],
    props: {},
    data(){
        return {
            cssLoaded: false,
            start_point: false,
        };
    },
    computed: {
        ...mapGetters([
        ]),
    },
    watch: {
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
                href: '/dist/css/page/travel-page.css',
                onload: () => {
                    that.cssLoaded = true;
                },
            },
        ]);
    },
    mounted(){
        this.setPageTitle(this.$t('Menu.Travel'));
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        sumbit(data){
            console.log(data);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>