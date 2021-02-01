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
    template: `<div class="card">
                    <section class="meta">
                        <h1>{{ meta.c_name }} <meta-ts :ts="meta.c_ts"/></h1>
                        <meta-user :name="meta.u_user_name" :active="meta.u_active" />
                    </section>
                    <section class="fields">

                    </section>
                </div>`,
    created: function() {
        const self = this;
        console.log('Load Card: ', this.cid);
        RPCCALL('cardread', { cid: this.cid }, (res) => {
            Object.assign(self.$data, res);
        });
    }
};