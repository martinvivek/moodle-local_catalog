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

/**
*	Get a list of all metadata categories
*	@return array of all metadata records
**/
function local_catalog_get_metadata_categories(){
	global $DB;
	$entry_list = $DB->get_records('local_catalog_metadata', null, 'name');

	$entries = array();
	$i=0;
	foreach($entry_list as $e){
		$entries[$i]['id'] = $e->id;
		$entries[$i]['name'] = $e->name;
		$entries[$i]['fa_icon'] = $e->fa_icon;
		$entries[$i]['datatype'] = $e->datatype;
		$entries[$i]['count'] = $DB->count_records('local_catalog_course_meta', array('metadata_id'=>$e->id));
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

function local_catalog_get_pages(){
	global $DB;
	$entry_list = $DB->get_records('local_catalog_pages', null, 'name');

	$entries = array();
	$i=0;
	foreach($entry_list as $e){
		$entries[$i]['id'] = $e->id;
		$entries[$i]['name'] = $e->name;
		$entries[$i]['fa_icon'] = $e->fa_icon;
		$entries[$i]['template'] = $e->template;
		$entries[$i]['count'] = $DB->count_records('local_catalog_course_pages', array('page_id'=>$e->id));
		$i++;
	}
	return $entries;

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
		$course['preview_video_id'] = $e->preview_video_id;
		$course['description'] = $e->description;
		$course['thumbnail'] = $e->thumbnail;
		$course['multi_course_label'] = $e->multi_course_label;
		$course['enrol_course_id'] = $e->enrol_course_id;
		$course['enrol_open'] = $e->enrol_open;
		$course['metadata'] = $metadata;
	return $course;

}


function local_catalog_get_courses(){
	global $DB;
	$i=0;

	$metadata = array();
	$metadata_list = $DB->get_records('local_catalog_course_meta',null,'sequence');
	$i=0;
	foreach($metadata_list as $m){
		$metadata[$m->id][$i]['metadata_id'] = $m->metadata_id;
		$metadata[$m->id][$i]['value'] = $m->value;
		$metadata[$m->id][$i]['url'] = $m->url;
		$metadata[$m->id][$i]['sequence'] = $m->sequence;
		$i++;
	}

	$entries = array();
	$entry_list = $DB->get_records('local_catalog', null, 'name');
	foreach($entry_list as $e){
		$entries[$i]['id'] = $e->id;
		$entries[$i]['name'] = $e->name;
		$entries[$i]['preview_video_id'] = $e->preview_video_id;
		$entries[$i]['description'] = $e->description;
		$entries[$i]['thumbnail'] = $e->thumbnail;
		$entries[$i]['multi_course_label'] = $e->multi_course_label;
		$entries[$i]['enrol_course_id'] = $e->enrol_course_id;
		$entries[$i]['enrol_open'] = $e->enrol_open;
		if(isset($metadata[$e->id]))$entries[$i]['metadata'] = $metadata[$e->id];
		$i++;
	}
	return $entries;

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