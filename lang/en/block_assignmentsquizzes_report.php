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
 * The graphs continuous assessment block
 *
 * @package    block_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Assignments & Quizzes Report';
$string['pluginname_desc'] = 'This plugin depends on DW.';
$string['assigquiz_report'] = 'Assignments';
$string['attendance_report:addinstance'] = 'Add a new Assignments & Quizzes report block';
$string['attendance_report:myaddinstance'] = 'Add a new Assignments & Quizzes report block to My Moodle page';
$string['dbtype'] = 'Database driver';
$string['dbtype_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhost'] = 'Database host';
$string['dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbname'] = 'Database name';
$string['dbuser'] = 'Database user';
$string['dbpass'] = 'Database password';
$string['dbspmoodleassign'] = 'Moodle assignment grades by id';
$string['dbspmoodleassign_desc'] = 'Stored procedure name to retrieve student Moodle assignments grades by student ID';
$string['dbspquizzbyid'] = 'Moodle quiz data by id';
$string['dbspquizzbyid_desc'] = 'Stored procedure name to retrieve student Moodle quizzes grades by student ID';
$string['dbspassignments'] = 'Student Assignments By ID SP';
$string['dbspassignments_desc'] = 'Stored procedure name to retrieve student Assignments  by student ID';
$string['nodbsettings'] = 'Please configure the DB options for the plugin';

$string['profileurl'] = 'Profile URL';
$string['profileurl_desc'] =' Moodle\'s profile URL';

$string['reportlabel'] = 'Assignments & Quizzes';
$string['course'] = 'Course';
$string['assignmentname'] = 'Assignment Name';
$string['date'] = 'Due';
$string['score'] = 'Score (Out of)';
$string['assignments'] = 'Assignments';

$string['moodlequiz'] = 'CGS Connect Quizzes';
$string['moodleassing'] = 'CGS Connect Assignments';
$string['quizname'] = 'Quiz name';
$string['quizstarted'] = 'Started';
$string['quizfinished'] = 'Finished';

$string['learningarea'] = 'Learning area';
$string['assessheading'] = 'Course';
$string['assessmentdescription'] = 'Assessment description';
$string['markoutof'] = 'Mark (Out of)';
$string['noincohort'] = 'No. in Cohort';
$string['weighting'] = 'Weighting (%)';
$string['cohortmeanscore'] = 'Cohort Mean';
$string['testdate'] = 'Test date';
$string['rank'] = "Rank";

$string['reportunavailable'] = 'Assignments and quizzes report unavailable';

