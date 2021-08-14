<template>
    <div class="live-cam-group-box">
        <div class="live-cam-group-title">
            <h3>{{ title }}</h3>
        </div>
        <div class="live-cam-group-content">
            <slot></slot>
        </div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';

// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {
        title: {
            type: String,
            default: '',
        },
    },
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
    mounted(){},
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