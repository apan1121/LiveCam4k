export default {
    props: {
    },
    data(){
        return {
            prevRoute: null,
        };
    },
    computed: {},
    methods: {
        setPageTitle(title){
            const WebTitle = this.$t('WebTitle');
            if (!title) {
                title = WebTitle;
            } else {
                title = `${title} - ${WebTitle}`;
            }
            window.document.title = title;
        },
    },
    beforeDestroyed(){
    },
    destroyed(){},
    mounted(){
    },
    beforeRouteEnter(to, from, next){
        $('body').attr('page-name', to.name);
        next((vm) => {
            vm.prevRoute = from;
        });
    },
};