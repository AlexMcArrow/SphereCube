LoadedMETAS['meta-user'] = {
    name: 'meta-user',
    data: function() {
        return {}
    },
    props: {
        data: {
            type: Object
        }
    },
    template: '<span class="metauser">by <b class="bold">{{ data.user_name }}</b></span>',
};