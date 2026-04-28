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
 * Language strings for the parent portal plugin.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General.
$string['pluginname'] = 'Parent portal';
$string['parentportal:addchild'] = 'Add a child via parent portal';
$string['pagetitle'] = 'Parent portal';
$string['addchild'] = 'Add child';
$string['mychildren'] = 'My children';
$string['nochildren'] = 'You have not added any children yet.';

// Form fields.
$string['firstname'] = 'First name';
$string['lastname'] = 'Last name';
$string['email'] = 'Email';
$string['password'] = 'Password';
$string['sex'] = 'Sex';
$string['male'] = 'Male';
$string['female'] = 'Female';
$string['birthdate'] = 'Birthdate';
$string['timezone'] = 'Timezone';
$string['country'] = 'Country';
$string['grade'] = 'Grade';
$string['curriculum'] = 'Curriculum';
$string['personalphoto'] = 'Personal photo';

// Grade options.
$string['grade_kg1'] = 'KG1';
$string['grade_kg2'] = 'KG2';
$string['grade_1'] = 'Grade 1';
$string['grade_2'] = 'Grade 2';
$string['grade_3'] = 'Grade 3';
$string['grade_4'] = 'Grade 4';
$string['grade_5'] = 'Grade 5';
$string['grade_6'] = 'Grade 6';
$string['grade_7'] = 'Grade 7';
$string['grade_8'] = 'Grade 8';
$string['grade_9'] = 'Grade 9';
$string['grade_10'] = 'Grade 10';
$string['grade_11'] = 'Grade 11';
$string['grade_12'] = 'Grade 12';

// Curriculum options.
$string['curriculum_american'] = 'American';
$string['curriculum_canadian'] = 'Canadian';
$string['curriculum_transition'] = 'Transition Student';
$string['curriculum_tunisian'] = 'Tunisian';

// Settings.
$string['settings_parentrole'] = 'Parent role';
$string['settings_parentrole_desc'] = 'Select the role to assign to the parent in the child\'s user context. This role establishes the parent-child relationship in Moodle.';

// Messages.
$string['childcreated'] = 'Child account for "{$a}" has been successfully created and linked to your account.';
$string['error_emailexists'] = 'This email address is already registered.';
$string['error_usernameexists'] = 'Could not generate a unique username. Please try different names.';
$string['error_norole'] = 'The parent role has not been configured. Please contact the site administrator.';
$string['error_creationfailed'] = 'An error occurred while creating the child account. Please try again.';

// Table headers.
$string['childname'] = 'Name';
$string['childemail'] = 'Email';
$string['childgrade'] = 'Grade';
$string['childcurriculum'] = 'Curriculum';
$string['dateadded'] = 'Date added';
