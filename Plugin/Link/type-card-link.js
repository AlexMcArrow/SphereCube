LoadedTYPES['type-card-link'] = {
    name: 'type-card-link',
    data: function() {
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
                    <dd @click="$root.opencard(data.link.cid)" class="link">{{ data.link.name }}</dd>
                </dl>
                <span class="meta">
                    <component :is="comp" v-for="(comp, ckey) in $root.METAS" :key="data.cfvid" :data="data" />
                </span>
                </div>`
};