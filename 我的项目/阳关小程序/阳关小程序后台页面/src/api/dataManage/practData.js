import request from '@/utils/request'
export function relaxSta(data) { //放松训练统计
	return request({
		url: '/admin/Relax/relaxSta',
		method: 'post',
		data
	})
}
export function excel_relaxSta(data) { //放松训练统计  导出
	return request({
		url: '/admin/Relax/excel_relaxSta',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function relaxDet(data) { //放松详情
	return request({
		url: '/admin/Relax/relaxDet',
		method: 'post',
		data
	})
}
export function excel_relaxDet(data) { //放松详情  导出
	return request({
		url: '/admin/Relax/excel_relaxDet',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function get_problem_list(data) { //S1-问题清单
	return request({
		url: '/admin/Exercise/problem_list',
		method: 'post',
		data
	})
}
export function excel_problem_list(data) { //S1-问题清单  导出
	return request({
		url: '/admin/Exercise/excel_problem_list',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function pleasure_event_list(data) { //愉快事件记录表
	return request({
		url: '/admin/Exercise/pleasure_event_list',
		method: 'post',
		data
	})
}
export function excel_pleasure_event_list(data) { //愉快事件记录表 导出
	return request({
		url: '/admin/Exercise/excel_pleasure_event_list',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function target_list(data) { //S2-目标清单
	return request({
		url: '/admin/Exercise/target_list',
		method: 'post',
		data
	})
}
export function excel_target_list(data) { //S2-目标清单  导出
	return request({
		url: '/admin/Exercise/excel_target_list',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function activity_record(data) { //S2-活动记录
	return request({
		url: '/admin/Exercise/activity_record',
		method: 'post',
		data
	})
}
export function excel_activity_record(data) { //S2-活动记录  导出
	return request({
		url: '/admin/Exercise/excel_activity_record',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function s3_activity_record(data) { //S3-活动记录
	return request({
		url: '/admin/Exercise/s3_activity_record',
		method: 'post',
		data
	})
}
export function excel_s3_activity_record(data) { //S3-活动记录  导出
	return request({
		url: '/admin/Exercise/excel_s3_activity_record',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function auto_think(data) { //S2-自动思维记录表
	return request({
		url: '/admin/Exercise/auto_think',
		method: 'post',
		data
	})
}
export function excel_auto_think(data) { //S2-自动思维记录表  导出
	return request({
		url: '/admin/Exercise/excel_auto_think',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function auto_think_s3(data) { //S3-自动思维记录表
	return request({
		url: '/admin/Exercise/auto_think_s3',
		method: 'post',
		data
	})
}
export function excel_auto_think_s3(data) { //S3-自动思维记录表  导出
	return request({
		url: '/admin/Exercise/excel_auto_think_s3',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function activity_record_answer(data) { //S3-一周回顾
	return request({
		url: '/admin/Exercise/activity_record_answer',
		method: 'post',
		data,
	})
}

export function excel_activity_record_answer(data) { //S3-一周回顾  导出
	return request({
		url: '/admin/Exercise/excel_activity_record_answer',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function activity_keys(data) { //S3-活动宝箱
	return request({
		url: '/admin/Exercise/activity_keys',
		method: 'post',
		data,
	})
}

export function excel_activity_keys(data) { //导出S3-活动宝箱
	return request({
		url: '/admin/Exercise/excel_activity_keys',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function activity_arrange(data) { //S3-活动安排
	return request({
		url: '/admin/Exercise/activity_arrange',
		method: 'post',
		data,
	})
}

export function excel_activity_arrange(data) { //导出S3-活动安排
	return request({
		url: '/admin/Exercise/excel_activity_arrange',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function identify_myth(data) { //S3-识别误区
	return request({
		url: '/admin/Exercise/identify_myth',
		method: 'post',
		data,
	})
}

export function excel_identify_myth(data) { //导出S3-识别误区
	return request({
		url: '/admin/Exercise/excel_identify_myth',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function myth_proportion(data) { //S3误区比例
	return request({
		url: '/admin/Exercise/myth_proportion',
		method: 'post',
		data,
	})
}

export function excel_myth_proportion(data) { //导出S3误区比例
	return request({
		url: '/admin/Exercise/excel_myth_proportion',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function taskDecomposition(data) { //S4任务分解
	return request({
		url: '/admin/S4Practice/taskDecomposition',
		method: 'post',
		data,
	})
}

export function excel_taskDecomposition(data) { //导出S4任务分解
	return request({
		url: '/admin/S4Practice/excel_taskDecomposition',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function s4_activity_arrange(data) { //S4活动安排
	return request({
		url: '/admin/S4Practice/s4_activity_arrange',
		method: 'post',
		data,
	})
}

export function excel_s4_activity_arrange(data) { //导出S4活动安排
	return request({
		url: '/admin/S4Practice/excel_s4_activity_arrange',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function s4_activity_record(data) { //S4活动记录
	return request({
		url: '/admin/S4Practice/s4_activity_record',
		method: 'post',
		data,
	})
}

export function excel_s4_activity_record(data) { //导出S4活动记录
	return request({
		url: '/admin/S4Practice/excel_s4_activity_record',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}


export function auto_think_s4(data) { //S4自动思维记录表
	return request({
		url: '/admin/S4Practice/auto_think_s4',
		method: 'post',
		data,
	})
}

export function excel_auto_think_s4(data) { //导出S4自动思维记录表
	return request({
		url: '/admin/S4Practice/excel_auto_think_s4',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function attributionPractice(data) { //S5归因练习表
	return request({
		url: '/admin/S5Practice/attributionPractice',
		method: 'post',
		data,
	})
}

export function excel_attributionPractice(data) { //导出S5归因练习表
	return request({
		url: '/admin/S5Practice/excel_attributionPractice',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function problemSolving(data) { //S5问题解决表
	return request({
		url: '/admin/S5Practice/problemSolving',
		method: 'post',
		data,
	})
}

export function excel_problemSolving(data) { //导出S5问题解决表
	return request({
		url: '/admin/S5Practice/excel_problemSolving',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function s5_activity_arrange(data) { //S5活动安排表
	return request({
		url: '/admin/S5Practice/s5_activity_arrange',
		method: 'post',
		data,
	})
}

export function excel_s5_activity_arrange(data) { //导出S5活动安排表
	return request({
		url: '/admin/S5Practice/excel_s5_activity_arrange',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function s5_activity_record(data) { //S5活动记录表
	return request({
		url: '/admin/S5Practice/s5_activity_record',
		method: 'post',
		data,
	})
}

export function excel_s5_activity_record(data) { //导出S5活动记录表
	return request({
		url: '/admin/S5Practice/excel_s5_activity_record',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function s6_activity_arrange(data) { //S6活动安排表
	return request({
		url: '/admin/S6Practice/s6_activity_arrange',
		method: 'post',
		data,
	})
}

export function excel_s6_activity_arrange(data) { //导出S6活动安排表
	return request({
		url: '/admin/S6Practice/excel_s6_activity_arrange',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function s6_activity_record(data) { //S6活动记录表
	return request({
		url: '/admin/S6Practice/s6_activity_record',
		method: 'post',
		data,
	})
}

export function excel_s6_activity_record(data) { //导出S6活动记录表
	return request({
		url: '/admin/S6Practice/excel_s6_activity_record',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function s6_found_faith(data) { //S6发现内在信念表
	return request({
		url: '/admin/S6Practice/s6_found_faith',
		method: 'post',
		data,
	})
}

export function excel_s6_found_faith(data) { //导出S6发现内在信念表
	return request({
		url: '/admin/S6Practice/excel_s6_found_faith',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function assess_faith(data) { //S6发现内在信念表
	return request({
		url: '/admin/S6Practice/assess_faith',
		method: 'post',
		data,
	})
}

export function excel_assess_faith(data) { //导出S6发现内在信念表
	return request({
		url: '/admin/S6Practice/excel_assess_faith',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function method_mastery(data) { //S7方法掌握程度
	return request({
		url: '/admin/S7Practice/method_mastery',
		method: 'post',
		data,
	})
}

export function excel_method_mastery(data) { //导出S7方法掌握程度
	return request({
		url: '/admin/S7Practice/excel_method_mastery',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}

export function new_target(data) { //S7我的新目标
	return request({
		url: '/admin/S7Practice/new_target',
		method: 'post',
		data,
	})
}

export function excel_new_target(data) { //导出S7我的新目标
	return request({
		url: '/admin/S7Practice/excel_new_target',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}