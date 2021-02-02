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
            return (this.data.ts) ? moment.unix(this.data.ts).format('llll') : '';
        },
        fromnow: function() {
            return (this.data.ts) ? moment.unix(this.data.ts).fromNow() : '';
        }
    },
    template: '<span class="metats" :title="moment">{{ fromnow }}</span>',
};