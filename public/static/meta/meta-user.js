LoadedMODELS['meta-user'] = {
    name: 'meta-user',
    data: function() {
        return {}
    },
    props: {
        data: {
            type: Object
        }
    },
    template: '<div class="metauser">{{ data.user_name }}</div>',
};