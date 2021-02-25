import { login, logout, getInfo } from '@/api/user'
import { getToken, setToken, removeToken, setCookie, removeCookie } from '@/utils/auth'
import { resetRouter } from '@/router'

const state = {
  token: getToken(),
  name: '',
  id: '',
  avatar: '',
  roles: [],
  auth: ''
}

const mutations = {
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_ID: (state, id) => {
    state.id = id
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles
  },
  SET_AUTH: (state, auth) => {
    state.auth = auth
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
	  console.log(userInfo);
	  
    const { account, password } = userInfo
    return new Promise((resolve, reject) => {
      login({ account: account.trim(), password: password }).then(response => {
        console.log('登录响应', response)
        const { res } = response
        commit('SET_TOKEN', res.token)
        commit('SET_NAME', res.nickname)
        commit('SET_AUTH', res.auth)
        setToken(res.token)
        setCookie('admin_n', res.nickname)
        setCookie('admin_id', res.admin_id)
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // get user info
  getUserInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo({token: state.token}).then(response => {
        console.log('getUserInfo', response)
        const { data } = response
        if (!data) {
          reject('验证失败，请重新登录！')
        }
        commit('SET_ROLES', data)
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state }) {
    return new Promise((resolve, reject) => {
      logout(state.token).then(() => {
        commit('SET_TOKEN', '')
        commit('SET_NAME', '')
		commit('SET_ROLES', [])
        removeToken()
        removeCookie('admin_n')
        removeCookie('admin_id')
        resetRouter() 
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '')
	  commit('SET_NAME', '')
	  commit('SET_ROLES', [])
      removeToken()
	  removeCookie('admin_n')
	  removeCookie('admin_id')
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

