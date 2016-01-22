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
$returnurl = $CFG->wwwroot.'/local/catalog/setup.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add(get_string('catalogsetup','local_catalog'), new moodle_url('/local/catalog/setup.php'), global_navigation::TYPE_CUSTOM);
//page layout
$PAGE->set_pagelayout('admin');     
//actions list
$actionIndex = 'index';
$addmeta = 'addmeta';
$editmeta = 'editmeta';
$deletemeta = 'deletemeta';
$addpages = 'addpages';
$editpages = 'editpages';
$deletepages = 'deletepages';
$action = optional_param('action', 0, PARAM_TEXT);
$action = (!empty($action) ? $action : 'index');
if($action=="index")$displaypage = true;
else $displaypage = false;


if($action==$addmeta){
		$metaform = new local_catalog_metadata(new moodle_url($returnurl, array('action' => $addmeta)));
		if($formdata = $metaform->get_data()){
			$inserted_id = local_catalog_add_metadata_category($formdata);
            $success = isset($inserted_id);
		}
		unset($metaform);
		unset($_POST);
		$displaypage=true;
}

if($action==$deletemeta){
	$del_id = required_param('id', PARAM_INT);
	local_catalog_delete_metadata_category($del_id);
	$displaypage = true;
}

if($action==$editmeta){
		$id = required_param('id',PARAM_INT);
		$record = $DB->get_record('local_catalog_metadata',array('id'=>$id), '*', MUST_EXIST);
		$editform = new local_catalog_metadata(new moodle_url($returnurl, array('action' => $editmeta, 'id'=>$id)), array('record'=>$record));
		if($formdata = $editform->get_data()){
			$success = local_catalog_edit_metadata_category($formdata);
		}
		if ((isset($success)&&$success)||$editform->is_cancelled()){  //call create Resource Type function
			unset($metaform);
			unset($_POST);
            $displaypage=true;
        }
        else{
        	$data = new stdClass();
        	$PAGE->navbar->add(get_string('edit_metadata_entry','local_catalog'));
	        $data->header = $OUTPUT->header();
	        $data->heading =  $OUTPUT->heading(get_string('edit_metadata_entry', 'local_catalog'));
			$data->form = $editform->render();
	        $data->footer = $OUTPUT->footer();
			echo $OUTPUT->render_from_template('local_catalog/displayform', $data);
		}
}

if($action==$addpages){
		$template_form = new local_catalog_pages(new moodle_url($returnurl, array('action' => $addpages)));
		if($formdata = $template_form->get_data()){
			$formdata->template = $formdata->template['text'];
			$inserted_id = local_catalog_add_page($formdata);
            $success = isset($inserted_id);
		}
		unset($template_form);
		unset($_POST);
		$displaypage=true;
}

if($action==$editpages){
		$id = required_param('id',PARAM_INT);
		$record = $DB->get_record('local_catalog_pages',array('id'=>$id), '*', MUST_EXIST);
		$editform = new local_catalog_pages(new moodle_url($returnurl, array('action' => $editpages, 'id'=>$id)), array('record'=>$record));
		if($formdata = $editform->get_data()){
			$formdata->template = $formdata->template['text'];
			$success = local_catalog_edit_page($formdata);
		}
		if ((isset($success)&&$success)||$editform->is_cancelled()){  //call create Resource Type function
			unset($metaform);
			unset($_POST);
            $displaypage=true;
        }
        else{
        	$data = new stdClass();
        	$PAGE->navbar->add(get_string('edit_page','local_catalog'));
	        $data->header = $OUTPUT->header();
	        $data->heading =  $OUTPUT->heading(get_string('edit_page', 'local_catalog'));
			$data->form = $editform->render();
	        $data->footer = $OUTPUT->footer();
			echo $OUTPUT->render_from_template('local_catalog/displayform', $data);
		}
}


if($action==$deletepages){
	$del_id = required_param('id', PARAM_INT);
	local_catalog_delete_page($del_id);
	$displaypage = true;
}



if($displaypage){
		$data = new stdClass();
		$data->url = new moodle_url($returnurl);
		$data->sesskey=sesskey();
		$data->deleteicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'iconsmall'));
		$data->editicon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/edit'), 'alt'=>get_string('edit'), 'class'=>'iconsmall'));
		$metaform = new local_catalog_metadata(new moodle_url($returnurl, array('action' => $addmeta)));

		$data->metadata = local_catalog_get_metadata_categories();
		if(count($data->metadata)>0)$data->has_metadata = true;
		foreach($data->metadata as $key=>$elem){
			if($elem['count']=="0")$data->metadata[$key]['candelete'] = "true";
		}

		$data->metaform = $metaform->render();

		$data->pages = local_catalog_get_pages();
		if(count($data->pages)>0)$data->has_pages = true;
		foreach($data->pages as $key=>$elem){
			if($elem['count']=="0")$data->pages[$key]['candelete'] = "true";
		}

		$template_form = new local_catalog_pages(new moodle_url($returnurl, array('action' => $addpages)));
		$data->template_form = $template_form->render();

        $data->header = $OUTPUT->header();
        $data->heading =  $OUTPUT->heading(get_string('catalogsetup', 'local_catalog'));
        $data->footer = $OUTPUT->footer();
		echo $OUTPUT->render_from_template('local_catalog/setup', $data);
}