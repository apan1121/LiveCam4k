import { string } from 'lib/common/util';

const distance = string.calVincentyCircleDistance(24.793158479457, 121.37540209632, 24.885121365228, 121.28255415532);


const defaultGPS = {
    lat: string.getRandomInRange(90, -90, 10),
    lng: string.getRandomInRange(180, -180, 10),
};


export default {
    // DiscussSortDirection: state => state.DiscussSortDirection,
    scrollTop: state => state.scrollTop,
    LiveCamGroupList: (state, getter, rootState, rootGetter) => {
        const { LiveCamList, CurrentPosition } = rootState;

        let currentGPS = defaultGPS;

        if (!!CurrentPosition && CurrentPosition.status === 'success') {
            currentGPS = {
                lat: CurrentPosition.lat,
                lng: CurrentPosition.lng,
            };
        }

        let distanceStorage = [];
        for (const LiveCamKey in LiveCamList) {
            const targetGPS = LiveCamList[LiveCamKey].gps;
            const dist = string.calVincentyCircleDistance(targetGPS.lat, targetGPS.lng, currentGPS.lat, currentGPS.lng);
            distanceStorage.push({
                key: LiveCamKey,
                distance: dist,
            });
        }

        distanceStorage = distanceStorage.sort((a, b) => {
            if (a.distance > b.distance) {
                return 1;
            }

            if (a.distance === b.distance) {
                return 0;
            }

            return -1;
        });


        const LiveCamGroupList = {};
        distanceStorage.forEach((distanceInfo) => {
            const { key } = distanceInfo;
            const LiveCamInfo = LiveCamList[key];
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