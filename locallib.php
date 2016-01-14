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

//Courses
function local_catalog_get_courses(){
	global $DB;
	$i=0;

	$metadata_list = $DB->get_records('local_catalog_course_meta',null,'sequence');
	$i=0;
	foreach($metadata_list as $m){
		$metadata[$m->id][$i]['metadata_id'] = $m->metadata_id;
		$metadata[$m->id][$i]['value'] = $m->value;
		$metadata[$m->id][$i]['url'] = $m->url;
		$metadata[$m->id][$i]['sequence'] = $m->sequence;
		$i++;
	}

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