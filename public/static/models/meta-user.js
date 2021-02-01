LoadedMODELS['meta-user'] = {
    name: 'meta-user',
    data: function() {
        return {}
    },
    props: {
        name: {
            type: String
        },
        active: {
            type: Number
        }
    },
    template: '<div class="metauser">{{ name }}</div>',
};