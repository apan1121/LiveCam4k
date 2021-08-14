export default {
    LiveCamList: (state, getter, rootState, rootGetter) => {
        const LiveCamGroupList = {};
        const { LiveCamList } = rootState;
        return Object.values(LiveCamList);
    },
};