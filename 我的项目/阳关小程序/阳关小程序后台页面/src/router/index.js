import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

/* Layout */
import Layout from "@/layout";

/**
 * Note: sub-menu only appear when route children.length >= 1
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * redirect: 'noredirect'         当设置 noredirect 的时候该路由在面包屑导航中不可被点击
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRouterMap
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
// 所有权限通用路由表
// 如首页和登录页和一些不用权限的公用页面
export const constantRouterMap = [
  {
    path: "/login",
    component: () => import("@/views/login/index"),
    hidden: true
  },

  {
    path: "/404",
    component: () => import("@/views/404"),
    hidden: true
  },

  {
    path: "/",
    component: Layout,
    redirect: "/dashboard", //首页重定向到想要的页面
    children: [
      {
        path: "dashboard",
        name: "Dashboard",
        component: () => import("@/views/dashboard/index"),
        meta: { title: "首页", icon: "home" }
      }
    ]
  }
];

// 路由表匹配后动态挂载
export const asyncRouterMap = [
  {
    path: "/users",
    component: Layout,
    redirect: "/users/patiCode",
    name: "users",
    meta: { title: "用户", icon: "users" },
    children: [
      {
        path: "patiCode",
        name: "PatiCode",
        component: () => import("@/views/users/patiCode/index"),
        meta: { title: "患者编码", icon: "" }
      },
      {
        path: "userManage",
        name: "UserManage",
        component: () => import("@/views/users/userManage/index"),
        meta: { title: "用户管理", icon: "" }
      }
    ]
  },
  {
    path: "/dataManage",
    component: Layout,
    redirect: "/dataManage/quesData",
    name: "DataManage",
    meta: {
      title: "数据",
      icon: "dataManage"
    },
    children: [
      {
        path: "quesData",
        name: "QuesData",
        component: () => import("@/views/dataManage/quesData/index"), // Parent router-view
        meta: { title: "问卷数据", icon: "" }
      },
      {
        path: "courseData",
        name: "CourseData",
        component: () => import("@/views/dataManage/courseData/index"),
        meta: { title: "课程数据", icon: "" }
      },
      {
        path: "practiceData",
        name: "PracticeData",
        component: () => import("@/views/dataManage/practiceData/index"),
        redirect: "/dataManage/practiceData/1",
        meta: { title: "练习数据", icon: "" },
        children: [
          {
            path: "1",
            name: "1",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType1/index"), // Parent router-view
            meta: { title: "放松训练统计" },
            hidden: true
          },
          {
            path: "2",
            name: "2",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType2/index"),
            meta: { title: "放松训练详情" },
            hidden: true
          },
          {
            path: "1-1",
            name: "1-1",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType1-1/index"),
            meta: { title: "S1-问题清单" },
            hidden: true
          },
          {
            path: "1-2",
            name: "1-2",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType1-2/index"),
            meta: { title: "S1-愉快事件记录表" },
            hidden: true
          },
          {
            path: "2-1",
            name: "2-1",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType2-1/index"),
            meta: { title: "S2-目标清单" },
            hidden: true
          },
          {
            path: "2-2",
            name: "2-2",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType2-2/index"),
            meta: { title: "S2-活动记录表" },
            hidden: true
          },
          {
            path: "2-3",
            name: "2-3",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType2-3/index"),
            meta: { title: "S2-自动思维记录表" },
            hidden: true
          },
          {
            path: "3-1",
            name: "3-1",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-1/index"),
            meta: { title: "S3-一周回顾" },
            hidden: true
          },
          {
            path: "3-2",
            name: "3-2",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-2/index"),
            meta: { title: "S3-活动宝箱" },
            hidden: true
          },
          {
            path: "3-3",
            name: "3-3",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-3/index"),
            meta: { title: "S3-活动安排" },
            hidden: true
          },
          {
            path: "3-4",
            name: "3-4",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-4/index"),
            meta: { title: "S3-识别误区" },
            hidden: true
          },
          {
            path: "3-5",
            name: "3-5",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-5/index"),
            meta: { title: "S3-误区比例" },
            hidden: true
          },
          {
            path: "3-6",
            name: "3-6",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-6/index"),
            meta: { title: "S3-自动思维记录" },
            hidden: true
          },
          {
            path: "3-7",
            name: "3-7",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType3-7/index"),
            meta: { title: "S3-活动记录" },
            hidden: true
          },
          {
            path: "4-1",
            name: "4-1",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType4-1/index"),
            meta: { title: "S4-任务分解" },
            hidden: true
          },
          {
            path: "4-2",
            name: "4-2",
            component: () =>
              import("@/views/dataManage/practiceData/practDataType4-2/index"),
            meta: { title: "S4-活动安排" },
            hidden: true
          },
            {
          	path: '4-3',
          	name: '4-3',
						component: () => import('@/views/dataManage/practiceData/practDataType4-3/index'),
						meta: { title: "S4-活动记录表" },
            hidden: true
            },
            {
          	path: '4-4',
          	name: '4-4',
						component: () => import('@/views/dataManage/practiceData/practDataType4-4/index'),
						meta: { title: "S4-自动思维记录表" },
            hidden: true
            },
            {
          	path: '5-1',
          	name: '5-1',
            component: () => import('@/views/dataManage/practiceData/practDataType5-1/index'),
            meta: { title: "S5-归因练习" },
            hidden: true
            },
            {
          	path: '5-2',
          	name: '5-2',
            component: () => import('@/views/dataManage/practiceData/practDataType5-2/index'),
            meta: { title: "S5-问题解决" },
            hidden: true
            },
            {
          	path: '5-3',
          	name: '5-3',
            component: () => import('@/views/dataManage/practiceData/practDataType5-3/index'),
            meta: { title: "S5-活动安排" },
            hidden: true
            },
            {
          	path: '5-4',
          	name: '5-4',
            component: () => import('@/views/dataManage/practiceData/practDataType5-4/index'),
            meta: { title: "S5-活动记录" },
            hidden: true
            },
            {
          	path: '6-1',
          	name: '6-1',
            component: () => import('@/views/dataManage/practiceData/practDataType6-1/index'),
            meta: { title: "S6-活动安排表" },
            hidden: true
            },
            {
          	path: '6-2',
          	name: '6-2',
            component: () => import('@/views/dataManage/practiceData/practDataType6-2/index'),
            meta: { title: "S6-活动记录表" },
            hidden: true
            },
            {
          	path: '6-3',
          	name: '6-3',
            component: () => import('@/views/dataManage/practiceData/practDataType6-3/index'),
            meta: { title: "S6-发现内在信念表" },
            hidden: true
            },
            {
          	path: '6-4',
          	name: '6-4',
            component: () => import('@/views/dataManage/practiceData/practDataType6-4/index'),
            meta: { title: "S6-评估内在信念表" },
            hidden: true
            },
            {
          	path: '7-1',
          	name: '7-1',
            component: () => import('@/views/dataManage/practiceData/practDataType7-1/index'),
            meta: { title: "S7-方法掌握程度表" },
            hidden: true
            },
            {
          	path: '7-2',
          	name: '7-2',
            component: () => import('@/views/dataManage/practiceData/practDataType7-2/index'),
            meta: { title: "S7-我的新目标表" },
            hidden: true
            }
        ]
      }
    ]
  },
  {
    path: "/setting",
    component: Layout,
    redirect: "/setting/rolePerm",
    name: "Setting",
    meta: { title: "设置", icon: "setting" },
    children: [
      {
        path: "rolePerm",
        name: "RolePerm",
        component: () => import("@/views/setting/rolePerm"),
        meta: { title: "角色权限", icon: "" }
      },
      {
        path: "accManage",
        name: "AccManage",
        component: () => import("@/views/setting/accManage"),
        meta: { title: "账号管理", icon: "" }
      },
      {
        path: "editPassw",
        name: "EditPassw",
        component: () => import("@/views/setting/editPassw"),
        meta: { title: "修改密码", icon: "" }
      }
    ]
  },
  //   {
  //     path: '/keepOnRecord',
  //     component: Layout,
  //     children: [
  //       {
  //         path: 'index',
  //         name: 'keepOnRecord',
  //         component: () => import('@/views/keepOnRecord/index'),
  //         meta: { title: '备案信息', icon: 'keeponrecord', roles: [] }
  //       }
  //     ]
  //   },

  //   {
  // 	path: '/sharePage',
  // 	component: Layout,
  // 	children: [
  // 	  {
  // 		path: 'index',
  // 		name: 'sharePage',
  // 		component: () => import('@/views/sharePage/index'),
  // 		meta: { title: '移动端分享管理', icon: 'sharepage', roles: [] }
  // 	  }
  // 	]
  // },
  //   {
  // 	path: '/addUsers',
  // 	component: Layout,
  // 	children: [
  // 	  {
  // 		path: 'index',
  // 		name: 'addUsers',
  // 		component: () => import('@/views/addUsers/index'),
  // 		meta: { title: '账号管理', icon: 'addusers', roles: [] }
  // 	  }
  // 	]
  // },

  // 404页面必须写到最后 !!!
  // 所有未找到的页面都重定向到首页
  {
    path: "*",
    redirect: "/404",
    hidden: true
  }
];

// export default new Router({
//   // routes: constantRouterMap
//   routes: constantRouterMap // 暂时把路由全部显示出来
// })

const createRouter = () =>
  new Router({
    // mode: 'history', // require service support
    scrollBehavior: () => ({ y: 0 }),
    routes: constantRouterMap
  });

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
