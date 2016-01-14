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
* Setup page for the course catalog module
* 
* @author  Mark Samberg
* @package local_catalog
* @copyright  2015 NC State University
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*
*-----------------------------------------------------------
*/
/// Includes 
require_once("../../config.php");
require_once('forms.php');
require_once('locallib.php');
/// Security
$systemcontext = context_system::instance();
require_login();
require_capability('moodle/site:config', $systemcontext);
/// Build page
$returnurl = $CFG->wwwroot.'/local/catalog/courses.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add(get_string('coursesetup','local_catalog'), new moodle_url('/local/catalog/courses.php'), global_navigation::TYPE_CUSTOM);
//page layout
$PAGE->set_pagelayout('admin');     
//actions list
$actionIndex = 'index';
$addcourse = 'addcourse';
$displayindex = true;

$action = optional_param('action', 0, PARAM_TEXT);
$action = (!empty($action) ? $action : 'index');

if($action=="addcourse"){
		$addform = new local_catalog_addcourse(new moodle_url($returnurl, array('action' => $addcourse)));
		if($formdata = $addform->get_data()){
			$inserted_id = local_catalog_add_course($formdata);
            $success = isset($inserted_id);
		}
		unset($addform);
		unset($_POST);
}


if($displayindex){
		$data = new stdClass();
		$data->url = new moodle_url($returnurl);
		$data->sesskey=sesskey();
		$data->deleteicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'iconsmall'));
		$data->editicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/edit'), 'alt'=>get_string('edit'), 'class'=>'iconsmall'));
		$addform = new local_catalog_addcourse(new moodle_url($returnurl, array('action' => $addcourse)));
		$data->addform = $addform->render();

		$data->courselist = local_catalog_get_courses();
		if(count($data->courselist)>0)$data->has_courses = true;

        $data->header = $OUTPUT->header();
        $data->heading =  $OUTPUT->heading(get_string('coursesetup', 'local_catalog'));
        $data->footer = $OUTPUT->footer();
		echo $OUTPUT->render_from_template('local_catalog/addcourse', $data);
}

?>