export default {
    // DiscussSortDirection: state => state.DiscussSortDirection,

    LiveCamGroupList: (state, getter, rootState, rootGetter) => {
        const LiveCamGroupList = {};
        const { LiveCamList } = rootState;

        Object.values(LiveCamList).forEach((LiveCamInfo) => {
            const { local } = LiveCamInfo;
            if (!LiveCamGroupList[local]) {
                LiveCamGroupList[local] = {
                    local,
                    list: [],
                };
            }
            LiveCamGroupList[local].list.push(LiveCamInfo);
        });

        return Object.values(LiveCamGroupList);
    },
};