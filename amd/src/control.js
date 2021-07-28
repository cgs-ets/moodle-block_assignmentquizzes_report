
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
        self.getSynAssign();
        self.setupEvents();
        self.init_tree('feedback_files_tree');

    };


    Controls.prototype.setupEvents = function () {
        let self = this;

        $('.connect-assignment').on('custom.getConnectAssign', function () {
            self.getConnectAssign();
        });

        $('.connect-assignment').click(function () {
            $(this).trigger('custom.getConnectAssign');
        });

        $('.connect-assignment').on('custom.renderFeedbackTree', function () {
            self.init_tree();
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

    Controls.prototype.getSynAssign = function () {
        let self = this;
        const username = self.username;

        // Add spinner.
        $('#syn-assignment-table').removeAttr('hidden');

        Ajax.call([{
            methodname: 'block_assignment_quizz_report_get_syn_assign_context',
            args: {
                username: username
            },

            done: function (response) {
                Log.debug(response);
                 const htmlResult = response.html;
                $('#syn-assignment-table').attr('hidden', true);
                $('[data-region="syn-assignment"]').replaceWith(htmlResult);
               
            },

            fail: function (reason) {
                Log.error('block_assignmentsquizzes_report: Unable to get context.');
                Log.debug(reason);
                $('[data-region="syn-assignment"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

        $('.syn-assignment').off('custom.getSynAssign'); // Remove listener.
    
    }

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
                $('#connect-assignment-table').attr('hidden', true);
                $('[data-region="connect-assignment"]').replaceWith(htmlResult);
                self.render_feedback_file_tree();
            },

            fail: function (reason) {
                Log.error('block_assignmentsquizzes_report: Unable to get context.');
                Log.debug(reason);
                $('[data-region="connect-assignment"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
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
                $('#connect-quizz-table').attr('hidden', true);
                $('[data-region="connect-quizz"]').replaceWith(htmlResult);
            },

            fail: function (reason) {
                Log.error('block_assignmentsquizzes_report: Unable to get context.');
                Log.debug(reason);
                $('[data-region="connect-quizz"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
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

    Controls.prototype.render_feedback_file_tree = function () {
        var self = this;

        $('#connectassignrep').children().each(function (e) {
            $(this).find('td:last').children().each(function (i) {
                let treecol = ($(this).first()[i]);
                let htmlid = $(treecol).attr('id');

                self.init_tree(htmlid);
            });

        });
    }

    Controls.prototype.init_tree = function (htmlid) {
        var treeElement = Y.one('#' + htmlid);
       
        if (treeElement) {
            Y.use('yui2-treeview', 'node-event-simulate', function (Y) {
                var tree = new Y.YUI2.widget.TreeView(htmlid);
                tree.subscribe("clickEvent", function (node, event) {
                    // We want normal clicking which redirects to url.
                    return false;
                });

                tree.subscribe("enterKeyPressed", function (node) {
                    // We want keyboard activation to trigger a click on the first link.
                    Y.one(node.getContentEl()).one('a').simulate('click');
                    return false;
                });
                
                tree.setNodesProperty('className', 'feedbackfilestv', false);
                tree.render();
            });
        }

    }


    return { init: init }
});
