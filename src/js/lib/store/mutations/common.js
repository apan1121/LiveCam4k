import Vue from 'vue';

export default {
    initSystem(state, params){
        // state.prizeList = prizeList;
    },
    SetPageSetting(state, params){
        for (const key in params) {
            state[`PageSetting_${key}`] = params[key];
        }
    },
    CheckAdBlock(state, data){
        state.adBlocked = data;
    },

    setCurrentPosition(state, data){
        state.CurrentPosition = data;
    },

    setLiveCamList(state, data){
        const LiveCamList = {};
        data.forEach((item) => {
            const key = `${item.local}_${item.serial_number}`;
            item.key = key;
            LiveCamList[key] = item;
        });
        state.LiveCamList = LiveCamList;
    },
};