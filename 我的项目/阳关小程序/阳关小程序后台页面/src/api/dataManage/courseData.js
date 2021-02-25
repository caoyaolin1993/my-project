// 课程数据
import request from '@/utils/request'
export function course_count(data) { //课程统计
	return request({
		url: '/admin/Course/course_count',
		method: 'post',
		data
	})
}
export function excel_course_count(data) { //导出课程统计
	return request({
		url: '/admin/Course/excel_course_count',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function course_study_dist(data) { //课程学习分布
	return request({
		url: '/admin/Course/course_study_distribution',
		method: 'post',
		data
	})
}
export function excel_study_dist(data) { //导出课程学习分布
	return request({
		url: '/admin/Course/excel_course_study_distribution',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function study_count(data) { //学习统计
	return request({
		url: '/admin/Course/study_count',
		method: 'post',
		data
	})
}
export function excel_study_count(data) { //导出学习统计
	return request({
		url: '/admin/Course/excel_study_count',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function course_info(data) { //学习详情
	return request({
		url: '/admin/Course/course_info',
		method: 'post',
		data
	})
}
export function excel_course_info(data) { //导出学习详情
	return request({
		url: '/admin/Course/excel_course_info',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}