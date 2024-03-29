
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
* Settings for module resource
*
* All the resourcelib specific functions, needed to implement the module
* logic, should go here. Never include this file from your lib.php!
*
* @package    local_catalog
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die;
global $CFG, $PAGE;



    $ADMIN->add('localplugins', new admin_category('local_catalog', get_string('pluginname', 'local_catalog')));
$ADMIN->add('local_catalog', new admin_externalpage('local_catalog_setup',  get_string('catalogsetup', 'local_catalog'),
        $CFG->wwwroot.'/local/catalog/setup.php'));
$ADMIN->add('local_catalog', new admin_externalpage('local_catalog_course_setup',  get_string('coursepagesetup', 'local_catalog'),
        $CFG->wwwroot.'/local/catalog/course_setup.php'));
$ADMIN->add('local_catalog', new admin_externalpage('local_catalog_section_setup',  get_string('sectionsetup', 'local_catalog'),
        $CFG->wwwroot.'/local/catalog/section_setup.php'));
?>
