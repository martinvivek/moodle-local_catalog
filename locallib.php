<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
* Function library for courseblog module
* 
* @author  Mark Samberg
* @package local_catalog
* @copyright  2015 NC State University
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*
*-----------------------------------------------------------
*/

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');
//Metadata related functions

/**
*	Add a new category of metadata
*	@param mform data
*	@return Insert ID of new record
**/
function local_catalog_add_metadata_category($data) {
    global $DB;
    $id = $DB->insert_record('local_catalog_metadata', $data);
    return $id;
}

function local_catalog_edit_metadata_category($data){
	global $DB;
	return $DB->update_record('local_catalog_metadata', $data);
}

function local_catalog_delete_metadata_category($id) {
    global $DB;
    $count = $DB->count_records('local_catalog_course_meta', array('metadata_id'=>$id));
    if($count>0)return false;
    $result = $DB->delete_records('local_catalog_metadata', array('id'=>$id));
    return $result;
}

function local_catalog_metadata_get_datatype($id){
	global $DB;
	return $DB->get_field('local_catalog_metadata','datatype',array('id'=>$id), MUST_EXIST);
}

/**
*	Get a list of all metadata categories
*	@return array of all metadata records
**/
function local_catalog_get_metadata_categories($keytype="SEQUENTIAL"){
	global $DB;
	$entry_list = $DB->get_records('local_catalog_metadata', null, 'name');

	$entries = array();
	$i=0;
	foreach($entry_list as $e){
		if($keytype=="ID") $key = $e->id;
		else $key = $i;
		$entries[$key]['id'] = $e->id;
		$entries[$key]['name'] = $e->name;
		$entries[$key]['fa_icon'] = $e->fa_icon;
		$entries[$key]['datatype'] = $e->datatype;
		$entries[$key]['count'] = $DB->count_records('local_catalog_course_meta', array('metadata_id'=>$e->id));
		$i++;

	}
	return $entries;

}

function local_catalog_add_course_metadata($data){
	   global $DB;
	$data->sequence = $DB->count_records('local_catalog_course_meta', array('catalog_id'=>$data->catalog_id))+1;
    $id = $DB->insert_record('local_catalog_course_meta', $data);
    return $id;
}

function local_catalog_delete_course_metadata($id, $catalog_id){
	global $DB;
    $result = $DB->delete_records('local_catalog_course_meta', array('id'=>$id, 'catalog_id'=>$catalog_id));
    if($result==false)return $result;

    $meta = $DB->get_records('local_catalog_course_meta', array('catalog_id'=>$catalog_id), 'sequence');
    $i=1;
    foreach($meta as $m){
    	$obj = new StdClass();
    	$obj->id = $m->id;
    	$obj->sequence = $i;
    	$DB->update_record('local_catalog_course_meta',$obj);
    	unset($obj);
    	$i++;
    }
    return;
}

function local_catalog_move_course_metadata($direction, $id, $catalog_id){
	global $DB;
	$direction = strtolower($direction);
	if($direction!="down"&&$direction!="up")return false;

	$sequence = $DB->get_field('local_catalog_course_meta','sequence',array('id'=>$id), MUST_EXIST);	

	if($direction=="up") $newseq = $sequence - 1;
	if($direction=="down")$newseq = $sequence + 1;
	$switch = $DB->get_field('local_catalog_course_meta','id',array('catalog_id'=>$catalog_id, 'sequence'=>$newseq), MUST_EXIST);

    $obj = new StdClass();
	$obj->id = $id;
	$obj->sequence = $newseq;
	$DB->update_record('local_catalog_course_meta',$obj);

    $obj = new StdClass();
	$obj->id = $switch;
	$obj->sequence = $sequence;
	$DB->update_record('local_catalog_course_meta',$obj);

}

