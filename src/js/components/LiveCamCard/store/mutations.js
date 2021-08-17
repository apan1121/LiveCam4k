/* eslint-disable no-extra-boolean-cast */
/* eslint-disable no-param-reassign */
const main = {
    changeStatisticsType(state){
        let { StatisticsType } = state;
        const typeArr = ['video', 'weather'];
        let index = typeArr.indexOf(StatisticsType);
        index = (index + 1) % typeArr.length;
        StatisticsType = typeArr[index];

        state.StatisticsType = StatisticsType;
    },
};

export default main;