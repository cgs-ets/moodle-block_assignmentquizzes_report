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
 * Continuous reporting block
 *
 * @package    block_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/assignmentsquizzes_report/lib.php');

class block_assignmentsquizzes_report extends block_base
{

    public function init()
    {
        $this->title = get_string('pluginname', 'block_assignmentsquizzes_report');
    }

    public function get_content()
    {
        global  $OUTPUT, $PAGE, $DB, $USER;

        if ($this->content !== null) {
            return $this->content;
        }


        $config = get_config('block_assignmentsquizzes_report');

        // Check DB settings are available
        if (
            empty($config->dbtype) ||
            empty($config->dbhost) ||
            empty($config->dbuser) ||
            empty($config->dbpass) ||
            empty($config->dbname) ||
            empty($config->dbspmoodleassign) ||
            empty($config->dbspquizzbyid)  ||
            empty($config->dbspassignments)
        ) {
            $notification = new \core\output\notification(
                get_string('nodbsettings', 'block_assignmentsquizzes_report'),
                \core\output\notification::NOTIFY_ERROR
            );
            $notification->set_show_closebutton(false);
            return $OUTPUT->render($notification);
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        try {

            if (assignmentsquizzes_report\can_view_on_profile()) {
                $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);
                $data = assignmentsquizzes_report\get_template_context($profileuser->username);

                if (!empty($data)) {
                    $this->content->text = $OUTPUT->render_from_template('block_assignmentsquizzes_report/main', $data);
                }
            }
        } catch (\Exception $e) {
           
        }

        return $this->content;
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function instance_allow_config()
    {
        return false;
    }

    public function has_config()
    {
        return true;
    }

    public function hide_header()
    {
        return true;
    }
}
