
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
 * @package   block_assignmentsquizzes_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'core/ajax', 'core/log'], function ($, Ajax, Log) {
    'use strict';

    function init() {
        const element = document.querySelector('.connect-assign-quizz');
        const username = element.getAttribute('data-username');

        var control = new Controls(username);
        control.main();
    }

    /**
    * Controls a single block_assignmentsquizzes_report block instance contents.
    *
    * @constructor
    */
    function Controls(username) {
        let self = this;
        self.username = username;
    }

    /**
     * Run the controller.
     *
     */
    Controls.prototype.main = function () {
        let self = this;
        self.setupEvents();
    };


    Controls.prototype.setupEvents = function () {
        let self = this;

        $('.connect-assignment').on('custom.getConnectAssign', function () {
            self.getConnectAssign();
        });

        $('.connect-assignment').click(function () {
            $(this).trigger('custom.getConnectAssign');
        });

        $('.connect-assignment').on('custom.assignHideCarretHandler', function () {
            self.assignHideCarretHandler();
        });

        $('.connect-quizz').on('custom.getConnectQuizz', function () {
            self.getConnectQuizz();
        });

        $('.connect-quizz').click(function () {
            $(this).trigger("custom.getConnectQuizz");
        });

        $('.connect-quizz').on('custom.quizzHideCarretHandler', function () {
            self.quizzHideCarretHandler();
        });

    };

    Controls.prototype.getConnectAssign = function () {
        let self = this;
        const username = self.username;

        // Add spinner.
        $('#connect-assignment-table').removeAttr('hidden');
        $('#connectassignment-show').toggle(); // Carret down.
        $('#connectassignment-hide').toggle(); // Carret right

        Ajax.call([{
            methodname: 'block_assignment_quizz_report_get_connect_assign_context',
            args: {
                username: username
            },

            done: function (response) {
                const htmlResult = response.html;
                const region = $('[data-region="connect-assignment"]');

                $('#connect-assignment-table').attr('hidden', true);
                region.replaceWith(htmlResult);
            },

            fail: function (reason) {
                Log.error('block_assignmentsquizzes_report: Unable to get context.');
                Log.debug(reason);
                region.replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

        $('.connect-assignment').off('custom.getConnectAssign'); // Remove listener.
        $('.connect-assignment').on('click', function () {
            $(this).trigger("custom.assignHideCarretHandler");
        });

    };

    
    Controls.prototype.getConnectQuizz = function () {
        let self = this;
        const username = self.username;

        // Add spinner.
        $('#connect-quizz-table').removeAttr('hidden');
        $('#connectquizz-show').toggle(); // Carret down.
        $('#connectquizz-hide').toggle(); // Carret right

        Ajax.call([{
            methodname: 'block_assignment_quizz_report_get_connect_quizz_context',
            args: {
                username: username
            },

            done: function (response) {
                const htmlResult = response.html;
                const region = $('[data-region="connect-quizz"]');

                $('#connect-quizz-table').attr('hidden', true);
                region.replaceWith(htmlResult);
            },

            fail: function (reason) {
                Log.error('block_assignmentsquizzes_report: Unable to get context.');
                Log.debug(reason);
                region.replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

         $('.connect-quizz').off('custom.getConnectQuizz'); // Remove listener.

        $('.connect-quizz').on('click', function () {
            $(this).trigger("custom.quizzHideCarretHandler");
        });

    };

    Controls.prototype.assignHideCarretHandler = function () {

        $('#connectassignment-hide').toggle(); // Carret right. (Table is hidden)
        $('#connectassignment-show').toggle(); // Carret down. (Table is displayed)

    }

    Controls.prototype.quizzHideCarretHandler = function () {

        $('#connectquizz-hide').toggle(); // Carret right. (Table is hidden)
        $('#connectquizz-show').toggle(); // Carret down. (Table is displayed)
    }

    return { init: init }
});
