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
 * This file keeps track of upgrades to the htmlresource module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    local_catalog
 * @author      Mark Samberg
 * @copyright  2015 NC State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute htmlresource upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_catalog_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
 /*
     * And upgrade begins here. For each one, you'll need one
     * block of code similar to the next one. Please, delete
     * this comment lines once this file start handling proper
     * upgrade code.
     *
     * if ($oldversion < YYYYMMDD00) { //New version in version.php
     * }
     *
     * Lines below (this included)  MUST BE DELETED once you get the first version
     * of your module ready to be installed. They are here only
     * for demonstrative purposes and to show how the htmlresource
     * iself has been upgraded.
     *
     * For each upgrade block, the file htmlresource/version.php
     * needs to be updated . Such change allows Moodle to know
     * that this file has to be processed.
     *
     * To know more about how to write correct DB upgrade scripts it's
     * highly recommended to read information available at:
     *   http://docs.moodle.org/en/Development:XMLDB_Documentation
     * and to play with the XMLDB Editor (in the admin menu) and its
     * PHP generation posibilities.
     *
     * First example, some fields were added to install.xml on 2007/04/01
     */
    if ($oldversion < 2016011400) {

        // Changing the default of field enrol_open on table local_catalog to 0.
        $table = new xmldb_table('local_catalog');
        $field = new xmldb_field('enrol_open', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'enrol_course_id');

        // Launch change of default for field enrol_open.
        $dbman->change_field_default($table, $field);

        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016011400, 'local', 'catalog');
    }

    if ($oldversion < 2016011900) {

        // Define table local_catalog_course_mcs to be created.
        $table = new xmldb_table('local_catalog_course_mcs');

        // Adding fields to table local_catalog_course_mcs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('catalog_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('section_id', XMLDB_TYPE_CHAR, '10', null, null, null, null);

        // Adding keys to table local_catalog_course_mcs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_catalog_id', XMLDB_KEY_FOREIGN, array('catalog_id'), 'local_catalog', array('id'));
        $table->add_key('fk_section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'course_sections', array('id'));

        // Conditionally launch create table for local_catalog_course_mcs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Define table local_catalog_pages to be created.
        $table = new xmldb_table('local_catalog_pages');

        // Adding fields to table local_catalog_pages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '32', null, null, null, null);
        $table->add_field('fa_icon', XMLDB_TYPE_CHAR, '32', null, null, null, null);
        $table->add_field('template', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_catalog_pages.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_catalog_pages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Define table local_catalog_course_pages to be created.
        $table = new xmldb_table('local_catalog_course_pages');

        // Adding fields to table local_catalog_course_pages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('catalog_id', XMLDB_TYPE_NUMBER, '10', null, null, null, null);
        $table->add_field('page_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_catalog_course_pages.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_catalog_id', XMLDB_KEY_FOREIGN, array('catalog_id'), 'local_catalog', array('id'));
        $table->add_key('fk_page_id', XMLDB_KEY_FOREIGN, array('page_id'), 'local_catalog_pages', array('id'));

        // Conditionally launch create table for local_catalog_course_pages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }



        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016011900, 'local', 'catalog');
    }

    if ($oldversion < 2016020300) {

        // Define field subtitle to be added to local_catalog.
        $table = new xmldb_table('local_catalog');
        $field = new xmldb_field('subtitle', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'name');

        // Conditionally launch add field subtitle.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Define field objectives to be added to local_catalog.
        $table = new xmldb_table('local_catalog');
        $field = new xmldb_field('objectives', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');

        // Conditionally launch add field objectives.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016020300, 'local', 'catalog');
    }

    if ($oldversion < 2016021000) {

        // Define field sequence to be added to local_catalog_course_pages.
        $table = new xmldb_table('local_catalog_course_pages');
        $field = new xmldb_field('sequence', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'content');

        // Conditionally launch add field sequence.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

                // Define field use_template to be added to local_catalog_course_pages.
        $table = new xmldb_table('local_catalog_course_pages');
        $field = new xmldb_field('use_template', XMLDB_TYPE_INTEGER, '1', null, null, null, '1', 'page_id');

        // Conditionally launch add field use_template.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016021000, 'local', 'catalog');
    }


    if ($oldversion < 2016021100) {

        // Define field sequence to be added to local_catalog_course_pages.
        $table = new xmldb_table('local_catalog_sections');
        $field = new xmldb_field('sequence', XMLDB_TYPE_INTEGER, '11', null, null, null, null, null);

        // Conditionally launch add field sequence.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016021100, 'local', 'catalog');
    }


    if ($oldversion < 2016021101) {

        // Define field footer to be added to local_catalog_sections.
        $table = new xmldb_table('local_catalog_sections');
        $field = new xmldb_field('footer', XMLDB_TYPE_TEXT, null, null, null, null, null, 'header');

        // Conditionally launch add field footer.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

                // Define field video to be added to local_catalog_sections.
        $table = new xmldb_table('local_catalog_sections');
        $field = new xmldb_field('video', XMLDB_TYPE_CHAR, '32', null, null, null, null, 'footer');

        // Conditionally launch add field video.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016021101, 'local', 'catalog');
    }

    if ($oldversion < 2016021700) {

        // Changing precision of field thumbnail on table local_catalog to (256).
        $table = new xmldb_table('local_catalog');
        $field = new xmldb_field('thumbnail', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'objectives');

        // Launch change of precision for field thumbnail.
        $dbman->change_field_precision($table, $field);

        // Catalog savepoint reached.
        upgrade_plugin_savepoint(true, 2016021700, 'local', 'catalog');
    }



     
    /*
     * And that's all. Please, examine and understand the 3 example blocks above. Also
     * it's interesting to look how other modules are using this script. Remember that
     * the basic idea is to have "blocks" of code (each one being executed only once,
     * when the module version (version.php) is updated.
     *
     * Lines above (this included) MUST BE DELETED once you get the first version of
     * yout module working. Each time you need to modify something in the module (DB
     * related, you'll raise the version and add one upgrade block here.
     *
     * Finally, return of upgrade result (true, all went good) to Moodle.
     */
    return true;
}