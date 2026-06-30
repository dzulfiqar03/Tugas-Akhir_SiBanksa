import '../css/app.css';
import './bootstrap';
import 'v-calendar/dist/style.css';
import FormWrapper from '@/Components/FormWrapper.vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import axios from 'axios';
import $ from 'jquery';
window.$ = window.jQuery = $;

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // Kirim event ke Vue
            window.dispatchEvent(new Event('session-expired'))
        }
        return Promise.reject(error)
    }
)

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        // Ambil semua file .vue dari kedua folder
        const pages = import.meta.glob('./Pages/**/*.vue');
        const errors = import.meta.glob('./Errors/**/*.vue');

        // Cari di folder Pages dulu, kalau tidak ada cari di folder Errors
        const page = pages[`./Pages/${name}.vue`] || errors[`./${name}.vue`];

        if (!page) {
            console.error(`Gagal menemukan komponen: ${name}`);
        }

        return (await page()).default
    },
    setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, Ziggy) // Ziggy masuk sini
        .component('FormWrapper', FormWrapper) // Komponen global masuk sini
        .mount(el);
},

    progress: {
        color: '#4B5563',
    },
});
