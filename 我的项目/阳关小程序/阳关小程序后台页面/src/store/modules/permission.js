import { asyncRouterMap, constantRouterMap } from '@/router'

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param routes asyncRouterMap 侧边栏路由表
 * @param roles 用户权限表
 */
function filterAsyncRouter(routes, roles) {
	// 学习filterAsyncRouter方法遍历asyncRouterMap动态路由，如果角色权限表有这个字段，就添加一个路由。
	const res = []
	console.log('遍历的路由', routes);

	// 遍历动态路由拿到每个子路由
	routes.forEach((route, index) => {
    console.log("filterAsyncRouter -> index", index)
		const tmp = { ...route }
		// 不用递归，循环到第二层就行了（太难了）
		if(index < routes.length-1){ 
			if(roles[index].exist == "1"){
				console.log("filterAsyncRouter -> roles[index]", roles[index])
				//最外层添加进新数组
				res.push(tmp); 
				// 清空嵌套的子路由
				res[index].children = [];
				// 遍历子路由，对应的字段exsit为1，就添加
				const route_Child = route.children
				const roles_Child = roles[index].info;
				route_Child.forEach((routeC_item, routeC_index)=>{
					if(roles_Child[routeC_index]) {  //如果能取到值就进行操作
						if(roles_Child[routeC_index].exist == "1"){
							res[index].children.push(routeC_item);
						}
						// const flag =  route_Child.indexOf(routeC_item);//找出改变后的下标
                        // console.log("filterAsyncRouter -> flag", flag)
						// if(flag > -1){
						// 	route_Child.splice(flag, 1)
						// }
						// delete route_Child[routeC_index]  //删除的元素有占位，路由遍历path会有问题
					}else{ //取不到值的代表此路由不受权限控制
						res[index].children.push(routeC_item);
					}
				})
			}else{
				res.push('') //给空字符串占位，后面再过滤掉
			}
		}else{ //路由最后有一个重定向404页面路由不用对比,直接添加进去
			res.push(tmp)
		}
	})
	// 删除空项
	var newRes = res.filter(function(res_c){
		return res_c != "";  //过滤掉空字符串的新数组
	  });
	return newRes
}

const permission = {
	state: {
		routers: constantRouterMap,
		addRouters: []
	},
	mutations: {
		// 设置动态路由
		SET_ROUTERS: (state, routers) => {
			state.addRouters = routers
			// 添加动态路由
			state.routers = constantRouterMap.concat(routers)
		}
	},
	actions: {
		// 获取用户信息后调用此action对比权限表，生成动态路由
		GenerateRoutes({ commit }, data) {
			return new Promise(resolve => {
				const { roles } = data
				let accessedRouters; //根据角色权限匹配完之后的路由表
				// console.log('匹配前路由表', asyncRouterMap)
				console.log('角色权限表', roles)
				// 学习filterAsyncRouter方法遍历asyncRouterMap动态路由，如果角色权限表有这个字段，就添加一个路由。
				accessedRouters = filterAsyncRouter(asyncRouterMap, roles);

				console.log('过滤后的路由表', accessedRouters);
				// console.log('过滤后',accessedRouters)
				//这里不是增加到了constantRouterMap静态路由中，而是增加到了vuex的state.permission.routers,然后通过辅助函数映射到计算属性渲染到页面
				commit('SET_ROUTERS', accessedRouters)
				resolve()
			})
		}
	}
}

export default permission
