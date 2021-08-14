<template>
    <div>TravelPage</div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import LiveCam4kPageMixin from 'lib/common/mixins/LiveCam4kPageMixin';

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
        return {};
    },
    computed: {
        ...mapGetters([
        ]),
    },
    watch: {
    },
    created(){},
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