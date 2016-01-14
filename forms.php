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
 * Form classes for courseblog
 *
 * @package    mod_videoresource
 * @copyright  2014 Jurets
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/formslib.php");

class local_catalog_metadata extends moodleform{
	public function definition(){
		global $CFG;
		$mform = $this->_form; // Don't forget the underscore! 

                if (isset($this->_customdata['record']) && is_object($this->_customdata['record'])) {
                        $data = $this->_customdata['record'];
                }
        
                //resourceItem: ID (readonly, hidden)
                $mform->addElement('hidden', 'id');
                $mform->setType('id', PARAM_INT);
                        
                        //resourceItem: Title
                $mform->addElement('text', 'name', get_string('name'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('name', PARAM_TEXT);                   //Set type of element
                $mform->addRule('name', get_string('required'), 'required', null, 'client');
                $mform->addRule('name', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');

                $datatype['date']="Date";
                $datatype['numeric']="Numeric";
                $datatype['text']="Text";
                $datatype['list']="Semicolon Separated List";

                $mform->addElement('select', 'datatype', get_string('datatype', 'local_catalog'), $datatype,  array('style'=>'width: 100%'));
                $mform->addRule('datatype', get_string('required'), 'required', null, 'client');

                $mform->addElement('text', 'fa_icon', get_string('fa-icon','local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('fa_icon', PARAM_TEXT);                   //Set type of element
                $mform->addRule('fa_icon', get_string('required'), 'required', null, 'client');
                $mform->addRule('fa_icon', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');


                if (isset($data)) {
                        $this->set_data($data);
                }
                
                $this->add_action_buttons();
	}
}