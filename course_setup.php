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
$returnurl = $CFG->wwwroot.'/local/catalog/course_setup.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add(get_string('pluginname','local_catalog'), new moodle_url('/admin/settings.php', array('section'=>'catalog')), global_navigation::TYPE_CUSTOM);
$PAGE->navbar->add(get_string('coursesetup','local_catalog'), new moodle_url('/local/catalog/course_setup.php'), global_navigation::TYPE_CUSTOM);
//page layout
$PAGE->set_pagelayout('admin');     
//actions list
$actionIndex = 'index';
$addcourse = 'addcourse';
$editcourse = 'editcourse';
$saveedits = 'saveedits';
$deletecourse = 'deletecourse';

$addmeta = "addmeta";
$displayindex = true;
$displayedit = false;

$action = optional_param('action', 0, PARAM_TEXT);
$action = (!empty($action) ? $action : 'index');

if($action=="addcourse"){
		confirm_sesskey();
		$addform = new local_catalog_addcourse(new moodle_url($returnurl, array('action' => $addcourse)));
		if($formdata = $addform->get_data()){
			$inserted_id = local_catalog_add_course($formdata);
            $success = isset($inserted_id);
		}
		unset($addform);
		unset($_POST);
		$displayindex = true;
}

if($action == $deletecourse){
	$del_id = required_param('id', PARAM_INT);
	confirm_sesskey();
	local_catalog_delete_course($del_id);
	$displayindex = true;
}

if($action == $editcourse){
	$displayindex = false;
	$displayedit = true;
}

if($action==$saveedits){
	$id = required_param('id', PARAM_INT);
	confirm_sesskey();
	$record = $DB->get_record('local_catalog',array('id'=>$id), '*', MUST_EXIST);

		$record = $DB->get_record('local_catalog_metadata',array('id'=>$id), '*', MUST_EXIST);
		$editform =  new local_catalog_editcourse(new moodle_url($returnurl, array('action' => $saveedits, 'id'=>$id)), array('record'=>$record));
		if($formdata = $editform->get_data()){
			$formdata->description = $formdata->description['text'];
			$formdata->objectives = $formdata->objectives['text'];
			$success = local_catalog_edit_course($formdata);
			unset($record);
		}

	$displayindex = false;
	$displayedit = true;
}

if($action=="addmeta"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	confirm_sesskey();
	$addform =  new local_catalog_coursemeta(new moodle_url($returnurl, array('action' => $addmeta, 'catalog_id'=>$catalog_id)), array('catalog_id'=>$catalog_id));
	if($formdata = $addform->get_data()){
		$formdata->catalog_id = $catalog_id;
		$datatype = local_catalog_metadata_get_datatype($formdata->metadata_id);
		if($datatype=="date")$formdata->value = strtotime($formdata->value);
		local_catalog_add_course_metadata($formdata);
	}
	$displayindex = false;
	$displayedit = true;
}

if($action=="metadel"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	$metaid = required_param('metaid',PARAM_INT);
	confirm_sesskey();
	local_catalog_delete_course_metadata($metaid, $catalog_id);
	$displayindex = false;
	$displayedit = true;
}

if($action=="metadown"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	$metaid = required_param('metaid',PARAM_INT);
	confirm_sesskey();
	local_catalog_move_course_metadata("down", $metaid, $catalog_id);
	$displayindex = false;
	$displayedit = true;
}

if($action=="metaup"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	$metaid = required_param('metaid',PARAM_INT);
	confirm_sesskey();
	local_catalog_move_course_metadata("up", $metaid, $catalog_id);
	$displayindex = false;
	$displayedit = true;
}

if($action=="addmc"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	confirm_sesskey();
	$addform =  new local_catalog_editcourse_mcs(new moodle_url($returnurl, array('action' => $addmc, 'catalog_id'=>$catalog_id)), array('catalog_id'=>$catalog_id));
	if($formdata = $addform->get_data()){
		$formdata->catalog_id = $catalog_id;
		local_catalog_add_course_mc($formdata);
	}
	$displayindex = false;
	$displayedit = true;
}

if($action=="mcdel"){
	$catalog_id = required_param('catalog_id',PARAM_INT);
	$mcid = required_param('mcid',PARAM_INT);
	confirm_sesskey();
	local_catalog_delete_course_mc($mcid, $catalog_id);
	$displayindex = false;
	$displayedit = true;
}

if($displayindex){
		$data = new stdClass();
		$data->returnurl = new moodle_url($returnurl);
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
		echo $OUTPUT->render_from_template('local_catalog/courses_add', $data);
}

if($displayedit){
	local_catalog_get_all_microcredentials();
		if(isset($catalog_id))$id = $catalog_id;
		else $id = required_param('id', PARAM_INT);

		$record = $DB->get_record('local_catalog',array('id'=>$id), '*', MUST_EXIST);
		$editform = new local_catalog_editcourse(new moodle_url($returnurl, array('action' => $saveedits, 'id'=>$id)), array('record'=>$record));
		$metaform = new local_catalog_coursemeta(new moodle_url($returnurl, array('action' => $addmeta, 'catalog_id'=>$id)), array('catalog_id'=>$id));
		$mcform = new local_catalog_editcourse_mcs(new moodle_url($returnurl, array('action' => 'addmc', 'catalog_id'=>$id)), array('catalog_id'=>$id));

		$data = new stdClass();
		$data->url = new moodle_url($returnurl);
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
        $data->editform = $editform->render();
        $data->metaform = $metaform->render();
        $data->catalog_id = $id;
        $data->metadata = local_catalog_get_course_metadata($id);
       	$data->metadata[0]['first'] = true;
       	$data->metadata[count($data->metadata)-1]['last'] = true;
        if(count($data->metadata)>0){
        	$data->hasmeta = true;
        	foreach($data->metadata as $key=>$elem){
        		if($elem['datatype']=="list")$data->metadata[$key]['islist']=true;
        	}
        }
        $data->mcform = $mcform->render();
        $data->course_mcs = local_catalog_get_course_microcredentials($id);
        if(count($data->course_mcs)>0)$data->hasmcs = true;
		echo $OUTPUT->render_from_template('local_catalog/courses_edit', $data);
}

?>