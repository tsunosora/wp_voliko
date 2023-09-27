import Vue from 'vue'
import Router from 'vue-router'
import Modules from '../pages/modules.vue'
import ModuleFields from '../components/module-fields.vue'
import Settings from '../pages/settings.vue'

Vue.use(Router)

export default new Router({
    routes: [
        // {
        //   path: '/',
        //   name: 'Dashboard',
        //   component: Dashboard
        // },
        {
            path: '/',
            name: 'Modules',
            component: Modules
        },
        {
            path: '/settings',
            name: 'Settings',
            component: Settings
        },
        {
            path: '/settings/:module',
            component: ModuleFields
        }
    ]
})
