/* eslint-disable no-param-reassign */
import { jsVars } from 'lib/common/util';
import baseApi from 'lib/api/baseApi';

import common from './common';

const main = {
    ...baseApi,

    ...common,
};

export default main;