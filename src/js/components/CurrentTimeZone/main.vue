<template>
    <span>{{ LocalDateTime }}</span>
</template>
<script>
import moment from 'moment';
import { mapActions, mapMutations, mapGetters } from 'vuex';

// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {
        timezone_sec: {
            type: Number,
            default: 0,
        },
    },
    data(){
        return {
            utc_timestamp: 0,
        };
    },
    computed: {
        ...mapGetters([
        ]),
        LocalDateTime(){
            let dateTime = '--';
            if (!!this.utc_timestamp && 1) {
                const timestamp = this.utc_timestamp + this.timezone_sec * 1000;
                dateTime = moment(timestamp).utc().format('YYYY-MM-DD HH:mm:ss');
            }

            return dateTime;
        },
    },
    watch: {
    },
    beforeCreate(){
    },
    created(){},
    mounted(){
        this.utc_timestamp = parseInt(moment.utc().format('x'));
        clearInterval(this.utcTimer);
        this.utcTimer = setInterval(() => {
            this.utc_timestamp += 1000;
        }, 1000);
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