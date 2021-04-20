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
 *  Attendance report block
 *
 * @package    block_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignmentsquizzes_report;

/**
 * Returns the context for the template
 * @return string
 */

function get_template_context($username)
{
    $assignments = get_assignments_context($username);
    $assignments->username = $username;
    return $assignments; 
}

function get_moodle_assignments_context($username)
{

    $assignments = get_moodle_assignment_grades_by_id($username);
    
    if (empty($assignments)) {
        return [];
    }

    $data = [];

    foreach ($assignments as $assignmnet) {
        $assign = new \stdClass();
        $assign->assignmentname =  $assignmnet->name;
        $assign->date =   date("d-m-Y", strtotime($assignmnet->timecreated));;
        $assign->coursename = $assignmnet->coursename;
        //$assign->score = "$assignmnet->grade ($assignmnet->outof)";
        $assign->grade = $assignmnet->grade;
        $assign->outof = $assignmnet->outof;
        $data['courses'][$assignmnet->coursename][] = $assign;
        $context = [];

        foreach ($data['courses'] as $c => $courses) {
            foreach ($courses as $course) {
                $context['courses']['course'][] = $course;
            }
        }
    }
    return $context;
}

function get_quizzes_context($username)
{
    $quizzes = get_moodle_quiz_data_by_id($username);

    if (empty($quizzes)) {
        return [];
    }

    $data = [];

    foreach ($quizzes as $quizz) {
        $q = new \stdClass();
        $q->quizname =  $quizz->quizname;
        $q->coursenameq = $quizz->coursename;
        $q->timestart = date("d-m-Y h:i A", strtotime($quizz->timestart));
        $q->timefinish = date("d-m-Y h:i A", strtotime($quizz->timefinish));
        $q->sumgrades = $quizz->sumgrades;
        $q->maxmark = $quizz->maxmark;
        $data['courses'][$quizz->coursename][] = $q;
        $context = [];

        foreach ($data['courses'] as $c => $courses) {
            foreach ($courses as $course) {
                $context['quizzes']['quiz'][] = $course;
            }
        }
    }

    return $context;
}

function get_assignments_context($username)
{
    $results = get_assignments_by_student_id($username);

    if (empty($results)) {
        return [];
    }

    $assessments = [];
    foreach ($results as $result) {
        $assignmentsummary = new \stdClass();
        $assignmentsummary->subject = $result->assessheading;
        $assignmentsummary->assesnumber = substr($result->assessareahdgabbrev1, -1);
        $assignmentsummary->assessareaoverview = $result->assessareaoverview;
        $assignmentsummary->raw =  $result->raw;
        $assignmentsummary->rank =  $result->rank;
        $assignmentsummary->outof = $result->assessareamarkoutof;
        $assignmentsummary->numberinclass = $result->numberinclass; // No.In Cohort.
        $assignmentsummary->weighting = floatval(round($result->weighting, 2));
        $assignmentsummary->meanscore = floatval(round($result->meanscore, 2));
        $assignmentsummary->testdate = (new \DateTime($result->testdate))->format('d/m/Y');
        $assessments[$result->fileyear][$result->filesemester][$result->classlearningareadescription][] = $assignmentsummary;
    }

    // Just focus on this years subjects for the prototype.
    $curryear = date("Y");
    $aux = new \stdClass();
    $aux->details = [];
    
    foreach ($assessments as $year => $assessment) {
        if ($year != $curryear) continue; // For the prototype just display the current year assignments
        foreach ($assessment as $term => $assess) {
            foreach ($assess as $area => $detail) {
                $aux->details['subjects'][] = ['subject' => $detail, 'area' => $area];
            }
        }
    }
   
    return $aux;
}

/**
 * Call to the SP 
 */
function get_assignments_by_student_id($username)
{

    try {

        $config = get_config('block_assignmentsquizzes_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbspassignments . ' :id';

        $params = array(
            'id' => $username,
        );

        $assignments = $externalDB->get_records_sql($sql, $params);

        return $assignments;
    } catch (\Exception $ex) {
        throw $ex;
    }
}

/**
 * Call to the SP 
 */
function get_moodle_quiz_data_by_id($username)
{
    try {

        $config = get_config('block_assignmentsquizzes_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbspquizzbyid . ' :id';

        $params = array(
            'id' => $username,
        );

        $moodlequizzes = $externalDB->get_records_sql($sql, $params);

        return $moodlequizzes;
    } catch (\Exception $ex) {
        throw $ex;
    }
}
/**
 * Call to the SP 
 */
function get_moodle_assignment_grades_by_id($username)
{

    try {

        $config = get_config('block_assignmentsquizzes_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbspmoodleassign . ' :id';

        $params = array(
            'id' => $username,
        );

        $moodleassignments = $externalDB->get_records_sql($sql, $params);

        return $moodleassignments;
    } catch (\Exception $ex) {
        throw $ex;
    }
}


// Parent view of own child's activity functionality
function can_view_on_profile()
{
    global $DB, $USER, $PAGE;

    $config = get_config('block_assignmentsquizzes_report');

    if ($PAGE->url->get_path() ==  $config->profileurl) {
        // Admin is allowed.
        $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);

        if (is_siteadmin($USER) && $profileuser->username != $USER->username) {
            return true;
        }

        // Students are allowed to see timetables in their own profiles.
        if ($profileuser->username == $USER->username && !is_siteadmin($USER)) {
            return true;
        }

        // Parents are allowed to view timetables in their mentee profiles.
        $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));

        if ($mentorrole) {

            $sql = "SELECT ra.*, r.name, r.shortname
                FROM {role_assignments} ra
                INNER JOIN {role} r ON ra.roleid = r.id
                INNER JOIN {user} u ON ra.userid = u.id
                WHERE ra.userid = ?
                AND ra.roleid = ?
                AND ra.contextid IN (SELECT c.id
                    FROM {context} c
                    WHERE c.contextlevel = ?
                    AND c.instanceid = ?)";
            $params = array(
                $USER->id, //Where current user
                $mentorrole->id, // is a mentor
                CONTEXT_USER,
                $profileuser->id, // of the prfile user
            );
            $mentor = $DB->get_records_sql($sql, $params);
            if (!empty($mentor)) {
                return true;
            }
        }
    }

    return false;
}
