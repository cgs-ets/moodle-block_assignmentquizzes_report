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
 * Plugin external functions and services are defined here.
 *
 * @package   blocks_assignmentsquizzes_report
 * @category    external
 * @copyright 2020 Veronica Bermegui, Canberra Grammar School <veronica.bermegui@cgs.act.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_assignment_quizz_report_get_connect_assign_context' => [
        'classname' => 'block_assignmentsquizzes_report\external\api', // Class containing a reference to the external function.
        'methodname' => 'get_connect_assign_context', // External function name.
        'description' => 'Get template context for Connect assignment table', // Human readable description of the WS function.
        'type' => 'read', // DB rights of the WS function.
        'loginrequired' => true,
        'ajax' => true    // Is this service available to 'internal' ajax calls.
    ],

    'block_assignment_quizz_report_get_syn_assign_context' => [
        'classname' => 'block_assignmentsquizzes_report\external\api', // Class containing a reference to the external function.
        'methodname' => 'get_synergetic_assign_context', // External function name.
        'description' => 'Get template context for Synergetic assignments table', // Human readable description of the WS function.
        'type' => 'read', // DB rights of the WS function.
        'loginrequired' => true,
        'ajax' => true    // Is this service available to 'internal' ajax calls.
    ],
    
    'block_assignment_quizz_report_get_connect_quizz_context' => [
        'classname' => 'block_assignmentsquizzes_report\external\api', // Class containing a reference to the external function.
        'methodname' => 'get_connect_quizz_context', // External function name.
        'description' => 'Get template context for Connect quizz table', // Human readable description of the WS function.
        'type' => 'read', // DB rights of the WS function.
        'loginrequired' => true,
        'ajax' => true    // Is this service available to 'internal' ajax calls.
    ],

];
