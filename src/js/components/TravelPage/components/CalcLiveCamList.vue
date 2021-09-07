<template>
    <div class="choose-live-cam-box">
        <div ref="list" class="choose-live-cam-list">
            <template v-for="liveCamKey in chooseLiveCamKeys">
                <div :key="liveCamKey" class="choose-live-cam-wrapper">
                    <div class="choose-live-cam-item"
                        rel="title"
                    >
                        {{ LiveCamList[liveCamKey].video.title }}
                    </div>
                    <div class="choose-live-cam-item"
                        rel="local"
                    >
                        {{ LiveCamList[liveCamKey].local }}
                    </div>
                </div>
            </template>
            <div v-if="calcing" class="calculating">
                計算中
            </div>
        </div>
    </div>
</template>
<script>
import { string } from 'lib/common/util';

import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from '../store/index';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {
        lat: {
            type: Number,
            default: 0,
        },
        lng: {
            type: Number,
            default: 0,
        },
        amount: {
            type: Number,
            default: 0,
        },
        sameCountryLimit: {
            type: Number,
            default: 0,
        },
    },
    data(){
        return {
            calcing: false,
            last_point: false,
            chooseLiveCamKeys: [],
            sameCountryCount: {},
        };
    },
    computed: {
        ...mapGetters([
            'LiveCamList',
            'PageSetting_scrollTop',
        ]),
    },
    watch: {
        lat: {
            immediate: true,
            handler(){
                this.calc();
            },
        },
        lng: {
            immediate: true,
            handler(){
                this.calc();
            },
        },
        chooseLiveCamKeys: {
            deep: true,
            immediate: true,
            handler(){
                this.$emit('update:live-cam-keys', this.chooseLiveCamKeys);
                $(this.$refs.list).scrollTop($(this.$refs.list)[0].scrollHeight + 1000);
            },
        },
    },
    beforeCreate(){
    },
    created(){
    },
    mounted(){
        const that = this;
        const wh = $(window).height();
        const { top } = $(this.$el).offset();
        if (that.PageSetting_scrollTop > top || (that.PageSetting_scrollTop + wh) < top) {
            $('html, body').stop(true, true).animate({ scrollTop: top }, 500, 'swing', () => {
            });
        }
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        calc(){
            const that = this;
            that.last_point = false;
            that.chooseLiveCamKeys = [];
            that.sameCountryCount = {};
            clearTimeout(this.calcTimer);
            clearTimeout(that.calcLiveCamTimer);
            that.calcTimer = setTimeout(() => {
                that.last_point = {
                    lat: that.lat,
                    lng: that.lng,
                };
                that.calcLiveCam();
            }, 100);
        },
        calcLiveCam(){
            const that = this;
            clearTimeout(that.calcLiveCamTimer);
            that.calcing = true;
            if (that.last_point !== false) {
                let liveCamKey = false;
                liveCamKey = that.getNearestLivCam();
                if (!!liveCamKey && 1) {
                    that.chooseLiveCamKeys.push(liveCamKey);
                    const { local } = that.LiveCamList[liveCamKey];
                    const sameCountryCount = JSON.parse(JSON.stringify(that.sameCountryCount));
                    if (!sameCountryCount[local]) {
                        sameCountryCount[local] = 0;
                    }
                    sameCountryCount[local] += 1;
                    that.sameCountryCount = sameCountryCount;
                }
                if (that.chooseLiveCamKeys.length < that.amount && !!liveCamKey) {
                    that.calcLiveCamTimer = setTimeout(() => {
                        that.calcLiveCam();
                    }, 20);
                } else {
                    that.calcing = false;
                }
            } else {
                that.calcing = false;
            }
        },
        getNearestLivCam(){
            const that = this;
            let LiveCamList = Object.values(JSON.parse(JSON.stringify(that.LiveCamList)));
            let liveCamKey = false;
            if (LiveCamList.length > 0) {
                LiveCamList = LiveCamList.filter((item) => {
                    let flag = true;
                    const { local, embed, video } = item;

                    if (embed !== '1') {
                        flag = false;
                    }

                    if (!video.live) {
                        flag = false;
                    }

                    if (that.chooseLiveCamKeys.includes(item.key)) {
                        flag = false;
                    }

                    if (that.sameCountryCount[local] >= that.sameCountryLimit) {
                        flag = false;
                    }

                    return flag;
                });

                LiveCamList = LiveCamList.map((item) => {
                    item.distance = string.calVincentyCircleDistance(item.gps.lat, item.gps.lng, that.lat, that.lng);
                    return item;
                });

                LiveCamList = LiveCamList.sort((a, b) => {
                    if (a.distance > b.distance) {
                        return 1;
                    } if (a.distance === b.distance) {
                        return 0;
                    }
                    return -1;
                });

                liveCamKey = LiveCamList[0].key;
            }
            return liveCamKey;
        },
    },
};
</script>
<style lang="scss" scoped>
</style>