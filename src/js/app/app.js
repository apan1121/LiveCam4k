import Vue from 'vue';
import $ from 'jquery';
import 'vendor/imgLiquid/imgLiquid';
import 'vendor/lazyload/lazyload';
import { jsVars } from 'lib/common/util';

Vue.config.debug = process.env.NODE_ENV !== 'production';
Vue.config.devtools = process.env.NODE_ENV !== 'production';

/* 全頁偵測 resize Image */
let resizeImageTimer = null;
$('body').on('resizeImg', () => {
    clearTimeout(resizeImageTimer);
    resizeImageTimer = setTimeout(() => {
        $('.imgLiquidFill').imgLiquid();
    }, 50);
});

let lazyImageTimer = null;
$('body').on('lazyImg', () => {
    clearTimeout(lazyImageTimer);
    lazyImageTimer = setTimeout(() => {
        const queryKey = '.lazyload:not(.lazy-bind)';
        const target = document.querySelectorAll(queryKey);
        // eslint-disable-next-line no-undef
        lazyload(target, {
            src: 'data-src',
            threshold: 0,
        });
        $(queryKey).addClass('lazy-bind lazy-empty')
            .unbind('load')
            .bind('load', function(){
                $(this).removeClass('lazy-empty').addClass('done');
            });
    }, 50);
});


const { origin, pathname } = window.location;
const BASE_API_HOST = `${origin}${pathname}`;
jsVars.set('API_CONFIG.API_HOST', BASE_API_HOST);
jsVars.set('ASSETS_HOST', BASE_API_HOST);