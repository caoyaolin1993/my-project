import request from '@/utils/request'
export function get_healthData(data) { //信息健康
	return request({
		url: '/admin/Sheet/health',
		method: 'post',
		data
	})
}
export function excel_health(data) { //导出信息健康
	return request({
		url: '/admin/Sheet/excel_health',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function get_course_befData(data) { //课前问卷
	return request({
		url: '/admin/Sheet/course_before',
		method: 'post',
		data
	})
}
export function excel_course_before(data) { //导出课前问卷
	return request({
		url: '/admin/Sheet/excel_course_before',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function get_moodData(data) { //心情记录
	return request({
		url: '/admin/Sheet/mood',
		method: 'post',
		data
	})
}
export function excel_mood(data) { //导出心情记录
	return request({
		url: '/admin/Sheet/excel_mood',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function get_feedbackData(data) { //心情记录
	return request({
		url: '/admin/Sheet/feedback',
		method: 'post',
		data
	})
}
export function excel_feedback(data) { //导出心情记录
	return request({
		url: '/admin/Sheet/excel_feedback',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}