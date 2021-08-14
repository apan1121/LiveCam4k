export default {
    props: {
    },
    data(){
        return {
        };
    },
    computed: {},
    methods: {
        setPageTitle(title){
            const WebTitle = this.$t('WebTitle');
            window.document.title = `${title} - ${WebTitle}`;
        },
    },
    beforeDestroyed(){
    },
    destroyed(){},
    mounted(){
    },
};