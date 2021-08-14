<template>
    <div class="live-cam-card" :class="{
        live,
    }"
    >
        <div class="live-cam-thumb lazyload" :data-src="thumb_img">
        </div>
        <div class="live-cam-statistics">
            <span class="statistic-item">
                <i class="fas fa-eye"></i>
                {{ carryFormatter(StatisticsFormat.view_count) }}
            </span>
            <span class="statistic-item">
                <i class="fas fa-thumbs-up"></i>
                {{ carryFormatter(StatisticsFormat.like_count) }}
            </span>
            <!-- <span class="statistic-item">
                <i class="fas fa-comment-alt"></i>
                {{ carryFormatter(StatisticsFormat.comment_count) }}
            </span> -->
        </div>
        <div class="live-cam-title ellipsis" v-text="title"></div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { linkRegister, string } from 'lib/common/util';

linkRegister.register([
    {
        rel: 'stylesheet',
        type: 'text/css',
        href: '/dist/css/page/components/live-cam-card.css',
    },
]);

// import { module_name, module_store } from './store/index';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {
        thumbnail: {
            type: [Boolean, Object],
            default: false,
        },
        title: {
            type: String,
            default: '',
        },
        live: {
            type: Boolean,
            default: false,
        },
        statistics: {
            type: Object,
            default: () => {},
        },
    },
    data(){
        return {};
    },
    computed: {
        thumb_img(){
            let thumb_img = '';
            if (!!this.thumbnail && !!this.thumbnail.url) {
                thumb_img = this.thumbnail.url;
            }

            return thumb_img;
        },
        StatisticsFormat(){
            return {
                view_count: 0,
                like_count: 0,
                dislike_count: 0,
                favorite_count: 0,
                comment_count: 0,
                ...this.statistics,
            };
        },
    },
    watch: {
    },
    created(){},
    mounted(){
        $('body').trigger('lazyImg');
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        carryFormatter: string.carryFormatter,
    },
};
</script>
<style lang="scss" scoped>
</style>