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
 * Student card view/edit page.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_parentportal\form\student_card_form;
use local_parentportal\studentcard;

require_login();

$childid = required_param('childid', PARAM_INT);
$mode    = optional_param('mode', 'view', PARAM_ALPHA);

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
$PAGE->set_url(new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid, 'mode' => $mode]));
$PAGE->set_title(get_string('studentcardfor', 'local_parentportal', $fullname));
$PAGE->set_heading(get_string('studentcardfor', 'local_parentportal', $fullname));
$PAGE->set_pagelayout('standard');

// Breadcrumb back to portal.
$PAGE->navbar->add(get_string('pagetitle', 'local_parentportal'), new moodle_url('/local/parentportal/index.php'));
$PAGE->navbar->add(get_string('studentcard', 'local_parentportal'));

// ============================================================
// EDIT MODE.
// ============================================================
if ($mode === 'edit') {

    // Explicit action URL required — moodleform strips query params from qualified_me().
    $formurl = new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid, 'mode' => 'edit']);
    $form = new student_card_form($formurl->out(false), null, 'post', '', ['class' => 'parentportal-card-form']);

    // Load existing data.
    $existingdata = studentcard::load($childid);
    $existingdata->childid = $childid;
    $form->set_data($existingdata);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid]));
    } else if ($data = $form->get_data()) {
        studentcard::save($childid, $data);
        redirect(
            new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid]),
            get_string('cardsaved', 'local_parentportal'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }

    echo $OUTPUT->header();
    echo html_writer::start_div('parentportal-form-container');
    $form->display();
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

// ============================================================
// VIEW MODE.
// ============================================================
echo $OUTPUT->header();

// Action buttons.
$editurl = new moodle_url('/local/parentportal/studentcard.php', ['childid' => $childid, 'mode' => 'edit']);
$reporturl = new moodle_url('/report/studentgrades/index.php', ['userid' => $childid]);

$buttons_html = $OUTPUT->single_button($editurl, get_string('editcard', 'local_parentportal'), 'get');

if (file_exists($CFG->dirroot . '/report/studentgrades/lib.php')) {
    require_once($CFG->dirroot . '/report/studentgrades/lib.php');
    if (function_exists('report_studentgrades_can_access_user') && report_studentgrades_can_access_user($childid)) {
        $buttons_html .= html_writer::link($reporturl, 'View Grades & AI Report', ['class' => 'btn btn-info', 'style' => 'margin-left: 10px;']);
    }
}

echo html_writer::div(
    $buttons_html,
    'parentportal-action-btns mb-3 d-flex align-items-center'
);

if (!studentcard::has_data($childid)) {
    echo html_writer::tag('p', get_string('nocarddata', 'local_parentportal'), ['class' => 'alert alert-info']);
    echo $OUTPUT->footer();
    exit;
}

// Load all data.
$data = studentcard::load($childid);
$countries = get_string_manager()->get_list_of_countries();

/**
 * Helper to render a single label-value row.
 *
 * @param string $label The label text.
 * @param string $value The value text.
 * @return string HTML string.
 */
function render_field(string $label, string $value): string {
    if (empty($value)) {
        $value = '—';
    }
    return html_writer::div(
        html_writer::tag('dt', $label) . html_writer::tag('dd', s($value)),
        'card-field'
    );
}

/**
 * Render a country code as its full name.
 *
 * @param string $code The country code.
 * @param array $countries The country list.
 * @return string The country name.
 */
function country_name(string $code, array $countries): string {
    return $countries[$code] ?? $code;
}

/**
 * Get education level label.
 *
 * @param string $key The education level key.
 * @return string The label.
 */
function edu_label(string $key): string {
    $levels = studentcard::get_education_levels();
    return $levels[$key] ?? $key;
}

/**
 * Get parent type label.
 *
 * @param string $key The type key.
 * @return string The label.
 */
function ptype_label(string $key): string {
    $types = studentcard::get_parent_types();
    return $types[$key] ?? $key;
}

// --- Section 1: General ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_general', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('nationality', 'local_parentportal'), country_name($data->nationality ?? '', $countries));
echo render_field(get_string('passportid', 'local_parentportal'), $data->passportid ?? '');
echo render_field(get_string('address', 'local_parentportal'), $data->address ?? '');
echo render_field(get_string('city', 'local_parentportal'), $data->city ?? '');
echo render_field(get_string('state', 'local_parentportal'), $data->state ?? '');
echo render_field(get_string('zipcode', 'local_parentportal'), $data->zipcode ?? '');
echo render_field(get_string('telephone', 'local_parentportal'), $data->telephone ?? '');
echo render_field(get_string('healthconditions', 'local_parentportal'), $data->healthconditions ?? '');
echo render_field(get_string('medication', 'local_parentportal'), $data->medication ?? '');
echo render_field(get_string('recentschool', 'local_parentportal'), $data->recentschool ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 2: Additional Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_additional', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('refnumber', 'local_parentportal'), $data->refnumber ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 3: Parent 1 Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_parent1', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('legalguardian', 'local_parentportal'),
    ($data->p1_legalguardian ?? '0') === '1' ? get_string('yes', 'local_parentportal') : get_string('no', 'local_parentportal'));
