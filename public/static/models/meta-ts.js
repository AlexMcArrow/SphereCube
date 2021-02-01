LoadedMODELS['meta-ts'] = {
    name: 'meta-ts',
    data: function() {
        return {}
    },
    props: {
        ts: {
            type: Number
        }
    },
    computed: {
        moment: function() {
            return moment.unix(this.ts).format('llll');
        },
        fromnow: function() {
            return moment.unix(this.ts).fromNow();
        }
    },
    template: '<div class="ts" :title="moment">{{ fromnow }}</div>',
};