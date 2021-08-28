<template>
    <div v-if="cssLoaded" id="travel">
        travel
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';

import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';
import { linkRegister } from 'lib/common/util';

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
            cssLoaded: false,
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
    },
};
</script>
<style lang="scss" scoped>
</style>