echo render_field(get_string('parenttype', 'local_parentportal'), ptype_label($data->p1_type ?? ''));
echo render_field(get_string('firstname', 'local_parentportal'), $data->p1_firstname ?? '');
echo render_field(get_string('lastname', 'local_parentportal'), $data->p1_lastname ?? '');
echo render_field(get_string('nationality', 'local_parentportal'), country_name($data->p1_nationality ?? '', $countries));
echo render_field(get_string('passportid', 'local_parentportal'), $data->p1_passportid ?? '');
echo render_field(get_string('countryofresidence', 'local_parentportal'), country_name($data->p1_countryofresidence ?? '', $countries));
echo render_field(get_string('occupation', 'local_parentportal'), $data->p1_occupation ?? '');
echo render_field(get_string('phone1', 'local_parentportal'), $data->p1_phone1 ?? '');
echo render_field(get_string('phone2', 'local_parentportal'), $data->p1_phone2 ?? '');
echo render_field(get_string('email', 'local_parentportal'), $data->p1_email ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 4: Parent 2 Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_parent2', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('parenttype', 'local_parentportal'), ptype_label($data->p2_type ?? ''));
echo render_field(get_string('firstname', 'local_parentportal'), $data->p2_firstname ?? '');
echo render_field(get_string('lastname', 'local_parentportal'), $data->p2_lastname ?? '');
echo render_field(get_string('nationality', 'local_parentportal'), country_name($data->p2_nationality ?? '', $countries));
echo render_field(get_string('passportid', 'local_parentportal'), $data->p2_passportid ?? '');
echo render_field(get_string('educationlevel', 'local_parentportal'), edu_label($data->p2_educationlevel ?? ''));
echo render_field(get_string('occupation', 'local_parentportal'), $data->p2_occupation ?? '');
echo render_field(get_string('phone1', 'local_parentportal'), $data->p2_phone1 ?? '');
echo render_field(get_string('phone2', 'local_parentportal'), $data->p2_phone2 ?? '');
echo render_field(get_string('email', 'local_parentportal'), $data->p2_email ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 5: Legal Guardian Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_guardian', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('firstname', 'local_parentportal'), $data->g_firstname ?? '');
echo render_field(get_string('lastname', 'local_parentportal'), $data->g_lastname ?? '');
echo render_field(get_string('relationshiptostudent', 'local_parentportal'), $data->g_relationship ?? '');
echo render_field(get_string('nationality', 'local_parentportal'), country_name($data->g_nationality ?? '', $countries));
echo render_field(get_string('passportid', 'local_parentportal'), $data->g_passportid ?? '');
echo render_field(get_string('countryofresidence', 'local_parentportal'), country_name($data->g_countryofresidence ?? '', $countries));
echo render_field(get_string('educationlevel', 'local_parentportal'), edu_label($data->g_educationlevel ?? ''));
echo render_field(get_string('occupation', 'local_parentportal'), $data->g_occupation ?? '');
echo render_field(get_string('phone1', 'local_parentportal'), $data->g_phone1 ?? '');
echo render_field(get_string('phone2', 'local_parentportal'), $data->g_phone2 ?? '');
echo render_field(get_string('email', 'local_parentportal'), $data->g_email ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 6: Emergency Contact Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_emergency', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('emergencycontactname', 'local_parentportal'), $data->em_fullname ?? '');
echo render_field(get_string('emergencyrelationship', 'local_parentportal'), $data->em_relationship ?? '');
echo render_field(get_string('emergencyphone', 'local_parentportal'), $data->em_phone ?? '');
echo render_field(get_string('emergencyemail', 'local_parentportal'), $data->em_email ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

// --- Section 7: Contact Information ---
echo html_writer::start_div('studentcard-section');
echo html_writer::tag('h4', get_string('section_contact', 'local_parentportal'), ['class' => 'section-title']);
echo html_writer::start_tag('dl', ['class' => 'card-fields-grid']);
echo render_field(get_string('studentphone', 'local_parentportal'), $data->studentphone ?? '');
echo html_writer::end_tag('dl');
echo html_writer::end_div();

echo $OUTPUT->footer();