function local_catalog_get_course_metadata($catalog_id){
	global $DB;
	$cat = local_catalog_get_metadata_categories("ID");
	$meta = $DB->get_records('local_catalog_course_meta', array('catalog_id'=>$catalog_id), 'sequence');
	$entries = array();
	$i=0;
	foreach($meta as $m){
		$key = $i;
		$entries[$key]['id'] = $m->id;
		$entries[$key]['name'] = $cat[$m->metadata_id]['name'];
		$entries[$key]['fa_icon'] = $cat[$m->metadata_id]['fa_icon'];
		$entries[$key]['datatype'] = $cat[$m->metadata_id]['datatype'];
		$entries[$key]['url'] = $m->url;
		$entries[$key]['value'] = $m->value;
		if($cat[$m->metadata_id]['datatype']=="date")$entries[$key]['value'] = date("m/d/Y",$m->value);
		if($cat[$m->metadata_id]['datatype']=="list")$entries[$key]['value']  = explode(";", $m->value);
		$i++;
	}
	return $entries;
}

//Course pages
function local_catalog_add_page($data) {
    global $DB;
    $id = $DB->insert_record('local_catalog_pages', $data);
    return $id;
}

function local_catalog_edit_page($data){
	global $DB;
	return $DB->update_record('local_catalog_pages', $data);
}

function local_catalog_delete_page($id) {
    global $DB;
    $count = $DB->count_records('local_catalog_course_pages', array('page_id'=>$id));
    if($count>0)return false;
    $result = $DB->delete_records('local_catalog_pages', array('id'=>$id));
    return $result;
}

function local_catalog_get_pages($extended = true, $keytype="SEQUENTIAL"){
	global $DB;
	$keytype = strtolower($keytype);
	$entry_list = $DB->get_records('local_catalog_pages', null, 'name');

	$entries = array();
	$i=0;
	foreach($entry_list as $e){
		if($keytype=="id")$key = $e->id;
		else $key = $i;
		if($extended){
			$entries[$key]['id'] = $e->id;
			$entries[$key]['name'] = $e->name;
			$entries[$key]['fa_icon'] = $e->fa_icon;
			$entries[$key]['template'] = $e->template;
			$entries[$key]['count'] = $DB->count_records('local_catalog_course_pages', array('page_id'=>$e->id));
		}
		else{
			$entries[$e->id] = $e->name;
		}
		$i++;
	}
	return $entries;

}

function local_catalog_get_course_pages($catalog_id){
	global $DB;
	$list = local_catalog_get_pages(true, "id");
	$records = $DB->get_records('local_catalog_course_pages', array('catalog_id'=>$catalog_id), 'sequence');

	$pages = array();
	$i=0;
	foreach($records as $r){
		$pages[$i]['id'] = $r->id;
		$pages[$i]['page_id'] = $r->page_id;
		$pages[$i]['name'] = $list[$r->page_id]['name'];
		$pages[$i]['fa_icon'] = $list[$r->page_id]['fa_icon'];
		if($r->use_template == 1){
			$pages[$i]['use_template'] = true;
			$pages[$i]['content'] = $list[$r->page_id]['template'];
		}
		else{
			$pages[$i]['use_template'] = false;
			$pages[$i]['content'] = $r->content;
		}
		$i++;
	}
	return $pages;
}

function local_catalog_add_course_pages($data){
	global $DB;
	$data->sequence = $DB->count_records('local_catalog_course_pages', array('catalog_id'=>$data->catalog_id))+1;
    $id = $DB->insert_record('local_catalog_course_pages', $data);
    return $id;
}

function local_catalog_edit_course_page($data){
	global $DB;
	return $DB->update_record('local_catalog_course_pages', $data);
}

function local_catalog_move_course_page($direction, $id, $catalog_id){
	global $DB;
	$direction = strtolower($direction);
	if($direction!="down"&&$direction!="up")return false;

	$sequence = $DB->get_field('local_catalog_course_pages','sequence',array('id'=>$id), MUST_EXIST);	

	if($direction=="up") $newseq = $sequence - 1;
	if($direction=="down")$newseq = $sequence + 1;
	$switch = $DB->get_field('local_catalog_course_pages','id',array('catalog_id'=>$catalog_id, 'sequence'=>$newseq), MUST_EXIST);

    $obj = new StdClass();
	$obj->id = $id;
	$obj->sequence = $newseq;
	$DB->update_record('local_catalog_course_pages',$obj);

    $obj = new StdClass();
	$obj->id = $switch;
	$obj->sequence = $sequence;
	$DB->update_record('local_catalog_course_pages',$obj);

}

