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
 * Main page for the parent portal.
 *
 * Displays the add child form and the list of existing children.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_parentportal\form\add_child_form;
use local_parentportal\manager;

require_login();

$context = context_system::instance();
require_capability('local/parentportal:addchild', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/parentportal/index.php'));
$PAGE->set_title(get_string('pagetitle', 'local_parentportal'));
$PAGE->set_heading(get_string('pagetitle', 'local_parentportal'));
$PAGE->set_pagelayout('standard');

$parentid = $USER->id;

// Instantiate the form.
$form = new add_child_form();

if ($form->is_cancelled()) {
    redirect(new moodle_url('/'));
} else if ($data = $form->get_data()) {
    try {
        $child = manager::create_child($data, $parentid);
        $fullname = fullname($child);
        $message = get_string('childcreated', 'local_parentportal', $fullname);
        \core\notification::add($message, \core\output\notification::NOTIFY_SUCCESS);
        redirect(new moodle_url('/local/parentportal/index.php'));
    } catch (\Exception $e) {
        $message = $e->getMessage();
        \core\notification::add($message, \core\output\notification::NOTIFY_ERROR);
        redirect(new moodle_url('/local/parentportal/index.php'));
    }
}

// Output the page.
echo $OUTPUT->header();

// --- Add Child Form ---
echo html_writer::start_div('parentportal-form-container');
$form->display();
echo html_writer::end_div();

// --- Children List ---
echo html_writer::start_div('parentportal-children-container mt-4');
echo $OUTPUT->heading(get_string('mychildren', 'local_parentportal'), 3);

$children = manager::get_children($parentid);

if (empty($children)) {
    echo html_writer::tag('p', get_string('nochildren', 'local_parentportal'), ['class' => 'text-muted']);
} else {
    $table = new html_table();
    $table->head = [
        '',
        get_string('childname', 'local_parentportal'),
        get_string('childemail', 'local_parentportal'),
        get_string('childgrade', 'local_parentportal'),
        get_string('childcurriculum', 'local_parentportal'),
        get_string('dateadded', 'local_parentportal'),
        get_string('actions', 'local_parentportal'),
    ];
    $table->attributes['class'] = 'table table-striped parentportal-children-table';

    foreach ($children as $child) {
        // Fetch the full user record (needed for user_picture and fullname with all name fields).
        $childuser = $DB->get_record('user', ['id' => $child->childid]);
        $userpicture = $OUTPUT->user_picture($childuser, ['size' => 35, 'link' => false]);

        $fullname = fullname($childuser);
        $gradelabel = manager::get_grade_label($child->grade);
        $curriculumlabel = manager::get_curriculum_label($child->curriculum);
        $dateadded = userdate($child->timecreated);

        // Action links.
        $cardurl = new moodle_url('/local/parentportal/studentcard.php', ['childid' => $child->childid]);
        $cardlink = html_writer::link($cardurl, get_string('studentcard', 'local_parentportal'),
            ['class' => 'btn btn-sm btn-outline-primary']);

        $table->data[] = [
            $userpicture,
            $fullname,
            $child->email,
            $gradelabel,
            $curriculumlabel,
            $dateadded,
            $cardlink,
        ];
    }

    echo html_writer::table($table);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
