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
 * Manager class for the parent portal plugin.
 *
 * Handles child user creation, parent-child linking, and data retrieval.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_parentportal;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/lib/gdlib.php');

/**
 * Manager class for parent portal operations.
 */
class manager {

    /**
     * Create a child user account and link it to the parent.
     *
     * @param \stdClass $data Form data from the add child form.
     * @param int $parentid The ID of the parent user.
     * @return \stdClass The created child user object.
     * @throws \moodle_exception If creation fails.
     */
    public static function create_child(\stdClass $data, int $parentid): \stdClass {
        global $DB, $CFG;

        // Generate a unique username.
        $username = self::generate_username($data->firstname, $data->lastname);

        // Build the user object.
        $user = new \stdClass();
        $user->username     = $username;
        $user->firstname    = $data->firstname;
        $user->lastname     = $data->lastname;
        $user->email        = $data->email;
        $user->password     = $data->password;
        $user->auth         = 'manual';
        $user->confirmed    = 1;
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->country      = $data->country ?? '';
        $user->timezone     = $data->timezone ?? '99';
        $user->lang         = $CFG->lang;

        // Create the user (this hashes the password automatically).
        $user->id = user_create_user($user, true, false);

        // Handle the profile picture if uploaded.
        self::process_user_picture($user->id, $data);

        // Store the child metadata in our custom table.
        $record = new \stdClass();
        $record->parentid    = $parentid;
        $record->childid     = $user->id;
        $record->sex         = $data->sex;
        $record->grade       = $data->grade;
        $record->curriculum  = $data->curriculum;
        $record->timecreated = time();

        $DB->insert_record('local_parentportal_children', $record);

        // Assign the parent role in the child's user context.
        self::assign_parent_role($parentid, $user->id);

        return $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);
    }

    /**
     * Process and save the uploaded user picture.
     *
     * @param int $userid The user ID to set the picture for.
     * @param \stdClass $data The form data containing the file.
     */
    private static function process_user_picture(int $userid, \stdClass $data): void {
        global $CFG, $DB;

        $context = \context_user::instance($userid);
        $draftitemid = $data->personalphoto ?? 0;

        if (empty($draftitemid)) {
            return;
        }

        // Save the file from the draft area.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'user', 'newicon');
        file_save_draft_area_files($draftitemid, $context->id, 'user', 'newicon', 0, [
            'maxfiles' => 1,
            'accepted_types' => ['image'],
        ]);

        // Process the uploaded image into user icon format.
        $files = $fs->get_area_files($context->id, 'user', 'newicon', 0, 'itemid, filepath, filename', false);

        if (!empty($files)) {
            $file = reset($files);

            // process_new_icon() expects a file path string, not a stored_file object.
            // Copy the stored file to a temp location.
            $tempdir = make_temp_directory('parentportal');
            $tempfile = $tempdir . '/' . $file->get_filename();
            $file->copy_content_to($tempfile);

            $newpicture = process_new_icon($context, 'user', 'icon', 0, $tempfile);
            if ($newpicture !== false) {
                $DB->set_field('user', 'picture', $newpicture, ['id' => $userid]);
            }

            // Cleanup temp file and the newicon area.
            @unlink($tempfile);
            $fs->delete_area_files($context->id, 'user', 'newicon');
        }
    }

    /**
     * Assign the configured parent role in the child's user context.
     *
     * @param int $parentid The parent user ID.
     * @param int $childid The child user ID.
     * @throws \moodle_exception If the role is not configured.
     */
    private static function assign_parent_role(int $parentid, int $childid): void {
        $roleid = get_config('local_parentportal', 'parentroleid');

        if (empty($roleid)) {
            throw new \moodle_exception('error_norole', 'local_parentportal');
        }

        $context = \context_user::instance($childid);
        role_assign($roleid, $parentid, $context->id);
    }

    /**
     * Get all children linked to a parent.
     *
     * @param int $parentid The parent user ID.
     * @return array Array of child records with user and metadata.
     */
    public static function get_children(int $parentid): array {
        global $DB;

        $sql = "SELECT c.id, c.childid, c.sex, c.grade, c.curriculum, c.timecreated,
                       u.firstname, u.lastname, u.email, u.picture
                  FROM {local_parentportal_children} c
                  JOIN {user} u ON u.id = c.childid
                 WHERE c.parentid = :parentid
              ORDER BY c.timecreated DESC";

        return $DB->get_records_sql($sql, ['parentid' => $parentid]);
    }

    /**
     * Generate a unique username from first and last name.
     *
     * Produces a username like "firstname.lastname", appending a number if needed.
     *
     * @param string $firstname The first name.
     * @param string $lastname The last name.
     * @return string A unique username.
     * @throws \moodle_exception If unable to generate a unique username after many attempts.
     */
    public static function generate_username(string $firstname, string $lastname): string {
        global $DB;

        // Clean the names: lowercase, strip non-alphanumeric.
        $first = preg_replace('/[^a-z0-9]/', '', \core_text::strtolower(trim($firstname)));
        $last  = preg_replace('/[^a-z0-9]/', '', \core_text::strtolower(trim($lastname)));

        // Ensure we have something.
        if (empty($first)) {
            $first = 'child';
        }
        if (empty($last)) {
            $last = 'user';
        }

        $base = $first . '.' . $last;
        $username = $base;

        // Try up to 100 suffixes.
        for ($i = 1; $i <= 100; $i++) {
            if (!$DB->record_exists('user', ['username' => $username])) {
                return $username;
            }
            $username = $base . $i;
        }

        throw new \moodle_exception('error_usernameexists', 'local_parentportal');
    }

    /**
     * Get grade display label from grade key.
     *
     * @param string $gradekey The grade key (e.g. 'kg1', 'grade5').
     * @return string The human readable grade label.
     */
    public static function get_grade_label(string $gradekey): string {
        $grades = self::get_grade_options();
        return $grades[$gradekey] ?? $gradekey;
    }

    /**
     * Get curriculum display label from curriculum key.
     *
     * @param string $curriculumkey The curriculum key.
     * @return string The human readable curriculum label.
     */
    public static function get_curriculum_label(string $curriculumkey): string {
        $curricula = self::get_curriculum_options();
        return $curricula[$curriculumkey] ?? $curriculumkey;
    }

    /**
     * Get grade options for the form select element.
     *
     * @return array Associative array of grade key => label.
     */
    public static function get_grade_options(): array {
        return [
            'kg1'     => get_string('grade_kg1', 'local_parentportal'),
            'kg2'     => get_string('grade_kg2', 'local_parentportal'),
            'grade1'  => get_string('grade_1', 'local_parentportal'),
            'grade2'  => get_string('grade_2', 'local_parentportal'),
            'grade3'  => get_string('grade_3', 'local_parentportal'),
            'grade4'  => get_string('grade_4', 'local_parentportal'),
            'grade5'  => get_string('grade_5', 'local_parentportal'),
            'grade6'  => get_string('grade_6', 'local_parentportal'),
            'grade7'  => get_string('grade_7', 'local_parentportal'),
            'grade8'  => get_string('grade_8', 'local_parentportal'),
            'grade9'  => get_string('grade_9', 'local_parentportal'),
            'grade10' => get_string('grade_10', 'local_parentportal'),
            'grade11' => get_string('grade_11', 'local_parentportal'),
            'grade12' => get_string('grade_12', 'local_parentportal'),
        ];
    }

    /**
     * Get curriculum options for the form select element.
     *
     * @return array Associative array of curriculum key => label.
     */
    public static function get_curriculum_options(): array {
        return [
            'american'   => get_string('curriculum_american', 'local_parentportal'),
            'canadian'   => get_string('curriculum_canadian', 'local_parentportal'),
            'transition' => get_string('curriculum_transition', 'local_parentportal'),
            'tunisian'   => get_string('curriculum_tunisian', 'local_parentportal'),
        ];
    }
}
