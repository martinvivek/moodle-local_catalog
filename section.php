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
$returnurl = $CFG->wwwroot.'/local/catalog/section.php';
$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading($SITE->fullname);
//page layout
$PAGE->set_pagelayout('standard');    

$id = required_param('id',PARAM_INT);
$detail = local_catalog_get_section_detail($id);

$data = new stdClass();
$data->url = new moodle_url($returnurl);
$data->wwwroot = $CFG->wwwroot;
$PAGE->set_title($detail['name']);
$PAGE->navbar->add($detail['name'], new moodle_url('/local/catalog/section.php', array('id'=>$id)), global_navigation::TYPE_CUSTOM);
$data->header = $OUTPUT->header();
$data->heading =  $OUTPUT->heading($detail['name']);
$data->footer = $OUTPUT->footer();

$data->name = $detail['name'];
$data->tagline = $detail['tagline'];
if(strlen($detail['video'])>0)$data->video = $detail['video'];
if(strlen($detail['header'])>0)$data->section_header = $detail['header'];
if(strlen($detail['footer'])>0)$data->section_footer = $detail['footer'];

$data->courses = local_catalog_get_course_tiles($id);

echo $OUTPUT->render_from_template('local_catalog/section', $data);

?>