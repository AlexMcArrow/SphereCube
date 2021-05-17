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
                        <table>
                            <tr>
                                <td>
                                    <span class="title">{{ meta.name }}</span>
                                </td>
                                <td>
                                <span class="control">
                                    <span @click="editcard()" title="Edit" class="pointer">
                                        <i class="fas fa-pencil-alt"></i>
                                    </span>
                                    <span @click="loadcard()" title="Refresh" class="pointer">
                                        <i class="fas fa-sync"></i>
                                    </span>
                                    <span @click="$root.closecard(cid)" title="Close" class="pointer">
                                        <i class="fas fa-times-circle"></i>
                                    </span>
                                </span>
                                </td>
                            </tr>
                        </table>
                        <span class="meta">
                            <component :is="comp" v-for="(comp, ckey) in $root.METAS" :key="ckey" :data="meta" />
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
            handle: 'section.meta table'
        }).on('staticClick', this.dragging).on('pointerDown', this.dragging).on('pointerUp', this.draggend).css('z-index', (Math.round(new Date().getTime() - Z)));
        if (localStorage.getItem('c-pos-' + this.cid)) {
            var xy = JSON.parse(localStorage.getItem('c-pos-' + this.cid));
            $(this.$el).draggabilly('setPosition', xy.left, xy.top);
        }
    },
    beforeUnmount: function() {
        $(this.$el).draggabilly('disable').draggabilly('destroy');
    },
    methods: {
        loadcard: function() {
            const self = this;
            MODELCALL('card', 'cardread', { cid: this.cid }, (res) => {
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
        },
        draggend: function(event) {
            localStorage.setItem('c-pos-' + this.cid, JSON.stringify($(this.$el).position()));
        }
    }
};