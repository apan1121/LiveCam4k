export default {
    PageSetting_width: 0,
    PageSetting_scrollTop: 0,
    PageSetting_mode_type: 'pc',
    PageSetting_device: '',
    PageSetting_browser: {},

    CurrentPosition: false,

    LiveCamListFlag: false,
    LiveCamList: {},

    SettingUnits: {
        temperature: 'C', //C, K, F
        length: 'Metric', // Imperial, Metric
    },

    // MapProviderUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    MapProviderUrl: 'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
    // MapProviderUrl: 'https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png',
    // MapProviderUrl: 'https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png',
    // MapProviderUrl: 'https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png',
    // MapProviderUrl: 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibGl2ZWNhbTRrIiwiYSI6ImNrc2FuN2JnODA5cnEyd3MyanZtbmJldGoifQ.Wsh6Un3PV_8eUA7EgecozA',
    // MapProviderUrl: 'http://a.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
    // MapProviderUrl: 'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',
    // MapProviderUrl: 'https://cartocdn_{s}.global.ssl.fastly.net/base-midnight/{z}/{x}/{y}.png',

    WeatherIcon: {
        '01d': 'icon icon-sun',
        '01n': 'icon icon-sun',

        '02d': 'icon icon-cloud-sun',
        '02n': 'icon icon-cloud-sun',

        '03d': 'icon icon-cloud',
        '03n': 'icon icon-cloud',

        '04d': 'icon icon-cloud',
        '04n': 'icon icon-cloud',

        '09d': 'icon icon-shower-rain',
        '09n': 'icon icon-shower-rain',

        '10d': 'icon icon-rain',
        '10n': 'icon icon-rain',

        '11d': 'icon icon-thunderstorm',
        '11n': 'icon icon-thunderstorm',

        '13d': 'icon icon-snow',
        '13n': 'icon icon-snow',

        '50d': 'icon icon-mist',
        '50n': 'icon icon-mist',
    },

    ShareUrlInfo: false,
};