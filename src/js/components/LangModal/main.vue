<template>
    <div id="LangModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>{{ $t('LangModal.ChooseLanguage') }}</p>
                    <div>
                        <template v-for="langKey in LangKeys">
                            <button
                                :key="langKey"
                                type="button"
                                class="btn btn-block"
                                :class="{
                                    'btn-primary': langKey === CurrentLang,
                                    'btn-secondary': (langKey !== CurrentLang),
                                }"
                                @click="chooseLang(langKey)"
                            >
                                <template v-if="langKey !== CurrentLang && langKey === SuggestLang">
                                    [{{ $t('LangModal.SuggestLanguage') }}]
                                </template>
                                {{ langKey }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import { localStorage } from 'lib/common/util';
import messages from 'i18n/messages/index';

const LangKeys = Object.keys(messages);

// import $ from 'jquery';
// import 'bootstrap';

// import 'app';
// import { string, jsVars, popup, trackJS, localStorage, ppPanel } from 'lib/common/util';

export default {
    components: {},
    filters: {},
    props: {
        trigger: {
            type: Number,
            default: 0,
        },
    },
    data(){
        return {
            LangKeys,
            buttonClass: [
                'btn-primary',
                'btn-secondary',
                'btn-success',
                'btn-danger',
                'btn-warning',
                'btn-info',
                'btn-dark',
            ],
        };
    },
    computed: {
        CurrentLang(){
            return this.$i18n.locale;
        },
        SuggestLang(){
            return window.navigator.language;
        },
    },
    watch: {
        trigger: {
            handler(){
                $(this.$el).modal('show');
            },
        },
    },
    created(){},
    mounted(){
        $(this.$el).modal('show');
    },
    updated(){},
    destroyed(){},
    methods: {
        ...mapActions({}),
        ...mapMutations({}),
        chooseLang(langKey){
            localStorage.set('choose-lang', langKey);
            this.$i18n.locale = langKey;
            $(this.$el).modal('hide');
        },
    },
};
</script>
<style lang="scss" scoped>
</style>