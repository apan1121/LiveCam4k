/* eslint-disable no-param-reassign */
import { jsVars } from 'lib/common/util';
import baseApi from 'lib/api/baseApi';

const API_HOST = jsVars.get('API_CONFIG.API_HOST');

const main = {
    ...baseApi,
    getLiveCamList(){
        const api_url = `${API_HOST}/log/LiveCamList.log?t=${new Date().getTime()}`;
        return main.run(api_url, 'GET');
    },
};

export default main;