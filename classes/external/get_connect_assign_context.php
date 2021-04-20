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
 *  Web service to get grade history/effort data.
 *
 * @package   assignmentsquizzes_report
 * @category
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_assignmentsquizzes_report\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use external_single_structure;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/blocks/assignmentsquizzes_report/lib.php');

/**
 * Trait implementing the external function block_grades_effort_report
 */
trait get_connect_assign_context
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */

    public static  function get_connect_assign_context_parameters()
    {
        return new external_function_parameters(
            array(
                'username' => new external_value(PARAM_RAW, 'student username')
            )
        );
    }

    /**
     * Return context.
     */
    public static function get_connect_assign_context($username)
    {
        global $USER, $PAGE;
        
        $context = \context_user::instance($USER->id);
       
        self::validate_context($context);
        //Parameters validation
        self::validate_parameters(self::get_connect_assign_context_parameters(), array('username' => $username));
        
        // Get the context for the template.
        $ctx = \assignmentsquizzes_report\get_moodle_assignments_context($username);
        $output = $PAGE->get_renderer('core');

        $html =  $output->render_from_template('block_assignmentsquizzes_report/moodleassignments', $ctx);
        return array(
            'html' => $html,
        );
    }

    /**
     * Describes the structure of the function return value.
     * @return external_single_structures
     */
    public static function get_connect_assign_context_returns()
    {
        return new external_single_structure(array(
            'html' =>  new external_value(PARAM_RAW, 'HTML with the Connect assignment table context'),
        ));
    }
}
