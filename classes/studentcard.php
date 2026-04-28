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
 * Student card CRUD manager.
 *
 * Handles saving and loading student card data across multiple tables.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_parentportal;

/**
 * Manager class for student card operations.
 */
class studentcard {

    /** @var array Student table fields. */
    private const STUDENT_FIELDS = [
        'nationality', 'passportid', 'address', 'city', 'state', 'zipcode',
        'telephone', 'healthconditions', 'medication', 'recentschool',
        'refnumber', 'studentphone',
    ];

    /** @var array Parent info table fields. */
    private const PARENT_FIELDS = [
        'legalguardian', 'type', 'firstname', 'lastname', 'nationality',
        'passportid', 'countryofresidence', 'educationlevel', 'occupation',
        'phone1', 'phone2', 'email',
    ];

    /** @var array Guardian table fields. */
    private const GUARDIAN_FIELDS = [
        'firstname', 'lastname', 'relationship', 'nationality', 'passportid',
        'countryofresidence', 'educationlevel', 'occupation',
        'phone1', 'phone2', 'email',
    ];

    /** @var array Emergency table fields. */
    private const EMERGENCY_FIELDS = [
        'fullname', 'relationship', 'phone', 'email',
    ];

    /**
     * Save all student card data from the form.
     *
     * @param int $childid The child user ID.
     * @param \stdClass $data The form data.
     */
    public static function save(int $childid, \stdClass $data): void {
        self::save_student($childid, $data);
        self::save_parentinfo($childid, $data, 1);
        self::save_parentinfo($childid, $data, 2);
        self::save_guardian($childid, $data);
        self::save_emergency($childid, $data);
    }

    /**
     * Load all student card data for a child.
     *
     * @param int $childid The child user ID.
     * @return \stdClass Object with all card data, keyed for form pre-population.
     */
    public static function load(int $childid): \stdClass {
        global $DB;

        $data = new \stdClass();

        // Student info.
        $student = $DB->get_record('local_parentportal_student', ['childid' => $childid]);
        if ($student) {
            foreach (self::STUDENT_FIELDS as $field) {
                $data->$field = $student->$field ?? '';
            }
        }

        // Parent 1 info.
        $parent1 = $DB->get_record('local_parentportal_parentinfo', ['childid' => $childid, 'parentnum' => 1]);
        if ($parent1) {
            foreach (self::PARENT_FIELDS as $field) {
                $key = 'p1_' . $field;
                $data->$key = $parent1->$field ?? '';
            }
        }

        // Parent 2 info.
        $parent2 = $DB->get_record('local_parentportal_parentinfo', ['childid' => $childid, 'parentnum' => 2]);
        if ($parent2) {
            foreach (self::PARENT_FIELDS as $field) {
                $key = 'p2_' . $field;
                $data->$key = $parent2->$field ?? '';
            }
        }

        // Guardian info.
        $guardian = $DB->get_record('local_parentportal_guardian', ['childid' => $childid]);
        if ($guardian) {
            foreach (self::GUARDIAN_FIELDS as $field) {
                $key = 'g_' . $field;
                $data->$key = $guardian->$field ?? '';
            }
        }

        // Emergency contact info.
        $emergency = $DB->get_record('local_parentportal_emergency', ['childid' => $childid]);
        if ($emergency) {
            foreach (self::EMERGENCY_FIELDS as $field) {
                $key = 'em_' . $field;
                $data->$key = $emergency->$field ?? '';
            }
        }

        return $data;
    }

    /**
     * Check if any card data exists for a child.
     *
     * @param int $childid The child user ID.
     * @return bool True if card data exists.
     */
    public static function has_data(int $childid): bool {
        global $DB;
        return $DB->record_exists('local_parentportal_student', ['childid' => $childid]);
    }

    /**
     * Check if the current user owns the child.
     *
     * @param int $childid The child user ID.
     * @param int $parentid The parent user ID.
     * @return bool True if the parent owns this child.
     */
    public static function verify_ownership(int $childid, int $parentid): bool {
        global $DB;
        return $DB->record_exists('local_parentportal_children', [
            'childid' => $childid,
            'parentid' => $parentid,
        ]);
    }

