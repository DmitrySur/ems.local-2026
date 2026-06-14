import '@tabler/core/dist/css/tabler.min.css'
import 'ant-design-vue/dist/reset.css'
import '../../css/inertia/ant-tabler-fix.css'
import '@tabler/core/dist/js/tabler.min.js'

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import Antd, { ConfigProvider } from 'ant-design-vue'
import ruRU from 'ant-design-vue/es/locale/ru_RU'
import dayjs from 'dayjs'
import 'dayjs/locale/ru'
import { ZiggyVue } from 'ziggy-js'

import TablerLayout from '@/Layouts/TablerLayout.vue'

dayjs.locale('ru')

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        const page = await pages[`./Pages/${name}.vue`]()

        page.default.layout = page.default.layout || TablerLayout

        return page.default
    },

    setup({ el, App, props, plugin }) {
        createApp({
            render: () =>
                h(ConfigProvider, { locale: ruRU }, {
                    default: () => h(App, props),
                }),
        })
            .use(plugin)
            .use(ZiggyVue)
            .use(Antd)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el)
    },
})