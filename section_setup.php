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
$returnurl = $CFG->wwwroot.'/local/catalog/section_setup.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
//page layout
$PAGE->set_pagelayout('admin');     

$displayindex = true;
$displayedit = false;

$action = optional_param('action', 0, PARAM_TEXT);
$action = (!empty($action) ? $action : 'index');

if($action=='addsection'){
		confirm_sesskey();
		$addform = new local_catalog_addcourse(new moodle_url($returnurl, array('action' => 'addsection')));
		if($formdata = $addform->get_data()){
			$inserted_id = local_catalog_add_section($formdata);
            $success = isset($inserted_id);
		}
		unset($addform);
		unset($_POST);
		$displayindex = true;
		$displayedit = false;

}

if($action=='deletesection'){
	confirm_sesskey();
	$id = required_param('id',PARAM_INT);
	local_catalog_delete_section($id);
	$displayindex = true;
	$displayedit = false;

}

if($action=="sectionup"){
	$id = required_param('id',PARAM_INT);
	confirm_sesskey();
	local_catalog_move_section("up", $id);
	$displayindex = true;
	$displayedit = false;

}

if($action=="sectiondown"){
	$id = required_param('id',PARAM_INT);
	confirm_sesskey();
	local_catalog_move_section("down", $id);
	$displayindex = true;
	$displayedit = false;
}

if($displayindex){
		$data = new stdClass();
		$data->returnurl = new moodle_url($returnurl);
		$data->sesskey=sesskey();
		$data->deleteicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'iconsmall'));
		$data->editicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/edit'), 'alt'=>get_string('edit'), 'class'=>'iconsmall'));
		$data->upicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/up'), 'alt'=>get_string('up'), 'class'=>'iconsmall'));
		$data->downicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/down'), 'alt'=>get_string('down'), 'class'=>'iconsmall'));
		$addform = new local_catalog_addcourse(new moodle_url($returnurl, array('action' => 'addsection')));
		$data->addform = $addform->render();

		$data->courselist = local_catalog_get_sections();
		if(count($data->courselist)>0){
			$data->has_courses = true;
			$data->courselist[0]['first'] = true;
			$data->courselist[count($data->courselist)-1]['last'] = true;
		}
        $data->header = $OUTPUT->header();
        $data->heading =  $OUTPUT->heading(get_string('sectionsetup', 'local_catalog'));
        $data->footer = $OUTPUT->footer();
		echo $OUTPUT->render_from_template('local_catalog/section_add', $data);
}

if($displayedit){
	local_catalog_get_all_microcredentials();
		if(isset($catalog_id))$id = $catalog_id;
		else $id = required_param('id', PARAM_INT);

		$record = $DB->get_record('local_catalog',array('id'=>$id), '*', MUST_EXIST);
		$editform = new local_catalog_editcourse(new moodle_url($returnurl, array('action' => $saveedits, 'id'=>$id)), array('record'=>$record));

		$data = new stdClass();
		$data->sesskey=sesskey();
		$data->deleteicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'iconsmall'));
		$data->editicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/edit'), 'alt'=>get_string('edit'), 'class'=>'iconsmall'));
		$data->upicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/up'), 'alt'=>get_string('up'), 'class'=>'iconsmall'));
		$data->downicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/down'), 'alt'=>get_string('down'), 'class'=>'iconsmall'));

		$course = get_course_detail($id);
		$PAGE->set_title($course['name']);
		$PAGE->navbar->add($course['name'], new moodle_url('/local/catalog/course_setup.php', array('id'=>$id, 'action'=>'editcourse')), global_navigation::TYPE_CUSTOM);
        $data->header = $OUTPUT->header();
        $data->heading =  $OUTPUT->heading($course['name']);
        $data->footer = $OUTPUT->footer();

		echo $OUTPUT->render_from_template('local_catalog/courses_edit', $data);
}

?>