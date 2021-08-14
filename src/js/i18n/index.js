import Vue from 'vue';
import VueI18n from 'vue-i18n';
import { localStorage } from 'lib/common/util';
import messages from './messages/index';

Vue.use(VueI18n);

let locale = localStorage.get('choose-lang', '');

if (!locale && !!window.navigator && !!window.navigator.language) {
    locale = window.navigator.language;
}

if (!messages[locale]) {
    locale = 'en-US';
}

// Create VueI18n instance with options
const i18n = new VueI18n({
    locale,
    fallbackLocale: 'en-US',
    messages, // set locale messages
});

export default i18n;