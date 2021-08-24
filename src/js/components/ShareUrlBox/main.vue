<template>
    <div v-if="canShare" class="share-url-box">
        <div class="share-url-wrapper">
            <div class="share-url-content">
                <div class="share-url-title">
                    分享至
                </div>
                <template v-for="shareBtn in shareBtnList">
                    <button :key="shareBtn.key"
                        class="btn btn-block mb-3"
                        :class="`btn-${shareBtn.color}`"
                        @click="shareTo(shareBtn.key)"
                    >
                        <i :class="shareBtn.icon"></i>
                        {{ shareBtn.title }}
                    </button>
                </template>

                <div class="share-url-box-footer">
                    <div class="share-url-box-footer-wrapper">
                        <div class="share-url-box-footer-item" rel="input">
                            <input id="share-url-box-input"
                                ref="shareUrlBoxInput"
                                type="text"
                                :value="ShareUrlInfo.url"
                                readonly
                            >
                        </div>
                        <div class="share-url-box-footer-item" rel="copy-url-btn">
                            <template v-if="clipboardJSFlag === ''">
                                <button class="btn btn-sm btn-outline-primary share-url-clipborad-btn" data-clipboard-target="#share-url-box-input">
                                    複製連結
                                </button>
                            </template>
                            <template v-if="clipboardJSFlag === 'success'">
                                <button class="btn btn-sm btn-success">
                                    複製成功
                                </button>
                            </template>
                            <template v-if="clipboardJSFlag === 'error'">
                                <button class="btn btn-sm btn-warning">
                                    複製失敗
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="share-url-close" @click="closeSharBox">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import clipboardJS from 'clipboard';
import { linkRegister } from 'lib/common/util';
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from './store/index';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {},
    data(){
        return {
            canShare: false,
            isMobile: false,
            clipboardJSFlag: '',
            shareBtnList: [
                {
                    key: 'facebook',
                    title: 'Facebook',
                    color: 'primary',
                    icon: 'fab fa-facebook',
                },
                {
                    key: 'messenger',
                    title: 'Messenger',
                    color: 'info',
                    icon: 'fab fa-facebook-messenger',
                },
                {
                    key: 'line',
                    title: 'LINE',
                    color: 'success',
                    icon: 'fab fa-line',
                },
            ],
        };
    },
    computed: {
        ...mapGetters([
            'ShareUrlInfo',
            'PageSetting_device',
        ]),
    },
    watch: {
        PageSetting: {
            immediate: true,
            deep: true,
            handler(newVal){
                const that = this;
                that.isMobile = ['iOS', 'Android'].includes(that.PageSetting_device);
            },
        },
        clipboardJSFlag(newVal, oldVal){
            const that = this;
            if (!!newVal && 1) {
                setTimeout(() => {
                    that.clipboardJSFlag = '';
                }, 500);
            }
        },
    },
    beforeCreate(){
    },
    created(){
        const that = this;
        linkRegister.register([
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: '/dist/css/page/components/share-url-box.css',
                onload(){
                    that.cssLoaded = true;
                },
            },
        ]);
    },
    mounted(){
        this.canShare = false;
        this.initClipboard();
        this.ShareUrlTriggerAct();
    },
    updated(){},
    destroyed(){
        this.setShareUrlInfo(false);
    },
    methods: {
        ...mapActions({}),
        ...mapMutations({
            setShareUrlInfo: 'setShareUrlInfo',
        }),
        initClipboard(){
            const that = this;
            const { title, url } = this.ShareUrlInfo;
            // eslint-disable-next-line new-cap
            const clipboard = new clipboardJS('.share-url-box .share-url-clipborad-btn', {
                text(trigger){
                    return url;
                },
            });

            clipboard.on('success', (e) => {
                console.log('複製成功');
                that.clipboardJSFlag = 'success';
                e.clearSelection();
            });

            clipboard.on('error', (e) => {
                console.log('複製失敗', e);
                that.clipboardJSFlag = 'error';
            });
        },
        ShareUrlTriggerAct(){
            const that = this;
            clearTimeout(that.shareUrlTriggerTimer);

            that.shareUrlTriggerTimer = setTimeout(() => {
                const { title, url } = this.ShareUrlInfo;
                const params = {
                    title,
                    url,
                };
                if (!!window.navigator && !!window.navigator.share) {
                    window.navigator.share(params).then((response) => {
                        that.closeSharBox();
                    }).catch((error) => {
                        that.closeSharBox();
                    });
                } else {
                    this.canShare = true;
                }
            }, 100);
        },
        shareTo(type){
            const that = this;
            switch (type) {
                case 'facebook':
                    that.shareToFacebook();
                    break;
                case 'messenger':
                    that.shareToMessenger();
                    break;
                case 'line':
                    that.shareToLine();
                    break;
                default:
                    break;
            }
        },
        shareToFacebook(){
            const { title, url } = this.ShareUrlInfo;

            window.FB.ui({
                method: 'share',
                href: url,
                display: 'popup',
            }, (response) => {
                if (response && !response.error_message) {
                    console.log('發送完成');
                } else {
                    console.log('發送失敗');
                }
            });
        },
        shareToMessenger(){
            const { title, url } = this.ShareUrlInfo;

            let messengerOpenFlag = false;
            if (this.isMobile) {
                try {
                    window.location.href = `fb-messenger://share?link=${encodeURIComponent(url)}`;
                    messengerOpenFlag = true;
                } catch (e) {
                    messengerOpenFlag = false;
                }
            }
            if (!messengerOpenFlag) {
                window.FB.ui({
                    method: 'send',
                    link: url,
                    display: 'popup',
                }, (response) => {
                    if (response && !response.error_message) {
                        console.log('發送完成');
                    } else {
                        console.log('發送失敗');
                    }
                });
            }
        },
        shareToLine(){
            const that = this;
            const { title, url } = this.ShareUrlInfo;

            if (that.isMobile) {
                const href = `http://line.me/R/msg/text/?${encodeURIComponent(url)}`;
                window.open(href);
            } else {
                const href = `https://timeline.line.me/social-plugin/share?url=${encodeURIComponent(url)}`;
                window.open(href, 'shareLine', 'width=600,height=600');
            }
        },
        closeSharBox(){
            this.setShareUrlInfo(false);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>