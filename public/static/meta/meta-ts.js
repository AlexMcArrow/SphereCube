LoadedMODELS['meta-ts'] = {
    name: 'meta-ts',
    data: function() {
        return {}
    },
    props: {
        data: {
            type: Object
        }
    },
    computed: {
        moment: function() {
            return moment.unix(this.data.ts).format('llll');
        },
        fromnow: function() {
            return moment.unix(this.data.ts).fromNow();
        }
    },
    template: '<div class="metats" :title="moment">{{ fromnow }}</div>',
};