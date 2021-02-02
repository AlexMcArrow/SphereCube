LoadedMODELS['meta-parent'] = {
    name: 'meta-parent',
    data: function() {
        return {}
    },
    props: {
        data: {
            type: Object
        }
    },
    template: `<div class="metaparent" v-if="data.parent">
                    <div v-for="(value, key) in data.parent" :cid="value.cid" @click="$root.opencard(value.cid)" class="link">{{ value.name }}</div>
                </div>`,
};