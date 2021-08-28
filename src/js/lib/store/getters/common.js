import { string } from 'lib/common/util';

export default {
    PageSetting_width: state => state.PageSetting_width,
    PageSetting_mode_type: state => state.PageSetting_mode_type,
    PageSetting_scrollTop: state => state.PageSetting_scrollTop,
    PageSetting_device: state => state.PageSetting_device,
    PageSetting_browser: state => state.PageSetting_browser,

    CurrentPosition: state => state.CurrentPosition,

    SettingUnits: state => state.SettingUnits,

    LiveCamList: state => state.LiveCamList,
    LiveCamListFlag: state => state.LiveCamListFlag,

    WeatherIcon: state => state.WeatherIcon,

    MapProviderUrl: (state) => {
        const lang = state.PageSetting_lang;
        let url = state.MapProviderUrl;
        const a = document.createElement('a');
        a.href = url;
        const query = string.getJsonFromUrl(a.search);
        query.lang = lang.toLowerCase();
        a.search = string.object2QueryStr(query);
        url = a.href;
        url = url.replace(/%7B/ig, '{');
        url = url.replace(/%7D/ig, '}');
        console.log(url);

        return url;
    },

    ShareUrlInfo: state => state.ShareUrlInfo,
};