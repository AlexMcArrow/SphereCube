LoadedTYPES['type-simple-text'] = {
    name: 'type-simple-text',
    data: function() {
        return {}
    },
    props: {
        cid: {
            type: String
        },
        cfid: {
            type: String
        },
        cfvid: {
            type: String
        },
        value: {
            type: String
        }
    },
    template: '<div class="type">Type</div>',
    created: function() {
        console.log('TypeSimple: ', this);
        console.log('Load TypeSimple: ', this.cid, this.cfid, this.cfvid);
    }
};