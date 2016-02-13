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
require_once('locallib.php');

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


class local_catalog_pages extends moodleform{
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

                $mform->addElement('text', 'fa_icon', get_string('fa-icon','local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('fa_icon', PARAM_TEXT);                   //Set type of element
                $mform->addRule('fa_icon', get_string('maximumchars', '', 32), 'maxlength', 32, 'client');

                $template = $mform->addElement('editor', 'template', get_string('template'));
                $mform->setType('template', PARAM_RAW);

                if (isset($data)) {
                        $this->set_data($data);
                        $template->setValue(array('text' => $data->template));
                }
                
                $this->add_action_buttons();
        }
}

class local_catalog_addcourse extends moodleform{
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

                if (isset($data)) {
                        $this->set_data($data);
                }
                
                $this->add_action_buttons();
        }
}

class local_catalog_editcourse extends moodleform{
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

                $mform->addElement('text', 'subtitle', get_string('subtitle', 'local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('subtitle', PARAM_TEXT);                   //Set type of element
                $mform->addRule('subtitle', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');

                $mform->addElement('text', 'preview_video_id', get_string('youtube_id_preview_video','local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('preview_video_id', PARAM_TEXT);                   //Set type of element
                $mform->addRule('preview_video_id', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');

                $mform->addElement('text', 'multi_course_label', get_string('label_mutliple_courses','local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('multi_course_label', PARAM_TEXT);                   //Set type of element
                $mform->addRule('multi_course_label', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');
                $mform->setDefault('multi_course_label', get_string('previous_courses','local_catalog'));

                $mform->addElement('advcheckbox', 'enrol_open', get_string('enrol_open', 'local_catalog'), null, array('group' => 1), array(0, 1));


                $enrolments = local_catalog_get_all_enrolments();
                $mform->addElement('select', 'enrol_course_id', get_string('enrolment_course', 'local_catalog'), $enrolments);

                $mform->addElement('editor', 'description', get_string('description'));
                $mform->setType('description', PARAM_RAW);

                $mform->addElement('editor', 'objectives', get_string('objectives','local_catalog'));
                $mform->setType('objectives', PARAM_RAW);

                $mform->addElement('text', 'thumbnail', get_string('thumbnail','local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('thumbnail', PARAM_URL);                   //Set type of element
                $mform->addRule('preview_video_id', get_string('maximumchars', '', 128), 'maxlength', 128, 'client');

                if (isset($data)) {
                        $this->set_data($data);
                }
                
                $this->add_action_buttons();
        }
}

class local_catalog_coursemeta extends moodleform{
        public function definition(){
                global $CFG;
                $mform = $this->_form; // Don't forget the underscore! 

                if (isset($this->_customdata['catalog_id']) && is_object($this->_customdata['catalog_id'])) {
                        $data = $this->_customdata['catalog_id'];
                }
                
                $metacategories = local_catalog_get_metadata_categories();

                $catlist = array();
                foreach($metacategories as $m){
                        $catlist[$m['id']] = $m['name'];
                }
                $mform->addElement('select', 'metadata_id', get_string('entry', 'local_catalog'), $catlist,  array('style'=>'width: 100%'));
                $mform->addRule('metadata_id', get_string('required'), 'required', null, 'client');

                $mform->addElement('text', 'value', get_string('value', 'local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->addRule('value', get_string('maximumchars', '', 128), 'maxlength', 128, 'client');
                $mform->addRule('value', get_string('required'), 'required', null, 'client');

                $mform->addElement('text', 'url', get_string('url'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('url', PARAM_URL);                   //Set type of element
                $mform->addRule('url', get_string('maximumchars', '', 128), 'maxlength', 128, 'client');

                if (isset($data)) {
                        $this->set_data($data);
                }

                $this->add_action_buttons();
        }

        function validation($data, $files){
                $errors = array();
                $datatype = local_catalog_metadata_get_datatype($data['metadata_id']);
                if($datatype=="date")if(strtotime($data['value'])===false)$errors['value'] = get_string('invalid');
                if($datatype=="numeric")if(!is_numeric($data['value']))$errors['value'] = get_string('invalid');
                if($datatype=="list")if(count(explode(';',$data['value']))<2)$errors['value'] = get_string('invalid');

                return $errors;
        }
}

class local_catalog_editcourse_mcs extends moodleform{
        public function definition(){
                global $CFG;
                $mform = $this->_form; // Don't forget the underscore! 

                if (isset($this->_customdata['catalog_id']) && is_object($this->_customdata['catalog_id'])) {
                        $catalog_id = $this->_customdata['catalog_id'];
                }
                $mform->addElement('select', 'section_id', get_string('entry', 'local_catalog'), local_catalog_get_all_microcredentials('assoc'),  array('style'=>'width: 100%'));
                $mform->addRule('section_id', get_string('required'), 'required', null, 'client');

                $this->add_action_buttons();
        }
}

class local_catalog_course_editions extends moodleform{
        public function definition(){
                $mform = $this->_form; // Don't forget the underscore! 

                if (isset($this->_customdata['catalog_id']) && is_object($this->_customdata['catalog_id'])) {
                        $catalog_id = $this->_customdata['catalog_id'];
                }

                $mform->addElement('text', 'label', get_string('label', 'local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
                $mform->setType('label', PARAM_TEXT);                   //Set type of element
                $mform->addRule('label', get_string('required'), 'required', null, 'client');
                $mform->addRule('label', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');

                $mform->addElement('select', 'course_id', get_string('course'), local_catalog_get_all_moodle_courses(),  array('style'=>'width: 100%'));
                $mform->addRule('course_id', get_string('required'), 'required', null, 'client');

                $this->add_action_buttons();
        }
}

class local_catalog_course_static_page_add extends moodleform{
            public function definition(){
                $mform = $this->_form; // Don't forget the underscore! 

                if (isset($this->_customdata['catalog_id']) && is_object($this->_customdata['catalog_id'])) {
                        $catalog_id = $this->_customdata['catalog_id'];
                }

                $mform->addElement('select', 'page_id', get_string('page'), local_catalog_get_pages(false),  array('style'=>'width: 100%'));
                $mform->addRule('page_id', get_string('required'), 'required', null, 'client');

                $this->add_action_buttons();
        }    
}

class local_catalog_course_page_edit extends moodleform{
        public function definition(){
            $mform = $this->_form; // Don't forget the underscore! 

            if (isset($this->_customdata['record']) && is_object($this->_customdata['record'])) {
                    $data = $this->_customdata['record'];
            }
    
            $mform->addElement('advcheckbox', 'use_template', get_string('use_template', 'local_catalog'), '', array('group' => 1), array(0, 1));
            $content = $mform->addElement('editor', 'content', get_string('content'));
            $mform->setType('content', PARAM_RAW);
            $mform->disabledIf('content', 'use_template', 'eq', '1');
            $this->add_action_buttons();

            if (isset($data)) {
                $this->set_data($data);
                $content->setValue(array('text' => $data->content));
            }
        }
}

class local_catalog_section_edit extends moodleform{
    public function definition(){
            $mform = $this->_form; // Don't forget the underscore! 

            if (isset($this->_customdata['record']) && is_object($this->_customdata['record'])) {
                    $data = $this->_customdata['record'];
            }

            $mform->addElement('text', 'name', get_string('name'), array('style'=>'width: 100%')); // Add elements to your form
            $mform->setType('name', PARAM_TEXT);  
            $mform->addRule('name', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'tagline', get_string('tagline', 'local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
            $mform->setType('tagline', PARAM_TEXT);     

            $mform->addElement('text', 'video', get_string('youtube_id_preview_video', 'local_catalog'), array('style'=>'width: 100%')); // Add elements to your form
            $mform->setType('video', PARAM_TEXT);   

            $header = $mform->addElement('editor', 'header', get_string('header', 'local_catalog'));
            $mform->setType('header', PARAM_RAW);

            $footer = $mform->addElement('editor', 'footer', get_string('footer', 'local_catalog'));
            $mform->setType('footer', PARAM_RAW);

            $mform->addElement('advcheckbox', 'enabled', get_string('enabled', 'local_catalog'), '', array('group' => 1), array(0, 1));
            $this->add_action_buttons();

            if (isset($data)) {
                $this->set_data($data);
                $header->setValue(array('text' => $data->header));
                $footer->setValue(array('text' => $data->footer));
            }
    }
}

class local_catalog_section_addcourse extends moodleform{
    public function definition(){
            $mform = $this->_form; // Don't forget the underscore! 

            if (isset($this->_customdata['record']) && is_object($this->_customdata['record'])) {
                    $data = $this->_customdata['record'];
            }

            $catalog_raw = local_catalog_get_courses();
            $catalog = array();
            foreach($catalog_raw as $c){
                $catalog[$c['id']] = $c['name'];
            }

            $mform->addElement('select', 'catalog_id', get_string('course'), $catalog,  array('style'=>'width: 100%'));
            $mform->addRule('catalog_id', get_string('required'), 'required', null, 'client');

            $this->add_action_buttons();
    }    
}