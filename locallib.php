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