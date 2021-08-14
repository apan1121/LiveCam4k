import Vue from 'vue';
import { mapActions, mapGetters, mapMutations } from 'vuex';
import router from 'router';
import i18n from 'i18n';


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
        ]),
        init(){
        },
    },
    store,
    router,
    i18n,
});