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
 * Form for adding a child user account.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_parentportal\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_parentportal\manager;

/**
 * Form definition for adding a child.
 */
class add_child_form extends \moodleform {

    /**
     * Define the form fields.
     */
    protected function definition() {
        $mform = $this->_form;

        // --- Personal Information ---
        $mform->addElement('header', 'personalinfo', get_string('addchild', 'local_parentportal'));

        // First name.
        $mform->addElement('text', 'firstname', get_string('firstname', 'local_parentportal'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        // Last name.
        $mform->addElement('text', 'lastname', get_string('lastname', 'local_parentportal'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');

        // Email.
        $mform->addElement('text', 'email', get_string('email', 'local_parentportal'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');

        // Password.
        $mform->addElement('password', 'password', get_string('password', 'local_parentportal'));
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');

        // Sex.
        $sexoptions = [
            ''       => get_string('choosedots'),
            'male'   => get_string('male', 'local_parentportal'),
            'female' => get_string('female', 'local_parentportal'),
        ];
        $mform->addElement('select', 'sex', get_string('sex', 'local_parentportal'), $sexoptions);
        $mform->addRule('sex', get_string('required'), 'required', null, 'client');

        // Birthdate.
        $mform->addElement('date_selector', 'birthdate', get_string('birthdate', 'local_parentportal'), [
            'startyear' => 2000,
            'stopyear'  => date('Y'),
            'optional'  => false,
        ]);

        // --- Location & Academic ---
        $mform->addElement('header', 'locationacademic', get_string('country', 'local_parentportal'));

        // Country.
        $countries = array_merge(['' => get_string('choosedots')], get_string_manager()->get_list_of_countries());
        $mform->addElement('select', 'country', get_string('country', 'local_parentportal'), $countries);
        $mform->addRule('country', get_string('required'), 'required', null, 'client');

        // Timezone.
        $timezones = \core_date::get_list_of_timezones(null, true);
        $mform->addElement('select', 'timezone', get_string('timezone', 'local_parentportal'), $timezones);
        $mform->setDefault('timezone', '99');

        // Grade.
        $gradeoptions = array_merge(['' => get_string('choosedots')], manager::get_grade_options());
        $mform->addElement('select', 'grade', get_string('grade', 'local_parentportal'), $gradeoptions);
        $mform->addRule('grade', get_string('required'), 'required', null, 'client');

        // Curriculum.
        $curricula = array_merge(['' => get_string('choosedots')], manager::get_curriculum_options());
        $mform->addElement('select', 'curriculum', get_string('curriculum', 'local_parentportal'), $curricula);
        $mform->addRule('curriculum', get_string('required'), 'required', null, 'client');

        // --- Photo ---
        $mform->addElement('header', 'photosection', get_string('personalphoto', 'local_parentportal'));

        // Personal photo upload.
        $mform->addElement('filepicker', 'personalphoto', get_string('personalphoto', 'local_parentportal'), null, [
            'maxbytes'       => 2 * 1024 * 1024, // 2 MB.
            'accepted_types' => ['image'],
        ]);

        // Buttons.
        $this->add_action_buttons(true, get_string('addchild', 'local_parentportal'));
    }

    /**
     * Server-side validation.
     *
     * @param array $data The form data.
     * @param array $files The uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Validate email uniqueness.
        if (!empty($data['email'])) {
            if ($DB->record_exists('user', ['email' => $data['email']])) {
                $errors['email'] = get_string('error_emailexists', 'local_parentportal');
            }
        }

        // Validate password against site policy.
        if (!empty($data['password'])) {
            $errmsg = '';
            if (!check_password_policy($data['password'], $errmsg)) {
                $errors['password'] = $errmsg;
            }
        }

        // Validate sex selection.
        if (empty($data['sex'])) {
            $errors['sex'] = get_string('required');
        }

        // Validate grade selection.
        if (empty($data['grade'])) {
            $errors['grade'] = get_string('required');
        }

        // Validate curriculum selection.
        if (empty($data['curriculum'])) {
            $errors['curriculum'] = get_string('required');
        }

        return $errors;
    }
}
