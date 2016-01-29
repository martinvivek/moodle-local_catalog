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
/// Build page
$returnurl = $CFG->wwwroot.'/local/catalog/courses.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add(get_string('coursesetup','local_catalog'), new moodle_url('/local/catalog/courses.php'), global_navigation::TYPE_CUSTOM);
//page layout
$PAGE->set_pagelayout('standard');    

$id = required_param('id',PARAM_INT);
$detail = get_course_detail($id);

$data = new stdClass();
$data->url = new moodle_url($returnurl);
$PAGE->set_title($detail['name']);
//$PAGE->navbar->add($course['name'], new moodle_url('/local/catalog/course_setup.php', array('id'=>$id, 'action'=>'editcourse')), global_navigation::TYPE_CUSTOM);
$data->header = $OUTPUT->header();
$data->heading =  $OUTPUT->heading($detail['name']);
$data->footer = $OUTPUT->footer();
$data->preview_video_id = $detail['preview_video_id'];
$data->description = $detail['description'];

echo $OUTPUT->render_from_template('local_catalog/course', $data);

?>