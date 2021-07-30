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

use html_writer;
use renderer_base;
use templatable;

/**
 * Returns the context for the template
 * @return string
 */

function get_template_context($username)
{
    $assignments = get_synergetic_assignment_context($username);
    
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
        $assign->date =   date("d/m/Y", strtotime($assignmnet->timecreated));;
        $assign->coursename = $assignmnet->coursename;
        $assign->grade = $assignmnet->grade;
        $assign->outof = $assignmnet->outof;
        $assign->filefeedback = get_assignments_feedback_context($username);
        $assign->commentfeedback = '';
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
        $q->timestart = date("d/m/Y h:i A", strtotime($quizz->timestart));
        $q->timefinish = date("d/m/Y h:i A", strtotime($quizz->timefinish));
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

function get_synergetic_assignment_context($username)
{
    $results = get_syn_assignments_by_student_id($username);

    if (empty($results)) {
        return [];
    }

    $assessments = [];
    $terms = ['username' => $username];

    foreach ($results as $result) {
       
        $assignmentsummary = new \stdClass();
        $assignmentsummary->heading = $result->heading;
        $assignmentsummary->result =  $result->result;
        $assignmentsummary->outof = $result->markoutof;   
        $assignmentsummary->classdescription = $result->classdescription;   
        $assignmentsummary->weighting = (floatval(round($result->weightingfactor, 2))) * 100;
        $assignmentsummary->testdate = (new \DateTime($result->testdate))->format('d/m/Y');
        $assessments[$result->term][$result->weeknumber][] = $assignmentsummary;

    }
   

    $dummycell = new \stdClass();
    $dummycell->t = '';
    
    for($i = 0; $i < 6; $i++) {
        $dummycells[] = $dummycell;
    }

    foreach ($assessments as $t => $weeks) {
        
        $term = new \stdClass();
        $term->term = $t;
        $term->dummycell = '';
        $term->results = [];

        foreach ($weeks as $wn => $assesment) {
            foreach ($assesment as $assess) {
                $assess->week = $wn;
                $term->results[] = $assess;
            }
        }

        $terms['assessments']['details'][] = $term;

    }
  
    return $terms;
}

function get_assignments_feedback_context($username)
{
    $results = get_moodle_assignments_feedback($username, 23125); // gradeid
    
    if (empty($results)) {
        return [];
    }

    $fs = get_file_storage();
    $out = array();

    foreach ($results as $i => $feedback) {
        //$tree = new Feedback_files_tree(15261, 39, $feedback->filearea,  $feedback->component); EXAMPLE
        
        $tree = new Feedback_files_tree($feedback->contextid, $feedback->itemid, $feedback->filearea, $feedback->component);
        $feedback = new \stdClass();
        $feedback->url = $tree->get_tree();
        $out['urls'][] =  $feedback;
    }
    return ($out);
}

/**
 * Call to the SP 
 */
function get_syn_assignments_by_student_id($username)
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
function get_moodle_assignments_feedback($username, $gradeid)
{

    try {

        $config = get_config('block_assignmentsquizzes_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbspmoodleassignfeedback . ' :id, :gradeid';

        $params = array(
            'id' => $username,
            'gradeid' => $gradeid
        );

        $assignments = $externalDB->get_records_sql($sql, $params);

        return $assignments;
    } catch (\Exception $ex) {
        //   var_dump($ex);
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

        // Students are allowed to see block in their own profiles.
        if ($profileuser->username == $USER->username && !is_siteadmin($USER)) {
            return true;
        }

        // Parents are allowed to view block in their mentee profiles.
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


class Feedback_files_tree implements \renderable, \templatable
{
    public $contextid;
    public $dir;
    public $files;
    public $itemid;
    public $filearea;

    public function __construct($contextid, $itemid, $filearea, $component)
    {
        $this->contextid = $contextid;
        $this->component = $component;
        $this->itemid = $itemid;
        $this->filearea = $filearea;
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->contextid, $component, $filearea, $itemid);

        $this->files =   $fs->get_area_files(
            $this->context->id,
            $component,
            $filearea,
            $itemid,
            'timemodified',
            false
        );
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();
        $data->url = $this->htmllize_tree($this, $this->dir);
     
        return $data;
    }

    public  function htmllize_tree($tree, $dir)
    {
        global  $OUTPUT, $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }

        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $OUTPUT->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.s($subdir['dirname']).'</div> '.$this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->contextid.'/assignfeedback_file/'.$tree->filearea. '/'. $tree->itemid . $file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $image = $OUTPUT->pix_icon(file_file_icon($file), $filename,'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div class="fileuploadsubmission">'.html_writer::link($url, $image.$filename).'</div></li>';
            
        }


        $result .= '</ul>';
        return $result;
    }

    public function get_tree() {
        $htmlid = \html_writer::random_id('feedback_files_tree');
        $html = '<div id="' . $htmlid . '">';
        $html .=  $this->htmllize_tree($this, $this->dir);
        $html .= '</div>';

        return $html;
    }
}
