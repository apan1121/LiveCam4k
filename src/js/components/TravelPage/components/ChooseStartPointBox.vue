<template>
    <div class="choose-start-point-box">
        <h5 class="choose-start-point-header">
            環遊世界起始地點
        </h5>

        <div v-if="input_point" class="choose-start-point-input" :class="{ process: runRandom_flag }">
            {{ input_point.lat.toFixed(5) }}, {{ input_point.lng.toFixed(5) }}
            <div class="choose-start-point-tools">
                <div class="choose-start-point-input-button"
                    rel="random"
                    :data-rotate="dice_icon_rotate"
                    :title="$t('TravelPage.Dice.Random')"
                    @click="runRandom"
                >
                    <i :class="dice_icon[dice_icon_choose]"></i>
                </div>
                <div v-if="CurrentPosition && CurrentPosition.status === 'success'" class="choose-start-point-input-button"
                    rel="current"
                    :title="$t('TravelPage.Dice.CurrentPoint')"
                    @click="runCurrentPosition"
                >
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mb-5">
                <div class="choose-start-point-map">
                    <static-map-box
                        v-if="input_point"
                        :lat="input_point.lat"
                        :lng="input_point.lng"
                        :zoom="mapZoom"
                        :zoom-control="true"
                        :dragging="false"
                        :move-mark-wait="50"
                    ></static-map-box>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-5">
                <template v-if="calc_input_point && !!calc_input_point.lat && !! calc_input_point.lng">
                    <calc-live-cam-list
                        :lat="calc_input_point.lat"
                        :lng="calc_input_point.lng"
                        :amount="liveCamKeyCount"
                        :same-country-limit="5"
                        :class="{ 'auto-height': autoHeight }"
                        :live-cam-keys.sync="chooseLiveCamKeys"
                    ></calc-live-cam-list>
                </template>
                <template v-else>
                    <div class="choose-live-cam-box">
                        <div class="choose-live-cam-empty">
                            處理中
                        </div>
                    </div>
                </template>
            </div>
        </div>



        <div v-if="!is_process && !!calc_input_point && chooseLiveCamKeys.length > 0" class="choose-start-point-start-button">
            <button class="btn btn-primary btn-block" @click="sumbit">
                開始旅行
            </button>
        </div>
    </div>
</template>
<script>
import { string } from 'lib/common/util';

import { mapActions, mapMutations, mapGetters } from 'vuex';
import { module_name, module_store } from '../store/index';

import CalcLiveCamList from '../components/CalcLiveCamList.vue';
// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {
        StaticMapBox: () => import('components/StaticMapBox/main.vue'),
        CalcLiveCamList,
    },
    filters: {},
    props: {},
    data(){
        return {
            input_point: false,
            calc_input_point: false,
            chooseLiveCamKeys: [],


            dice_icon: [
                'fas fa-dice-one',
                'fas fa-dice-two',
                'fas fa-dice-three',
                'fas fa-dice-four',
                'fas fa-dice-five',
                'fas fa-dice-six',
            ],
            dice_icon_choose: 0,
            dice_icon_rotate: 0,
            runRandom_flag: false,
            mapZoom: 10,
            is_process: false,
            liveCamKeyCount: 50,
        };
    },
    computed: {
        ...mapGetters([
            'CurrentPosition',
            'PageSetting_width',
        ]),
        autoHeight(){
            return this.PageSetting_width <= 767;
        },
    },
    watch: {
        CurrentPosition: {
            immediate: true,
            deep: true,
            handler(){
                const that = this;
                that.calc_input_point = false;
                that.calc_input_point_focus = false;
                console.log(that.CurrentPosition.status);
                if (that.CurrentPosition.status === 'success') {
                    that.input_point = {
                        ...this.CurrentPosition,
                    };
                } else if (that.CurrentPosition.status === 'ask') {

                } else {
                    this.runRandom();
                }
            },
        },
        input_point: {
            immediate: true,
            deep: true,
            handler(newVal){
                const that = this;
                that.calc_input_point = false;
                clearTimeout(that.input_point_timer);
                if (!!newVal && 1) {
                    that.input_point_timer = setTimeout(() => {
                        that.calc_input_point = {
                            ...that.input_point,
                        };
                    }, 1000);
                }
            },
        },
        calc_input_point(){
            this.isProcess();
        },
        chooseLiveCamKeys(){
            this.isProcess();
        },
    },
    beforeCreate(){
    },
    created(){
        // this.setRandomInputPoint();
        // this.setRandomDice();
    },
    mounted(){},
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        isProcess(){
            this.is_process = true;
            clearTimeout(this.isProcessTimer);
            this.isProcessTimer = setTimeout(() => {
                this.is_process = false;
            }, 1500);
        },
        runRandom(){
            const that = this;
            clearInterval(that.runRandomDice);
            clearTimeout(that.stopRunRandom);
            clearTimeout(that.runRandomTimer);
            that.runRandom_flag = true;
            that.mapZoom = 1;
            that.calc_input_point = false;
            that.runRandomTimer = setTimeout(() => {
                that.runRandomDice = setInterval(() => {
                    that.setRandomDice();
                    that.setRandomInputPoint();
                }, 100);

                that.stopRunRandom = setTimeout(() => {
                    that.runRandom_flag = false;
                    that.mapZoom = 5;
                    clearInterval(that.runRandomDice);
                }, 3000);
            }, 500);
        },
        runCurrentPosition(){
            this.input_point = {
                ...this.CurrentPosition,
            };
        },
        setRandomDice(){
            this.dice_icon_choose = string.getRandomInRange(0, this.dice_icon.length - 1);
            this.dice_icon_rotate = string.getRandomInRange(0, 18);
        },
        setRandomInputPoint(){
            this.input_point = {
                lat: string.getRandomInRange(-70, 70, 3),
                lng: string.getRandomInRange(-180, 180, 3),
            };
        },
        sumbit(){
            const params = {
                start: this.calc_input_point,
                liveCamKeys: this.chooseLiveCamKeys,
            };
            this.$emit('submit', params);
        },
    },
};
</script>
<style lang="scss" scoped>
</style>