function local_catalog_delete_course_page($id, $catalog_id){
	global $DB;
    $result = $DB->delete_records('local_catalog_course_pages', array('id'=>$id, 'catalog_id'=>$catalog_id));
    if($result==false)return $result;

    $meta = $DB->get_records('local_catalog_course_pages', array('catalog_id'=>$catalog_id), 'sequence');
    $i=1;
    foreach($meta as $m){
    	$obj = new StdClass();
    	$obj->id = $m->id;
    	$obj->sequence = $i;
    	$DB->update_record('local_catalog_course_pages',$obj);
    	unset($obj);
    	$i++;
    }
    return;
}


//Courses
function get_course_detail($id){
	global $DB;

	$metadata = array();
	$metadata_list = $DB->get_records('local_catalog_course_meta',array('catalog_id'=>$id),'sequence');
	$i=0;
	foreach($metadata_list as $m){
		$metadata[$i]['metadata_id'] = $m->metadata_id;
		$metadata[$i]['value'] = $m->value;
		$metadata[$i]['url'] = $m->url;
		$metadata[$i]['sequence'] = $m->sequence;
		$i++;
	}

	$course = array();
	$e = $DB->get_record('local_catalog', array('id'=>$id), '*', MUST_EXIST);
		$course['id'] = $e->id;
		$course['name'] = $e->name;
		$course['subtitle'] = $e->subtitle;
		$course['preview_video_id'] = $e->preview_video_id;
		$course['description'] = $e->description;
		$course['objectives'] = $e->objectives;
		$course['thumbnail'] = $e->thumbnail;
		$course['multi_course_label'] = $e->multi_course_label;
		$course['enrol_course_id'] = $e->enrol_course_id;
		$course['enrol_open'] = $e->enrol_open;
		$course['metadata'] = $metadata;
	return $course;

}

function local_catalog_add_course($data) {
    global $DB;
    $id = $DB->insert_record('local_catalog', $data);
    return $id;
}

function local_catalog_edit_course($data){
	global $DB;
	return $DB->update_record('local_catalog', $data);
}


function local_catalog_delete_course($id){
	global $DB;
	$DB->delete_records('local_catalog_course_meta', array('catalog_id'=>$id));
	$DB->delete_records('local_catalog_section_course', array('catalog_id'=>$id));
	$DB->delete_records('local_catalog_allcourses', array('catalog_id'=>$id));
	$DB->delete_records('local_catalog', array('id'=>$id));
	return;
}

function local_catalog_get_all_enrolments(){
	global $DB;
	$enrol_list = $DB->get_records_sql("SELECT e.id as id, e.enrol as enroltype, e.name as enrolname,c.fullname as course FROM {enrol} e, {course} c WHERE c.id=e.courseid ORDER BY c.fullname, e.enrol, e.name");
	$e = array();
	foreach($enrol_list as $i){
		$e[$i->id] = $i->course;
		if(strlen($i->enrolname)==0)$e[$i->id].=": ".$i->enroltype;
		else $e[$i->id].=": ".$i->enrolname." (".$i->enroltype.")";
	}
	return $e;
}

function local_catalog_get_enrolment_url($id){
	global $DB;
	global $CFG;
	if(!is_numeric($id))return "#";
	$enrol = $DB->get_record('enrol', array('id'=>$id), '*', MUST_EXIST);
	if($enrol->enrol=="survey")return $CFG->wwwroot.'/enrol/survey/survey.php?enrolid='.$id;
	if($enrol->enrol=="guest")return $CFG->wwwroot.'/course/view.php?id='.$enrol->courseid;
	return $CFG->wwwroot.'/enrol/?id='.$enrol->courseid;
}

function get_microcredential_course(){
	global $PAGE;
	if(!isset($PAGE->theme->settings->course_microcredentials))return false;
	else return $PAGE->theme->settings->course_microcredentials;
}

