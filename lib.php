<?php

function local_catalog_get_courses($keytype="sequential"){
	global $DB;
	$keytype = strtolower($keytype);
	$i=0;
	$entries = array();
	$entry_list = $DB->get_records('local_catalog', null, 'name');
	$i=0;
	foreach($entry_list as $e){
		if($keytype=="id")$key = $e->id;
		else $key = $i;
		$entries[$key]['id'] = $e->id;
		$entries[$key]['name'] = $e->name;
		$entries[$key]['preview_video_id'] = $e->preview_video_id;
		$entries[$key]['description'] = $e->description;
		$entries[$key]['thumbnail'] = $e->thumbnail;
		$entries[$key]['multi_course_label'] = $e->multi_course_label;
		$entries[$key]['enrol_course_id'] = $e->enrol_course_id;
		$entries[$key]['enrol_open'] = $e->enrol_open;
		$i++;
	}
	return $entries;
}

function local_catalog_get_sections(){
	global $DB;
	$i=0;
	$entries = array();
	$entry_list = $DB->get_records('local_catalog_sections', null, 'sequence');
	$i=0;
	foreach($entry_list as $e){
		$entries[$i]['id'] = $e->id;
		$entries[$i]['name'] = $e->name;
		$entries[$i]['tagline'] = $e->tagline;
		$entries[$i]['header'] = $e->header;
		$entries[$i]['footer'] = $e->header;
		$entries[$i]['video'] = $e->video;
		$entries[$i]['enabled'] = $e->enabled;
		$entries[$i]['sequence'] = $e->sequence;
		$i++;
	}
	return $entries;
}

function local_catalog_get_section_detail($id){
	global $DB;
	$e = $DB->get_record('local_catalog_sections', array('id'=>$id), '*', MUST_EXIST);
	$section['id'] = $e->id;
	$section['name'] = $e->name;
	$section['tagline'] = $e->tagline;
	$section['header'] = $e->header;
	$section['footer'] = $e->header;
	$section['video'] = $e->video;
	$section['enabled'] = $e->enabled;
	$section['sequence'] = $e->sequence;
	return $section;
}

function local_catalog_get_section_courses($section_id){
	global $DB;
	$all = local_catalog_get_courses("id");
	$res = $DB->get_records('local_catalog_section_course', array('catalog_section_id'=>$section_id), 'sequence');

	$return = array();
	$i=0;
	foreach($res as $r){
		$key = $i;
		$return[$key] = $all[$r->catalog_id];
		$return[$key]['local_catalog_section_course_id'] = $r->id;
		$i++;
	}
	return $return;
}

function local_catalog_get_course_tiles($section_id){
	global $OUTPUT;
	global $CFG;
	$courses = local_catalog_get_section_courses($section_id);
	$cols = 3;
	$ret = 4;
	if(count($courses)==1){$cols=12; $ret=1;}
	if(count($courses)%3==0){$cols=4; $ret = 3;}
	if(count($courses)==5){$cols = 4; $ret = 3;}
	if(count($courses)==2||count($courses)==4){$cols = 6; $ret = 2;}

	$data = new stdClass();
	$i=0;
	foreach($courses as $c){
		$cd = new stdClass();
		$cd->name = $c['name'];
		$cd->id = $c['id'];
		$cd->thumbnail = $c['thumbnail'];
		$cd->wwwroot = $CFG->wwwroot;
		$cd->section_id = $section_id;
		$data->courses[$i]['course'] = $OUTPUT->render_from_template('local_catalog/course_tile',$cd);
		if(($i+1)%$ret==0)$data->courses[$i]['return'] = true;
		$i++;
	}
	if(isset($data->courses[count($data->courses)-1]['return']))unset($data->courses[count($data->courses)-1]['return']);
	$data->wwwroot = $CFG->wwwroot;
	$data->cols = $cols;
	return $OUTPUT->render_from_template('local_catalog/course_list', $data);
}

?>