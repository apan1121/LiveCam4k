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
            LiveCamList[item.key] = item;
        });
        state.LiveCamList = LiveCamList;

        state.LiveCamListFlag = true;
    },

    setShareUrlInfo(state, data){
        if (data !== false && !!data.url) {
            state.ShareUrlInfo = {
                title: '',
                url: '',
                ...data,
            };
        } else {
            state.ShareUrlInfo = false;
        }
    },
};