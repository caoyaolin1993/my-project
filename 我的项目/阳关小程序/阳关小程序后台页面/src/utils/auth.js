import Cookies from 'js-cookie'

const TokenKey = 'lk_token'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}

export function removeToken() {
  return Cookies.remove(TokenKey)
}

// 自定义cookie操作
export function getCookie(key) {
  return Cookies.get(key)
}
export function setCookie(key,val) {
  return Cookies.set(key, val)
}
export function removeCookie(key) {
  return Cookies.remove(key)
}