function local_catalog_get_all_microcredentials($keytype="SEQUENTIAL"){
	global $CFG;
	global $DB;
	$keytype = strtolower($keytype);
	if(!get_microcredential_course())return array();
	$mc_course = get_microcredential_course();
	$records = $DB->get_records('course_sections',array('course'=>$mc_course),'section');
	$result = array();
	$i=0;
	foreach($records as $r){
		if(strlen($r->name)==0)continue;
		if($r->section==0)continue;
		if($keytype=="id")$key = $r->id;
		else $key=$i;
		$result[$key]['id'] = $r->id;
		$result[$key]['name'] = $r->name;
		$result[$key]['sequence'] = $r->section;
		$i++;
	}

	if($keytype=="assoc"){
		$assoc = array();
		foreach($result as $r){
			$assoc[$r['id']] = $r['name'];
		}
		return $assoc;
	}
	else return $result;
}

function local_catalog_add_course_mc($data){
	global $DB;
	$DB->delete_records('local_catalog_course_mcs',array('catalog_id'=>$data->catalog_id, 'section_id'=>$data->section_id));
    $id = $DB->insert_record('local_catalog_course_mcs', $data);
    return $id;
}

function local_catalog_delete_course_mc($mcid, $catalog_id){
	global $DB;
	$DB->delete_records('local_catalog_course_mcs',array('catalog_id'=>$catalog_id, 'section_id'=>$mcid));
}

function local_catalog_get_course_microcredentials($catalog_id){
	global $DB;
	$cm_list = $DB->get_records('local_catalog_course_mcs',array('catalog_id'=>$catalog_id));
	$mc_list = local_catalog_get_all_microcredentials('id');
	$result = array();
	$sort = array();
	foreach($cm_list as $c){
		$result[$c->section_id]['id'] = $mc_list[$c->section_id]['id'];
		$result[$c->section_id]['name'] = $mc_list[$c->section_id]['name'];
		$result[$c->section_id]['sequence'] =$mc_list[$c->section_id]['sequence'];
		$sort[$c->section_id] = $mc_list[$c->section_id]['sequence'];
	}
	if(count($result)==0)return array();
	asort($sort);
	$r = array();
	$i=0;
	foreach($sort as $key=>$elem){
		$r[$i] = $result[$key];
		$i++;
	}
	return $r;

}

function local_catalog_get_all_moodle_courses(){
	global $DB;
	return $DB->get_records_menu('course', null, 'fullname', 'id, fullname'); 
}

function local_catalog_add_course_edition($data){
	global $DB;
	$data->sequence = $DB->count_records('local_catalog_allcourses', array('catalog_id'=>$data->catalog_id))+1;
    $id = $DB->insert_record('local_catalog_allcourses', $data);
    return $id;
}

function local_catalog_delete_course_edition($id, $catalog_id){
	global $DB;
    $result = $DB->delete_records('local_catalog_allcourses', array('id'=>$id, 'catalog_id'=>$catalog_id));
    if($result==false)return $result;

    $meta = $DB->get_records('local_catalog_allcourses', array('catalog_id'=>$catalog_id), 'sequence');
    $i=1;
    foreach($meta as $m){
    	$obj = new StdClass();
    	$obj->id = $m->id;
    	$obj->sequence = $i;
    	$DB->update_record('local_catalog_allcourses',$obj);
    	unset($obj);
    	$i++;
    }
    return;
}

function local_catalog_move_course_edition($direction, $id, $catalog_id){
	global $DB;
	$direction = strtolower($direction);
	if($direction!="down"&&$direction!="up")return false;

	$sequence = $DB->get_field('local_catalog_allcourses','sequence',array('id'=>$id), MUST_EXIST);	

	if($direction=="up") $newseq = $sequence - 1;
	if($direction=="down")$newseq = $sequence + 1;
	$switch = $DB->get_field('local_catalog_allcourses','id',array('catalog_id'=>$catalog_id, 'sequence'=>$newseq), MUST_EXIST);

    $obj = new StdClass();
	$obj->id = $id;
	$obj->sequence = $newseq;
	$DB->update_record('local_catalog_allcourses',$obj);

    $obj = new StdClass();
	$obj->id = $switch;
	$obj->sequence = $sequence;
	$DB->update_record('local_catalog_allcourses',$obj);

}

