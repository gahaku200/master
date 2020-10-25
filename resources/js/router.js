import Vue from 'vue'
import Router from 'vue-router'
import Home from './components/Home'
import Schedule from './components/Schedule'

Vue.use(Router)

export default new Router({
  mode: 'history', // SPAのURLにはhistoryモード(#ハッシュが付かないタイプを使います)
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home // URL「/」に対してHomeコンポーネントを使うという意味です
    },
    {
      path: '/schedule',
      name: 'schedule',
      component: Schedule
    },
  ]
})