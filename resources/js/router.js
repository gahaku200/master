import Vue from 'vue'
import Router from 'vue-router'
import Home from './components/page/Home'
import ResultOfAttendance from './components/page/ResultOfAttendance'
import Register from './components/page/Register'
import Login from './components/page/Login'
import Group from './components/admin/CreateGroup'
import InviteMember from './components/admin/InviteMember'
import AlreadyJoinGroup from './components/mail/AlreadyJoinGroup'
import NotInviteData from './components/mail/NotInviteData'
import SuccessInvite from './components/mail/SuccessInvite'
import SuccessSendEmail from './components/mail/SuccessSendEmail'
import GroupMember from './components/admin/GroupMember'
import KindOfTasks from './components/admin/KindOfTasks'
import ShowUser from './components/page/ShowUser'
import AttendanceDetail from './components/page/AttendanceDetail'
import TaskDetail from './components/page/TaskDetail'
import MemberAttendance from './components/admin/MemberAttendance'
import MemberAttendanceDetail from './components/admin/MemberAttendanceDetail'
import MemberTask from './components/admin/MemberTask'

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
      path: '/resultOfAttendance',
      name: 'resultOfAttendance',
      component: ResultOfAttendance
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
    {
      path: '/showUser',
      name: 'showUser',
      component: ShowUser
    },
    {
      path: '/attendanceDetail',
      name: 'attendanceDetail',
      component: AttendanceDetail
    },
    {
      path: '/taskDetail',
      name: 'taskDetail',
      component: TaskDetail
    },
    {
      path: '/memberAttendance',
      name: 'memberAttendance',
      component: MemberAttendance
    },
    {
      path: '/memberAttendanceDetail',
      name: 'memberAttendanceDetail',
      component: MemberAttendanceDetail
    },
    {
      path: '/memberTask',
      name: 'memberTask',
      component: MemberTask
    },
  ]
})

export default router