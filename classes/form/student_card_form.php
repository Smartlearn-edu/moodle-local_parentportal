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
 * Student card form with all 7 sections.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_parentportal\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_parentportal\studentcard;

/**
 * Form for editing the student card information.
 */
class student_card_form extends \moodleform {

    /**
     * Define the form fields.
     */
    protected function definition() {
        $mform = $this->_form;

        // Hidden field for childid.
        $mform->addElement('hidden', 'childid');
        $mform->setType('childid', PARAM_INT);

        // =====================================================================
        // SECTION 1: General.
        // =====================================================================
        $mform->addElement('header', 'section_general', get_string('section_general', 'local_parentportal'));

        $countries = array_merge(['' => get_string('choosedots')], get_string_manager()->get_list_of_countries());

        $mform->addElement('select', 'nationality', get_string('nationality', 'local_parentportal'), $countries);

        $mform->addElement('text', 'passportid', get_string('passportid', 'local_parentportal'));
        $mform->setType('passportid', PARAM_TEXT);

        $mform->addElement('text', 'address', get_string('address', 'local_parentportal'));
        $mform->setType('address', PARAM_TEXT);

        $mform->addElement('text', 'city', get_string('city', 'local_parentportal'));
        $mform->setType('city', PARAM_TEXT);

        $mform->addElement('text', 'state', get_string('state', 'local_parentportal'));
        $mform->setType('state', PARAM_TEXT);

        $mform->addElement('text', 'zipcode', get_string('zipcode', 'local_parentportal'));
        $mform->setType('zipcode', PARAM_TEXT);

        $mform->addElement('text', 'telephone', get_string('telephone', 'local_parentportal'));
        $mform->setType('telephone', PARAM_TEXT);

        $mform->addElement('textarea', 'healthconditions', get_string('healthconditions', 'local_parentportal'),
            ['rows' => 3, 'cols' => 50]);
        $mform->setType('healthconditions', PARAM_TEXT);

        $mform->addElement('textarea', 'medication', get_string('medication', 'local_parentportal'),
            ['rows' => 3, 'cols' => 50]);
        $mform->setType('medication', PARAM_TEXT);

        $mform->addElement('text', 'recentschool', get_string('recentschool', 'local_parentportal'));
        $mform->setType('recentschool', PARAM_TEXT);

        // =====================================================================
        // SECTION 2: Additional Information.
        // =====================================================================
        $mform->addElement('header', 'section_additional', get_string('section_additional', 'local_parentportal'));

        $mform->addElement('text', 'refnumber', get_string('refnumber', 'local_parentportal'));
        $mform->setType('refnumber', PARAM_TEXT);

        // =====================================================================
        // SECTION 3: Parent 1 Information.
        // =====================================================================
        $mform->addElement('header', 'section_parent1', get_string('section_parent1', 'local_parentportal'));

        $mform->addElement('select', 'p1_legalguardian', get_string('legalguardian', 'local_parentportal'),
            studentcard::get_yesno_options());

        $mform->addElement('select', 'p1_type', get_string('parenttype', 'local_parentportal'),
            studentcard::get_parent_types());

        $mform->addElement('text', 'p1_firstname', get_string('firstname', 'local_parentportal'));
        $mform->setType('p1_firstname', PARAM_TEXT);

        $mform->addElement('text', 'p1_lastname', get_string('lastname', 'local_parentportal'));
        $mform->setType('p1_lastname', PARAM_TEXT);

        $mform->addElement('select', 'p1_nationality', get_string('nationality', 'local_parentportal'), $countries);

        $mform->addElement('text', 'p1_passportid', get_string('passportid', 'local_parentportal'));
        $mform->setType('p1_passportid', PARAM_TEXT);

        $mform->addElement('select', 'p1_countryofresidence', get_string('countryofresidence', 'local_parentportal'), $countries);

        $mform->addElement('text', 'p1_occupation', get_string('occupation', 'local_parentportal'));
        $mform->setType('p1_occupation', PARAM_TEXT);

        $mform->addElement('text', 'p1_phone1', get_string('phone1', 'local_parentportal'));
        $mform->setType('p1_phone1', PARAM_TEXT);

        $mform->addElement('text', 'p1_phone2', get_string('phone2', 'local_parentportal'));
        $mform->setType('p1_phone2', PARAM_TEXT);

        $mform->addElement('text', 'p1_email', get_string('email', 'local_parentportal'));
        $mform->setType('p1_email', PARAM_TEXT);

        // =====================================================================
        // SECTION 4: Parent 2 Information.
        // =====================================================================
        $mform->addElement('header', 'section_parent2', get_string('section_parent2', 'local_parentportal'));

        $mform->addElement('select', 'p2_type', get_string('parenttype', 'local_parentportal'),
            studentcard::get_parent_types());

        $mform->addElement('text', 'p2_firstname', get_string('firstname', 'local_parentportal'));
        $mform->setType('p2_firstname', PARAM_TEXT);

        $mform->addElement('text', 'p2_lastname', get_string('lastname', 'local_parentportal'));
        $mform->setType('p2_lastname', PARAM_TEXT);

        $mform->addElement('select', 'p2_nationality', get_string('nationality', 'local_parentportal'), $countries);

        $mform->addElement('text', 'p2_passportid', get_string('passportid', 'local_parentportal'));
        $mform->setType('p2_passportid', PARAM_TEXT);

        $mform->addElement('select', 'p2_educationlevel', get_string('educationlevel', 'local_parentportal'),
            studentcard::get_education_levels());

        $mform->addElement('text', 'p2_occupation', get_string('occupation', 'local_parentportal'));
        $mform->setType('p2_occupation', PARAM_TEXT);

        $mform->addElement('text', 'p2_phone1', get_string('phone1', 'local_parentportal'));
        $mform->setType('p2_phone1', PARAM_TEXT);

        $mform->addElement('text', 'p2_phone2', get_string('phone2', 'local_parentportal'));
        $mform->setType('p2_phone2', PARAM_TEXT);

        $mform->addElement('text', 'p2_email', get_string('email', 'local_parentportal'));
        $mform->setType('p2_email', PARAM_TEXT);

        // =====================================================================
        // SECTION 5: Legal Guardian Information.
        // =====================================================================
        $mform->addElement('header', 'section_guardian', get_string('section_guardian', 'local_parentportal'));

        $mform->addElement('text', 'g_firstname', get_string('firstname', 'local_parentportal'));
        $mform->setType('g_firstname', PARAM_TEXT);

        $mform->addElement('text', 'g_lastname', get_string('lastname', 'local_parentportal'));
        $mform->setType('g_lastname', PARAM_TEXT);

        $mform->addElement('text', 'g_relationship', get_string('relationshiptostudent', 'local_parentportal'));
        $mform->setType('g_relationship', PARAM_TEXT);

        $mform->addElement('select', 'g_nationality', get_string('nationality', 'local_parentportal'), $countries);

        $mform->addElement('text', 'g_passportid', get_string('passportid', 'local_parentportal'));
        $mform->setType('g_passportid', PARAM_TEXT);

        $mform->addElement('select', 'g_countryofresidence', get_string('countryofresidence', 'local_parentportal'), $countries);

        $mform->addElement('select', 'g_educationlevel', get_string('educationlevel', 'local_parentportal'),
            studentcard::get_education_levels());

        $mform->addElement('text', 'g_occupation', get_string('occupation', 'local_parentportal'));
        $mform->setType('g_occupation', PARAM_TEXT);

        $mform->addElement('text', 'g_phone1', get_string('phone1', 'local_parentportal'));
        $mform->setType('g_phone1', PARAM_TEXT);

        $mform->addElement('text', 'g_phone2', get_string('phone2', 'local_parentportal'));
        $mform->setType('g_phone2', PARAM_TEXT);

        $mform->addElement('text', 'g_email', get_string('email', 'local_parentportal'));
        $mform->setType('g_email', PARAM_TEXT);

        // =====================================================================
        // SECTION 6: Emergency Contact Information.
        // =====================================================================
        $mform->addElement('header', 'section_emergency', get_string('section_emergency', 'local_parentportal'));

        $mform->addElement('text', 'em_fullname', get_string('emergencycontactname', 'local_parentportal'));
        $mform->setType('em_fullname', PARAM_TEXT);

        $mform->addElement('text', 'em_relationship', get_string('emergencyrelationship', 'local_parentportal'));
        $mform->setType('em_relationship', PARAM_TEXT);

        $mform->addElement('text', 'em_phone', get_string('emergencyphone', 'local_parentportal'));
        $mform->setType('em_phone', PARAM_TEXT);

        $mform->addElement('text', 'em_email', get_string('emergencyemail', 'local_parentportal'));
        $mform->setType('em_email', PARAM_TEXT);

        // =====================================================================
        // SECTION 7: Contact Information.
        // =====================================================================
        $mform->addElement('header', 'section_contact', get_string('section_contact', 'local_parentportal'));

        $mform->addElement('text', 'studentphone', get_string('studentphone', 'local_parentportal'));
        $mform->setType('studentphone', PARAM_TEXT);

        // Buttons.
        $this->add_action_buttons(true, get_string('savecard', 'local_parentportal'));
    }
}
