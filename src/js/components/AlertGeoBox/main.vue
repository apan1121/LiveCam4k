<template>
    <div class="alert alert-primary"
        :class="{
            'alert-primary': CurrentPosition.status === 'ask',
            'alert-danger': CurrentPosition.status === 'deny',
            'alert-info': CurrentPosition.status === 'not support',
        }"
        role="alert"
    >
        <i class="fas fa-satellite"></i> {{ notice }}
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
    props: {},
    data(){
        return {};
    },
    computed: {
        ...mapGetters([
            'CurrentPosition',
        ]),
        notice(){
            let text = '';
            switch (this.CurrentPosition.status) {
                case 'ask':
                    text = this.$t('GeoError.ask');
                    break;
                case 'deny':
                    text = this.$t('GeoError.deny');
                    break;
                case 'not support':
                    text = this.$t('GeoError.not_support');
                    break;
                default:
                    break;
            }
            return text;
        },
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