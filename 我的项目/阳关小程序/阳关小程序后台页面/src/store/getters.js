const getters = {
  permission_routers: state => state.permission.routers,
  addRouters: state => state.permission.addRouters,
  
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  token: state => state.user.token,
  name: state => state.user.name,
  id: state => state.user.id,
  avatar: state => state.user.avatar,
  roles: state => state.user.roles,
  auth: state => state.user.auth,
}
export default getters
