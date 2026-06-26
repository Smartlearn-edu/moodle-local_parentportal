<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Student card view page.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_parentportal\studentcard;

require_login();

$childid = required_param('childid', PARAM_INT);

$context  = context_system::instance();
$parentid = $USER->id;

// Verify ownership.
if (!studentcard::verify_ownership($childid, $parentid)) {
    throw new moodle_exception('error_noaccess', 'local_parentportal');
}

// Get child user info.
$childuser = $DB->get_record('user', ['id' => $childid], '*', MUST_EXIST);
$fullname  = fullname($childuser);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid]));
$PAGE->set_title(get_string('studentcardfor', 'local_parentportal', $fullname));
$PAGE->set_heading(get_string('studentcardfor', 'local_parentportal', $fullname));
$PAGE->set_pagelayout('standard');

// Breadcrumb back to portal.
$PAGE->navbar->add(get_string('pagetitle', 'local_parentportal'), new moodle_url('/local/parentportal/index.php'));
$PAGE->navbar->add(get_string('studentcard', 'local_parentportal'));

echo $OUTPUT->header();

$reporturl = new moodle_url('/report/studentgrades/index.php', ['userid' => $childid]);
$buttons_html = '';

if (file_exists($CFG->dirroot . '/report/studentgrades/lib.php')) {
    require_once($CFG->dirroot . '/report/studentgrades/lib.php');
    if (function_exists('report_studentgrades_can_access_user') && report_studentgrades_can_access_user($childid)) {
        $buttons_html .= html_writer::link($reporturl, 'View Grades & AI Report', ['class' => 'btn btn-info btn-lg']);
    }
}

if (!empty($buttons_html)) {
    echo html_writer::div(
        $buttons_html,
        'parentportal-action-btns mb-4 d-flex align-items-center justify-content-center'
    );
} else {
    echo html_writer::tag('p', get_string('noacademicdata', 'local_parentportal', 'No academic records available.'), ['class' => 'alert alert-info']);
}

// Fetch user courses to show basic academic progress
$courses = enrol_get_users_courses($childid, true);
if (!empty($courses)) {
    echo html_writer::tag('h3', 'Enrolled Courses');
    
    $table = new html_table();
    $table->head = ['Course', 'Category'];
    $table->attributes['class'] = 'table table-striped';
    
    foreach ($courses as $course) {
        $category = $DB->get_record('course_categories', ['id' => $course->category]);
        $categoryname = $category ? format_string($category->name) : '';
        
        $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        $courselink = html_writer::link($courseurl, format_string($course->fullname));
        
        $table->data[] = [
            $courselink,
            $categoryname
        ];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
