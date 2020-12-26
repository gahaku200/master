import Vue from 'vue'
import Router from 'vue-router'
import Home from './components/page/Home'
import Schedule from './components/page/Schedule'
import Register from './components/page/Register'
import Login from './components/page/Login'
import Group from './components/page/CreateGroup'
import InviteMember from './components/page/InviteMember'
import AlreadyJoinGroup from './components/mail/AlreadyJoinGroup'
import NotInviteData from './components/mail/NotInviteData'
import SuccessInvite from './components/mail/SuccessInvite'
import SuccessSendEmail from './components/mail/SuccessSendEmail'
import GroupMember from './components/page/GroupMember'
import KindOfTasks from './components/page/KindOfTasks'

Vue.use(Router)

const router = new Router({
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
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/group',
      name: 'group',
      component: Group
    },
    {
      path: '/inviteMember',
      name: 'inviteMember',
      component: InviteMember
    },
    {
      path: '/successSendEmail',
      name: 'successSendEmail',
      component: SuccessSendEmail
    },
    {
      path: '/alreadyJoinGroup',
      name: 'alreadyJoinGroup',
      component: AlreadyJoinGroup
    },
    {
      path: '/notInviteData',
      name: 'notInviteData',
      component: NotInviteData
    },
    {
      path: '/successInvite',
      name: 'successInvite',
      component: SuccessInvite
    },
    {
      path: '/groupMember',
      name: 'groupMember',
      component: GroupMember
    },
    {
      path: '/kindOfTasks',
      name: 'kindOfTasks',
      component: KindOfTasks
    },
  ]
})

export default router