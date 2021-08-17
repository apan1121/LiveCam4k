import { string } from 'lib/common/util';
import { mapGetters } from 'vuex';

import configureMeasurements, { allMeasures } from 'convert-units';

const convert = configureMeasurements(allMeasures);

export default {
    props: {
    },
    data(){
        return {
        };
    },
    computed: {
        ...mapGetters([
            'SettingUnits',
        ]),
    },
    methods: {
        transTemp(value, fixed = 2){
            let val = value;
            val = string.calcTemperatureUnit(val, this.SettingUnits.temperature, fixed);
            return val;
        },
        transLength(value, fixed = 2){
            let val = value;
            val = string.calcLengthUnit(val, this.SettingUnits.length, fixed);
            return val;
        },
        transSpeed(value, fixed = 2){
            let val = value;
            val = string.calcSpeedUnit(val, this.SettingUnits.length, fixed);
            return val;
        },
    },
    beforeDestroyed(){
    },
    destroyed(){},
    mounted(){
    },
};