    /**
     * Save student general info.
     *
     * @param int $childid The child user ID.
     * @param \stdClass $data The form data.
     */
    private static function save_student(int $childid, \stdClass $data): void {
        global $DB;

        $record = new \stdClass();
        $record->childid = $childid;
        foreach (self::STUDENT_FIELDS as $field) {
            $record->$field = $data->$field ?? '';
        }
        $record->timemodified = time();

        $existing = $DB->get_record('local_parentportal_student', ['childid' => $childid]);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_parentportal_student', $record);
        } else {
            $DB->insert_record('local_parentportal_student', $record);
        }
    }

    /**
     * Save parent info (parent 1 or parent 2).
     *
     * @param int $childid The child user ID.
     * @param \stdClass $data The form data.
     * @param int $parentnum 1 or 2.
     */
    private static function save_parentinfo(int $childid, \stdClass $data, int $parentnum): void {
        global $DB;

        $prefix = 'p' . $parentnum . '_';

        $record = new \stdClass();
        $record->childid = $childid;
        $record->parentnum = $parentnum;
        foreach (self::PARENT_FIELDS as $field) {
            $key = $prefix . $field;
            $record->$field = $data->$key ?? '';
        }
        $record->timemodified = time();

        $existing = $DB->get_record('local_parentportal_parentinfo', [
            'childid' => $childid,
            'parentnum' => $parentnum,
        ]);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_parentportal_parentinfo', $record);
        } else {
            $DB->insert_record('local_parentportal_parentinfo', $record);
        }
    }

    /**
     * Save guardian info.
     *
     * @param int $childid The child user ID.
     * @param \stdClass $data The form data.
     */
    private static function save_guardian(int $childid, \stdClass $data): void {
        global $DB;

        $record = new \stdClass();
        $record->childid = $childid;
        foreach (self::GUARDIAN_FIELDS as $field) {
            $key = 'g_' . $field;
            $record->$field = $data->$key ?? '';
        }
        $record->timemodified = time();

        $existing = $DB->get_record('local_parentportal_guardian', ['childid' => $childid]);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_parentportal_guardian', $record);
        } else {
            $DB->insert_record('local_parentportal_guardian', $record);
        }
    }

    /**
     * Save emergency contact info.
     *
     * @param int $childid The child user ID.
     * @param \stdClass $data The form data.
     */
    private static function save_emergency(int $childid, \stdClass $data): void {
        global $DB;

        $record = new \stdClass();
        $record->childid = $childid;
        foreach (self::EMERGENCY_FIELDS as $field) {
            $key = 'em_' . $field;
            $record->$field = $data->$key ?? '';
        }
        $record->timemodified = time();

        $existing = $DB->get_record('local_parentportal_emergency', ['childid' => $childid]);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_parentportal_emergency', $record);
        } else {
            $DB->insert_record('local_parentportal_emergency', $record);
        }
    }

    /**
     * Get education level options.
     *
     * @return array Associative array of education level key => label.
     */
    public static function get_education_levels(): array {
        return [
            ''            => get_string('choosedots'),
            'highschool'  => get_string('edu_highschool', 'local_parentportal'),
            'diploma'     => get_string('edu_diploma', 'local_parentportal'),
            'bachelors'   => get_string('edu_bachelors', 'local_parentportal'),
            'masters'     => get_string('edu_masters', 'local_parentportal'),
            'phd'         => get_string('edu_phd', 'local_parentportal'),
            'other'       => get_string('edu_other', 'local_parentportal'),
        ];
    }

    /**
     * Get parent type options.
     *
     * @return array Associative array of parent type key => label.
     */
    public static function get_parent_types(): array {
        return [
            ''       => get_string('choosedots'),
            'father' => get_string('father', 'local_parentportal'),
            'mother' => get_string('mother', 'local_parentportal'),
            'other'  => get_string('other', 'local_parentportal'),
        ];
    }

    /**
     * Get yes/no options.
     *
     * @return array Associative array.
     */
    public static function get_yesno_options(): array {
        return [
            '0' => get_string('no', 'local_parentportal'),
            '1' => get_string('yes', 'local_parentportal'),
        ];
    }
}
