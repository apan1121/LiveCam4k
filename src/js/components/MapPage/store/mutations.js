/* eslint-disable no-extra-boolean-cast */
/* eslint-disable no-param-reassign */
const main = {
    // setDiscussSortDirection(state, params){
    //     const DiscussSortDirection = JSON.parse(JSON.stringify(state.DiscussSortDirection));
    //     DiscussSortDirection[params.discussBoxId] = params.sortDirection;
    //     state.DiscussSortDirection = DiscussSortDirection;
    // },
    setCurrentPoint(state, params){
        state.currentPoint = {
            lat: 0,
            lng: 0,
            zoom: 0,
            ...state.currentPoint,
            ...params,
        };
    },
};

export default main;