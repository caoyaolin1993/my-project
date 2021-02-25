import axios from "axios";
import { MessageBox, Message } from "element-ui";
import store from "@/store";
import { getToken } from "@/utils/auth";

// create an axios instance
const service = axios.create({
  baseURL:
    process.env.NODE_ENV === "production"
      ? process.env.VUE_APP_BASE_API
      : "/admin", // url = base url + request url
  headers: {
    "Content-Type": "application/json;charset=utf-8"
  },
  //   withCredentials: true, // send cookies when cross-domain requests
  // withCredentials的情况下，后端要设置Access-Control-Allow-Origin为你的源地址，例如http://localhost:8080，不能是*，而且还要设置header('Access-Control-Allow-Credentials: true');
  timeout: 50000 // request timeout
});

// request interceptor
service.interceptors.request.use(
  config => {
    // do something before request is sent

    if (store.getters.token) {
      // let each request carry token
      // ['X-Token'] is a custom headers key
      // please modify it according to the actual situation
      config.headers["token"] = getToken();
    }
    return config;
  },
  error => {
    // do something with request error
    console.log(error); // for debug
    return Promise.reject(error);
  }
);

// response interceptor
service.interceptors.response.use(
  /**
   * If you want to get http information such as headers or status
   * Please return  response => response
   */

  /**
   * Determine the request status by custom code
   * Here is just an example
   * You can also judge the status by HTTP Status Code
   */
  response => {
    //   这里的响应体可以看到响应体的所有有关信息（包括请求头和浏览器信息）
    const res = response.data;
    console.log("响应体", response);
    // if the custom code is not 20000, it is judged as an error.对响应体进行拦截
    if (res.code != 200) {
      //注意状态码的数据类型（是否是数字类型）本项目是返回字符串类型，所以用'!=' 不用验证类型，数字相同就可以
      if (res.code == -200) {
        setTimeout(function() {
          store.dispatch("user/logout").then(() => {
            // location.reload()
          });
        }, 1000);
      }
      if (res.size && res.type) {
        //有这两个属性 就为blob 类型数据，就返回blob数据
        return res;
      }
      Message({
        message: res.msg || "Error",
        type: "error",
        duration: 5 * 1000
      });

      // 50008: Illegal token; 50012: Other clients logged in; 50014: Token expired;
      //   if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
      //     // to re-login
      //     MessageBox.confirm('You have been logged out, you can cancel to stay on this page, or log in again', 'Confirm logout', {
      //       confirmButtonText: 'Re-Login',
      //       cancelButtonText: 'Cancel',
      //       type: 'warning'
      //     }).then(() => {
      //       store.dispatch('user/resetToken').then(() => {
      //         location.reload()
      //       })
      //     })
      //   }
      return Promise.reject(new Error(res.message || "Error"));
    } else {
      return res;
    }
  },
  error => {
    console.log("err" + error); // for debug
    Message({
      message: error.message,
      type: "error",
      duration: 5 * 1000
    });
    return Promise.reject(error);
  }
);

export default service;
