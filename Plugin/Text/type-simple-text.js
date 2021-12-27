LoadedTYPES['type-simple-text'] = {
    name: 'type-simple-text',
    data: function () {
        return {}
    },
    props: {
        data: {
            type: Object
        }
    },
    template: `<div class="type">
                <dl>
                    <dt>{{ data.name }}</dt>
                    <dd>{{ data.value }}</dd>
                </dl>
                <span class="meta">
                    <component :is="comp" v-for="(comp, ckey) in $root.METAS" :key="data.cfvid" :data="data" />
                </span>
                </div>`
};
