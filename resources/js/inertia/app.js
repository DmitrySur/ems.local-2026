import '@tabler/core/dist/css/tabler.min.css'
import 'ant-design-vue/dist/reset.css'
import '../../css/inertia/ant-tabler-fix.css'
import '@tabler/core/dist/js/tabler.min.js'

import {createApp, h} from 'vue'
import {createInertiaApp, Head, Link} from '@inertiajs/vue3'
import Antd from 'ant-design-vue'
import {ZiggyVue} from 'ziggy-js'

import TablerLayout from "@/Layouts/TablerLayout.vue";


createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        const page = await pages[`./Pages/${name}.vue`]()

        page.default.layout = page.default.layout || TablerLayout

        return page.default
    },

    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(Antd)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el)
    },
})
