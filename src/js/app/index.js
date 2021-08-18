import Vue from 'vue';
import { mapActions, mapGetters, mapMutations } from 'vuex';
import router from 'router';
import i18n from 'i18n';
import browser from 'browser-detect';

import app from './app';

import { createStore } from 'lib/store/index';
import { jsVars } from 'lib/common/util';

import 'jquery';
import 'bootstrap';


const store = createStore([
    'common',
]);

const Page = new Vue({
    el: '#appBox',
    components: {
        MainPage: () => import('components/MainPage/main.vue'),
    },
    data(){
        return {
            input: 'here',
        };
    },
    computed: {
        ...mapGetters([
        ]),
    },
    watch: {

    },
    mounted(){
        const that = this;
        that.detectBrowser();


        if ('geolocation' in window.navigator) {
            that.setCurrentPosition({
                status: 'ask',
            });
            window.navigator.geolocation.getCurrentPosition((position) => {
                that.setCurrentPosition({
                    status: 'success',
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                });
            }, (positionError) => {
                console.log(positionError);
                that.setCurrentPosition({
                    status: 'deny',
                });
            });
        } else {
            that.setCurrentPosition({
                status: 'not support',
            });
        }
    },
    methods: {
        ...mapMutations([
            'setCurrentPosition',
            'SetPageSetting',
        ]),
        init(){
        },
        detectBrowser(){
            const that = this;
            const result = browser();
            let os_name = result.os || '';
            if (os_name.indexOf('OS X') !== -1) {
                /* 因為 ios 跟 mac os 都是 OS X 所以偵測 是不是 mobile 區分成 iOS 跟 Mac OS */
                if (result.mobile) {
                    os_name = 'iOS';
                } else {
                    os_name = 'Mac OS';
                }
            } else if (os_name.indexOf('Android') !== -1) {
                /* Android 版號太多，過濾成 Android */
                os_name = 'Android';
            }

            result.os = os_name;
            const params = {
                device: os_name,
                browser: result,
            };

            that.SetPageSetting(params);
        },
    },
    store,
    router,
    i18n,
});