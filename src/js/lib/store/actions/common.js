import api from 'lib/api/index';

export default {
    initSystem({ commit }, params){
        commit('initSystem', params);
    },

    getLiveCamList({ commit }, params){
        return new Promise((resolve, reject) => {
            api.getLiveCamList(params).success((response) => {
                commit('setLiveCamList', response);
                resolve(response);
            }).error((response) => {
                reject(response);
            });
        });
    },
};