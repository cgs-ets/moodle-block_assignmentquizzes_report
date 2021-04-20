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
 * Debugger
 *
 * @package   blocks_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

//use block_assignmentsquizzes_report\external\api;
use block_assignmentsquizzes_report\external\api as ExternalApi;


require_once(dirname(__FILE__) . '/../../config.php');

// Set context.
$context = context_system::instance();

// Set up page parameters.
$PAGE->set_context($context);
$pageurl = new moodle_url('/blocks/assignmentsquizzes_report/debug.php');
$PAGE->set_url($pageurl);
$title = get_string('pluginname', 'block_assignmentsquizzes_report');
$PAGE->set_heading($title);
$PAGE->set_title($SITE->fullname . ': ' . $title);
$PAGE->navbar->add($title);

// Ensure user is logged in and has capability to update course.
require_login();
require_capability('moodle/site:config', $context, $USER->id);

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$api = new ExternalApi();
echo "hello";


// Build page output
$output = '';
$output .= $OUTPUT->header();
$output .= $OUTPUT->footer();
echo $output;