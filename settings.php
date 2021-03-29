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
 * Attendance report block
 *
 * @package    block_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading(
        'block_assignmentsquizzes_report',
        '',
        get_string('pluginname_desc', 'block_assignmentsquizzes_report')
    ));

    $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
    $options = array_combine($options, $options);

    $settings->add(new admin_setting_configselect(
        'block_assignmentsquizzes_report/dbtype',
        get_string('dbtype', 'block_assignmentsquizzes_report'),
        get_string('dbtype_desc', 'block_assignmentsquizzes_report'),
        '',
        $options
    ));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbhost', get_string('dbhost', 'block_assignmentsquizzes_report'), get_string('dbhost_desc', 'block_assignmentsquizzes_report'), 'localhost'));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbuser', get_string('dbuser', 'block_assignmentsquizzes_report'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('block_assignmentsquizzes_report/dbpass', get_string('dbpass', 'block_assignmentsquizzes_report'), '', ''));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbname', get_string('dbname', 'block_assignmentsquizzes_report'), '', ''));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbspmoodleassign', get_string('dbspmoodleassign', 'block_assignmentsquizzes_report'), get_string('dbspmoodleassign_desc', 'block_assignmentsquizzes_report'), ''));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbspquizzbyid', get_string('dbspquizzbyid', 'block_assignmentsquizzes_report'), get_string('dbspquizzbyid_desc', 'block_assignmentsquizzes_report'), ''));

    $settings->add(new admin_setting_configtext('block_assignmentsquizzes_report/dbspassignments', get_string('dbspassignments', 'block_assignmentsquizzes_report'), get_string('dbspassignments_desc', 'block_assignmentsquizzes_report'), ''));
}
