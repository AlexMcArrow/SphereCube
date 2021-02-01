LoadedMODELS['card'] = {
    name: 'card',
    data: function() {
        return {
            meta: {},
            fields: {}
        }
    },
    props: {
        cid: {
            type: String
        }
    },
    template: `<div class="card" :ref="cid" :key="cid">
                    <section class="meta">
                        <h1>{{ meta.name }}</h1>
                        <span class="meta">
                            <component :is="comp" v-for="(comp, ckey) in $root.METAS" :key="ckey" :data="meta" />
                        </span>
                        <span class="control">
                            <div @click="loadcard()">[*]</div>
                            <div @click="$root.closecard(cid)">[X]</div>
                        </span>
                    </section>
                    <section class="fields">
                        <component :is="slottype(field.cfvid)" v-for="(field, key) in fields" :key="field.cfvid" :data="field" />
                    </section>
                </div>`,
    created: function() {
        this.loadcard();
    },
    mounted: function() {
        $(this.$el).draggabilly({
            containment: '#workspace',
            handle: 'section.meta'
        }).on('staticClick', this.dragging).on('pointerDown', this.dragging);
    },
    beforeUnmount: function() {
        $(this.$el).draggabilly('disable').draggabilly('destroy');
    },
    methods: {
        loadcard: function() {
            const self = this;
            RPCCALL('cardread', { cid: this.cid }, (res) => {
                Object.assign(self.$data, res);
            });
        },
        slottype: function(cfvid) {
            var tpl = this.$parent.TYPES['text'];
            if (this.fields[cfvid].cf_type in this.$parent.TYPES) {
                tpl = this.$parent.TYPES[this.fields[cfvid].cf_type];
            }
            return tpl;
        },
        dragging: function(event) {
            $(event.currentTarget).css('z-index', (Math.round(new Date().getTime() - Z)));
            //this.$forceUpdate();
        }
    }
};