function local_catalog_get_course_editions($catalog_id, $keytype="SEQUENTIAL"){
	global $DB;
	$keytype = strtolower($keytype);
	$all_course_list = local_catalog_get_all_moodle_courses();
	$ed_list = $DB->get_records('local_catalog_allcourses',array('catalog_id'=>$catalog_id),'sequence');

	$ced = array();
	$i=0;
	foreach($ed_list as $elem){
		if($keytype=="sequential")$key = $i;
		else $key = $elem->id;
		$ced[$key]['id'] = $elem->id;
		$ced[$key]['course_id'] = $elem->course_id;
		$ced[$key]['coursename'] = $all_course_list[$elem->course_id];
		$ced[$key]['label'] = $elem->label;
		$ced[$key]['sequence'] = $elem->sequence;
		$i++;
	}
	return $ced;
}

function local_catalog_add_section($data) {
    global $DB;
    $data->sequence = $DB->count_records('local_catalog_sections')+1;
    $data->enabled = 0;
    $id = $DB->insert_record('local_catalog_sections', $data);
    return $id;
}

function local_catalog_update_section($data){
	global $DB;
	return $DB->update_record('local_catalog_sections', $data);
}

function local_catalog_delete_section($id){
	global $DB;
	$DB->delete_records('local_catalog_section_course', array('catalog_section_id'=>$id));
	$DB->delete_records('local_catalog_sections', array('id'=>$id));
	return;
}

function local_catalog_move_section($direction, $id){
	global $DB;
	$direction = strtolower($direction);
	if($direction!="down"&&$direction!="up")return false;

	$sequence = $DB->get_field('local_catalog_sections','sequence',array('id'=>$id), MUST_EXIST);	

	if($direction=="up") $newseq = $sequence - 1;
	if($direction=="down")$newseq = $sequence + 1;
	$switch = $DB->get_field('local_catalog_sections','id',array('sequence'=>$newseq), MUST_EXIST);
    $obj = new StdClass();
	$obj->id = $id;
	$obj->sequence = $newseq;
	$DB->update_record('local_catalog_sections',$obj);

    $obj = new StdClass();
	$obj->id = $switch;
	$obj->sequence = $sequence;
	$DB->update_record('local_catalog_sections',$obj);

}

function local_catalog_section_course_add($data) {
    global $DB;
    $data->sequence = $DB->count_records('local_catalog_section_course', array('catalog_section_id'=>$data->catalog_section_id))+1;
    $id = $DB->insert_record('local_catalog_section_course', $data);
    return $id;
}

function local_catalog_section_course_delete($id, $section_id){
	global $DB;
	$result = $DB->delete_records('local_catalog_section_course', array('id'=>$id, 'catalog_section_id'=>$section_id));
    if($result==false)return $result;

    $meta = $DB->get_records('local_catalog_section_course', array('catalog_section_id'=>$section_id), 'sequence');
    $i=1;
    foreach($meta as $m){
    	$obj = new StdClass();
    	$obj->id = $m->id;
    	$obj->sequence = $i;
    	$DB->update_record('local_catalog_section_course',$obj);
    	unset($obj);
    	$i++;
    }
    return;
}

function local_catalog_section_course_move($direction, $id, $section_id){
	global $DB;
	$direction = strtolower($direction);
	if($direction!="down"&&$direction!="up")return false;

	$sequence = $DB->get_field('local_catalog_section_course','sequence',array('id'=>$id), MUST_EXIST);	

	if($direction=="up") $newseq = $sequence - 1;
	if($direction=="down")$newseq = $sequence + 1;
	$switch = $DB->get_field('local_catalog_section_course','id',array('catalog_section_id'=>$section_id, 'sequence'=>$newseq), MUST_EXIST);

    $obj = new StdClass();
	$obj->id = $id;
	$obj->sequence = $newseq;
	$DB->update_record('local_catalog_section_course',$obj);

    $obj = new StdClass();
	$obj->id = $switch;
	$obj->sequence = $sequence;
	$DB->update_record('local_catalog_section_course',$obj);
}