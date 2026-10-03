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
        $user->country      = $data->country ?? ($CFG->country ?? '');
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
        $record->curriculum  = $data->curriculum ?? '';
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
     * Get all children linked to a parent with enriched profile and enrollment data.
     *
     * @param int $parentid The parent user ID.
     * @return array Array of child records keyed by childid.
     */
    public static function get_children(int $parentid): array {
        global $DB, $OUTPUT;

        $sql = "SELECT c.id, c.childid, c.sex, c.grade, c.curriculum, c.timecreated,
                       u.firstname, u.lastname, u.email, u.picture, u.imagealt
                  FROM {local_parentportal_children} c
                  JOIN {user} u ON u.id = c.childid
                 WHERE c.parentid = :parentid
                   AND u.deleted = 0
              ORDER BY c.timecreated DESC";

        $records = $DB->get_records_sql($sql, ['parentid' => $parentid]);
        $children = [];

        foreach ($records as $r) {
            $childuser = (object)[
                'id'        => $r->childid,
                'picture'   => $r->picture,
                'firstname' => $r->firstname,
                'lastname'  => $r->lastname,
                'imagealt'  => $r->imagealt ?? '',
                'email'     => $r->email,
            ];

            $r->fullname = fullname($childuser);
            $r->gradelabel = !empty($r->grade) ? self::get_grade_label($r->grade) : '';
            $r->dateadded = userdate($r->timecreated, get_string('strftimedate', 'langconfig'));
            $r->avatar = $OUTPUT->user_picture($childuser, ['size' => 60, 'link' => false]);
            $r->avatarsmall = $OUTPUT->user_picture($childuser, ['size' => 32, 'link' => false]);

            // Enrolled courses count.
            $sqlcourses = "SELECT COUNT(DISTINCT c.id)
                             FROM {course} c
                             JOIN {enrol} e ON e.courseid = c.id
                             JOIN {user_enrolments} ue ON ue.enrolid = e.id
                            WHERE ue.userid = :childid
                              AND c.id <> :siteid
                              AND ue.status = :status";
            $r->enrolledcount = (int)$DB->count_records_sql($sqlcourses, [
                'childid' => $r->childid,
                'siteid'  => SITEID,
                'status'  => ENROL_USER_ACTIVE,
            ]);

            $children[$r->childid] = $r;
        }

        return $children;
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
     * @param ?string $curriculumkey The curriculum key.
     * @return string The human readable curriculum label.
     */
    public static function get_curriculum_label(?string $curriculumkey): string {
        if (empty($curriculumkey)) {
            return '';
        }
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

    /**
     * Check if a user is a parent in the portal.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public static function is_parent(int $userid): bool {
        global $DB;
        return $DB->record_exists('local_parentportal_children', ['parentid' => $userid]);
    }

    /**
     * Get the active child for a parent with session caching.
     *
     * @param int $parentid The parent user ID.
     * @param ?int $requestedchildid Explicitly requested child ID.
     * @return ?\stdClass The active child object or null.
     */
    public static function get_active_child(int $parentid, ?int $requestedchildid = null): ?\stdClass {
        global $SESSION;

        $children = self::get_children($parentid);
        if (empty($children)) {
            unset($SESSION->parentportal_active_child);
            return null;
        }

        // 1. Explicit request:
        if (!empty($requestedchildid) && isset($children[$requestedchildid])) {
            $SESSION->parentportal_active_child = $requestedchildid;
            return $children[$requestedchildid];
        }

        // 2. Existing session value:
        if (!empty($SESSION->parentportal_active_child) && isset($children[$SESSION->parentportal_active_child])) {
            return $children[$SESSION->parentportal_active_child];
        }

        // 3. Fallback to first child:
        $first = reset($children);
        $SESSION->parentportal_active_child = $first->childid;
        return $first;
    }

    /**
     * Get attendance metrics for a child.
     *
     * Integrates with local_async_attendance or core attendance.
     *
     * @param int $childid The child user ID.
     * @return array Attendance metrics and status.
     */
    public static function get_child_attendance(int $childid): array {
        global $DB;

        $total = 0;
        $present = 0;
        $absent = 0;
        $rate = null;

        // Check local_async_attendance first.
        if ($DB->get_manager()->table_exists('local_async_attendance')) {
            $total = $DB->count_records('local_async_attendance', ['userid' => $childid]);
            if ($total > 0) {
                $present = $DB->count_records('local_async_attendance', [
                    'userid' => $childid,
                    'status' => 'PRESENT',
                ]);
                $absent = $total - $present;
                $rate = round(($present / $total) * 100, 1);
            }
        }

        // Fallback to core attendance log if no async attendance records.
        if ($total === 0 && $DB->get_manager()->table_exists('attendance_log')) {
            $total = $DB->count_records('attendance_log', ['studentid' => $childid]);
            if ($total > 0) {
                $present = $total;
                $absent = 0;
                $rate = 100.0;
            }
        }

        if ($rate !== null) {
            if ($rate >= 90) {
                $statuslabel = get_string('attendance_status_excellent', 'local_parentportal');
                $badgeclass = 'bg-success-subtle text-success border border-success-subtle';
            } else if ($rate >= 75) {
                $statuslabel = get_string('attendance_status_good', 'local_parentportal');
                $badgeclass = 'bg-primary-subtle text-primary border border-primary-subtle';
            } else {
                $statuslabel = get_string('attendance_status_attention', 'local_parentportal');
                $badgeclass = 'bg-warning-subtle text-warning border border-warning-subtle';
            }
        } else {
            $rate = 100.0;
            $statuslabel = get_string('attendance_status_excellent', 'local_parentportal');
            $badgeclass = 'bg-success-subtle text-success border border-success-subtle';
        }

        return [
            'hasrecords'  => ($total > 0),
            'total'       => $total,
            'present'     => $present,
            'absent'      => $absent,
            'rate'        => $rate,
            'ratestr'     => $rate . '%',
            'statuslabel' => $statuslabel,
            'badgeclass'  => $badgeclass,
        ];
    }

    /**
     * Get high-level KPI metrics for a child.
     *
     * @param int $childid The child user ID.
     * @return array
     */
    public static function get_child_kpis(int $childid): array {
        global $DB;

        // 1. Enrolled courses count.
        $sqlcourses = "SELECT COUNT(DISTINCT c.id)
                         FROM {course} c
                         JOIN {enrol} e ON e.courseid = c.id
                         JOIN {user_enrolments} ue ON ue.enrolid = e.id
                        WHERE ue.userid = :childid
                          AND c.id <> :siteid
                          AND ue.status = :status";
        $enrolled = (int)$DB->count_records_sql($sqlcourses, [
            'childid' => $childid,
            'siteid'  => SITEID,
            'status'  => ENROL_USER_ACTIVE,
        ]);

        // 2. Average grade percentage.
        $sqlgrade = "SELECT AVG((gg.finalgrade / gi.grademax) * 100) AS avgg
                       FROM {grade_grades} gg
                       JOIN {grade_items} gi ON gi.id = gg.itemid
                      WHERE gg.userid = :childid
                        AND gi.itemtype = 'course'
                        AND gg.finalgrade IS NOT NULL
                        AND gi.grademax > 0";
        $rawgrade = $DB->get_field_sql($sqlgrade, ['childid' => $childid]);
        $avggrade = ($rawgrade !== false && $rawgrade !== null) ? round((float)$rawgrade, 1) : null;

        // 3. Upcoming deadlines.
        $deadlines = count(self::get_child_deadlines($childid, 50));

        // 4. Real attendance rate calculation.
        $attendance = self::get_child_attendance($childid);

        return [
            'enrolled_courses' => $enrolled,
            'avg_grade'        => $avggrade,
            'avg_grade_str'    => $avggrade !== null ? $avggrade . '%' : '--',
            'deadlines_count'  => $deadlines,
            'attendance_rate'  => $attendance['rate'],
            'attendance_str'   => $attendance['ratestr'],
        ];
    }

    /**
     * Get enrolled courses with completion progress, grades, teachers, and images for a child.
     *
     * @param int $childid The child user ID.
     * @return array List of enriched course objects.
     */
    public static function get_child_courses_progress(int $childid): array {
        global $DB, $CFG, $OUTPUT;

        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.summary, c.summaryformat, c.category,
                       c.startdate, c.enddate, cc.name AS categoryname
                  FROM {course} c
                  JOIN {course_categories} cc ON cc.id = c.category
                  JOIN {enrol} e ON e.courseid = c.id
                  JOIN {user_enrolments} ue ON ue.enrolid = e.id
                 WHERE ue.userid = :childid
                   AND c.id <> :siteid
                   AND c.visible = 1
                   AND ue.status = :status
                   AND e.status = :enrolstatus
              ORDER BY c.fullname ASC";

        $records = $DB->get_records_sql($sql, [
            'childid'     => $childid,
            'siteid'      => SITEID,
            'status'      => ENROL_USER_ACTIVE,
            'enrolstatus' => ENROL_INSTANCE_ENABLED,
        ]);

        $courses = [];
        foreach ($records as $r) {
            $course = $DB->get_record('course', ['id' => $r->id]);
            $cinfo = new \completion_info($course);
            $completionenabled = $cinfo->is_enabled();

            $progress = null;
            $iscompleted = false;
            if ($completionenabled) {
                $rawprog = \core_completion\progress::get_course_progress_percentage($course, $childid);
                if ($rawprog !== null) {
                    $progress = (int)round($rawprog);
                }
                $iscompleted = $cinfo->is_course_complete($childid);
            }

            // Grade calculation.
            $item = \grade_item::fetch_course_item($r->id);
            $gradepct = null;
            $gradestr = '--';
            $gradebadge = 'bg-light text-muted border';

            if ($item) {
                $gg = new \grade_grade(['itemid' => $item->id, 'userid' => $childid], true);
                if ($gg && $gg->finalgrade !== null && $item->grademax > 0) {
                    $gradepct = round(($gg->finalgrade / $item->grademax) * 100, 1);
                    $gradestr = $gradepct . '%';
                    if ($gradepct >= 85) {
                        $gradebadge = 'bg-success text-white';
                    } else if ($gradepct >= 70) {
                        $gradebadge = 'bg-primary text-white';
                    } else if ($gradepct >= 50) {
                        $gradebadge = 'bg-warning text-dark';
                    } else {
                        $gradebadge = 'bg-danger text-white';
                    }
                }
            }

            // Course image.
            $courseimage = null;
            $courseobj = new \core_course_list_element($course);
            foreach ($courseobj->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $courseimage = \moodle_url::make_file_url(
                        "$CFG->wwwroot/pluginfile.php",
                        "/{$file->get_contextid()}/{$file->get_component()}/{$file->get_filearea()}/{$file->get_itemid()}" . $file->get_filepath() . $file->get_filename()
                    )->out();
                    break;
                }
            }

            // Teachers.
            $coursecontext = \context_course::instance($r->id);
            $teacherfields = 'u.id, u.firstname, u.lastname, u.email, u.picture, u.imagealt, u.phone1, u.phone2';
            $teachersraw = get_enrolled_users($coursecontext, 'moodle/course:update', 0, $teacherfields, 'u.lastname, u.firstname', 0, 5);
            if (empty($teachersraw)) {
                $teachersraw = get_enrolled_users($coursecontext, 'moodle/grade:viewall', 0, $teacherfields, 'u.lastname, u.firstname', 0, 5);
            }

            $teachers = [];
            foreach ($teachersraw as $t) {
                $phone = !empty($t->phone1) ? $t->phone1 : (!empty($t->phone2) ? $t->phone2 : '');
                $cleanphone = preg_replace('/[^0-9]/', '', $phone);
                $whatsappurl = !empty($cleanphone) ? 'https://wa.me/' . $cleanphone : null;

                $teachers[] = [
                    'id'          => $t->id,
                    'fullname'    => fullname($t),
                    'email'       => $t->email,
                    'avatar'      => $OUTPUT->user_picture($t, ['size' => 36, 'link' => false]),
                    'avatarsmall' => $OUTPUT->user_picture($t, ['size' => 28, 'link' => false]),
                    'phone'       => $phone,
                    'hasphone'    => !empty($phone),
                    'whatsappurl' => $whatsappurl,
                    'messageurl'  => (new \moodle_url('/local/parentportal/index.php', [
                        'tab'       => 'messages',
                        'childid'   => $childid,
                        'teacherid' => $t->id,
                        'courseid'  => $r->id,
                    ]))->out(),
                ];
            }

            $r->fullname = format_string($r->fullname);
            $r->shortname = format_string($r->shortname);
            $r->categoryname = format_string($r->categoryname);
            $r->courseimage = $courseimage;
            $r->hasimage = !empty($courseimage);
            $r->progress = $progress;
            $r->hasprogress = ($progress !== null);
            $r->iscompleted = $iscompleted;
            $r->grade = $gradepct;
            $r->gradestr = $gradestr;
            $r->gradebadge = $gradebadge;
            $r->teachers = $teachers;
            $r->hasteachers = !empty($teachers);
            $r->firstteacher = !empty($teachers) ? reset($teachers) : null;
            $r->courseurl = (new \moodle_url('/course/view.php', ['id' => $r->id]))->out();
            $r->smartcourseurl = (new \moodle_url('/local/smartcoursepage/view.php', ['id' => $r->id]))->out();

            $courses[] = $r;
        }

        return $courses;
    }

    /**
     * Get upcoming deadlines and milestones for a child from Moodle calendar events.
     *
     * @param int $childid The child user ID.
     * @param int $limit Maximum number of events to return.
     * @return array Formatted event list.
     */
    public static function get_child_deadlines(int $childid, int $limit = 6): array {
        global $DB, $CFG;

        $now = time();
        $sql = "SELECT e.id, e.name, e.description, e.timestart, e.courseid, e.modulename, e.eventtype,
                       c.fullname AS coursename
                  FROM {event} e
                  LEFT JOIN {course} c ON c.id = e.courseid
                 WHERE (
                       e.userid = :childid
                       OR (e.courseid > 0 AND e.courseid IN (
                           SELECT DISTINCT en.courseid
                             FROM {enrol} en
                             JOIN {user_enrolments} ue ON ue.enrolid = en.id
                            WHERE ue.userid = :childid2
                              AND ue.status = :activestatus
                              AND en.status = :enrolstatus
                       ))
                 )
                   AND e.timestart >= :now
              ORDER BY e.timestart ASC";

        $events = $DB->get_records_sql($sql, [
            'childid'      => $childid,
            'childid2'     => $childid,
            'activestatus' => ENROL_USER_ACTIVE,
            'enrolstatus'  => ENROL_INSTANCE_ENABLED,
            'now'          => $now,
        ], 0, $limit);

        $formatted = [];
        foreach ($events as $e) {
            $icon = 'fa-calendar-alt text-primary';
            if ($e->modulename === 'assign') {
                $icon = 'fa-file-alt text-warning';
            } else if ($e->modulename === 'quiz') {
                $icon = 'fa-question-circle text-danger';
            } else if ($e->modulename === 'attendance') {
                $icon = 'fa-clock text-info';
            }

            $timeleft = $e->timestart - $now;
            $isurgent = ($timeleft <= 172800); // 48 hours or less.

            $formatted[] = [
                'id'            => $e->id,
                'name'          => format_string($e->name),
                'coursename'    => !empty($e->coursename) ? format_string($e->coursename) : '',
                'courseid'      => $e->courseid,
                'modulename'    => $e->modulename,
                'icon'          => $icon,
                'timestart'     => $e->timestart,
                'formatteddate' => userdate($e->timestart, get_string('strftimedatetime', 'langconfig')),
                'daydate'       => userdate($e->timestart, '%d %b'),
                'time'          => userdate($e->timestart, '%H:%M'),
                'timeuntil'     => format_time($timeleft),
                'isurgent'      => $isurgent,
                'url'           => (new \moodle_url('/calendar/view.php', ['view' => 'event', 'id' => $e->id]))->out(),
            ];
        }

        return $formatted;
    }

    /**
     * Get aggregate academic summary metrics for a child.
     *
     * @param int $childid The child user ID.
     * @return array
     */
    public static function get_child_academic_summary(int $childid): array {
        $courses = self::get_child_courses_progress($childid);
        $total = count($courses);
        $completed = 0;
        $inprogress = 0;
        $gradessum = 0;
        $gradescount = 0;

        foreach ($courses as $c) {
            if (!empty($c->iscompleted) || ($c->progress !== null && $c->progress >= 100)) {
                $completed++;
            } else {
                $inprogress++;
            }
            if ($c->grade !== null) {
                $gradessum += $c->grade;
                $gradescount++;
            }
        }

        $overallgrade = ($gradescount > 0) ? round($gradessum / $gradescount, 1) : null;

        return [
            'total_courses'      => $total,
            'completed_courses'  => $completed,
            'inprogress_courses' => $inprogress,
            'overall_grade'      => $overallgrade,
            'overall_grade_str'  => ($overallgrade !== null) ? $overallgrade . '%' : '--',
        ];
    }

    /**
     * Link an existing student account to the parent using credentials.
     *
     * @param int $parentid The parent user ID.
     * @param string $identifier Username or email of the child.
     * @param string $password The child user password.
     * @return array Result with success, message, and optional child object.
     */
    public static function link_existing_child(int $parentid, string $identifier, string $password): array {
        global $DB, $CFG;

        $identifier = trim($identifier);
        if (empty($identifier) || empty($password)) {
            return [
                'success' => false,
                'message' => get_string('error_invalid_credentials', 'local_parentportal'),
            ];
        }

        // Find user by username or email.
        $user = $DB->get_record_select('user', '(username = :u OR email = :e) AND deleted = 0', [
            'u' => $identifier,
            'e' => $identifier,
        ]);

        if (!$user) {
            return [
                'success' => false,
                'message' => get_string('error_invalid_credentials', 'local_parentportal'),
            ];
        }

        // Verify password against Moodle auth.
        require_once($CFG->libdir . '/authlib.php');
        $authuser = authenticate_user_login($user->username, $password);
        if (!$authuser) {
            return [
                'success' => false,
                'message' => get_string('error_invalid_credentials', 'local_parentportal'),
            ];
        }

        // Check if already linked.
        if ($DB->record_exists('local_parentportal_children', ['parentid' => $parentid, 'childid' => $user->id])) {
            return [
                'success' => false,
                'message' => get_string('error_already_linked', 'local_parentportal'),
            ];
        }

        // Link child.
        $record = new \stdClass();
        $record->parentid    = $parentid;
        $record->childid     = $user->id;
        $record->sex         = '';
        $record->grade       = '';
        $record->curriculum  = '';
        $record->timecreated = time();
        $DB->insert_record('local_parentportal_children', $record);

        // Assign parent role.
        try {
            self::assign_parent_role($parentid, $user->id);
        } catch (\Throwable $e) {
            // Role assignment error, but linkage record exists.
        }

        return [
            'success' => true,
            'message' => get_string('childlinked', 'local_parentportal', fullname($user)),
            'child'   => $user,
        ];
    }

    /**
     * Get real-time parent wallet balance via enrol_wallet plugin.
     *
     * @param int $parentid The parent user ID.
     * @return float
     */
    public static function get_parent_wallet_balance(int $parentid): float {
        global $CFG;

        if (file_exists($CFG->dirroot . '/enrol/wallet/locallib.php')) {
            require_once($CFG->dirroot . '/enrol/wallet/locallib.php');
            if (class_exists('\enrol_wallet\local\wallet\balance')) {
                try {
                    $balanceobj = new \enrol_wallet\local\wallet\balance($parentid);
                    return (float)$balanceobj->get_valid_balance();
                } catch (\Throwable $e) {
                    return 0.0;
                }
            }
        }

        return 0.0;
    }

    /**
     * Get wallet currency.
     *
     * @return string
     */
    public static function get_wallet_currency(): string {
        global $CFG;
        return get_config('enrol_wallet', 'currency') ?: ($CFG->currency ?? 'USD');
    }

    /**
     * Get enrollment cost and instance info for a course.
     *
     * @param int $courseid The course ID.
     * @return array Cost and instance details.
     */
    public static function get_course_enrol_cost(int $courseid): array {
        global $DB;

        // 1. Look for active enrol_wallet instance.
        $instance = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol'    => 'wallet',
            'status'   => ENROL_INSTANCE_ENABLED,
        ]);

        if ($instance) {
            return [
                'has_wallet' => true,
                'instanceid' => (int)$instance->id,
                'cost'       => (float)($instance->cost ?? 0.0),
                'currency'   => $instance->currency ?: self::get_wallet_currency(),
            ];
        }

        // 2. Check any other enrol instance with cost.
        $anycost = $DB->get_record_select('enrol', 'courseid = :courseid AND status = :status AND cost > 0', [
            'courseid' => $courseid,
            'status'   => ENROL_INSTANCE_ENABLED,
        ]);

        if ($anycost) {
            return [
                'has_wallet' => false,
                'instanceid' => (int)$anycost->id,
                'cost'       => (float)$anycost->cost,
                'currency'   => $anycost->currency ?: self::get_wallet_currency(),
            ];
        }

        // 3. Fallback: Free or standard.
        return [
            'has_wallet' => false,
            'instanceid' => 0,
            'cost'       => 0.0,
            'currency'   => self::get_wallet_currency(),
        ];
    }

    /**
     * Get Family Cart items stored in parent's session.
     *
     * @return array Enriched cart items list.
     */
    public static function get_cart(): array {
        global $SESSION, $DB, $OUTPUT;

        if (empty($SESSION->parentportal_cart) || !is_array($SESSION->parentportal_cart)) {
            $SESSION->parentportal_cart = [];
            return [];
        }

        $items = [];
        $index = 0;
        foreach ($SESSION->parentportal_cart as $entry) {
            $course = $DB->get_record('course', ['id' => $entry['courseid']], 'id, fullname, shortname');
            $child = $DB->get_record('user', ['id' => $entry['childid']], 'id, firstname, lastname, picture, imagealt, email');

            if (!$course || !$child) {
                continue;
            }

            $items[] = [
                'index'           => $index,
                'courseid'        => $course->id,
                'coursename'      => format_string($course->fullname),
                'courseshortname' => format_string($course->shortname),
                'childid'         => $child->id,
                'childname'       => fullname($child),
                'childavatar'     => $OUTPUT->user_picture($child, ['size' => 28, 'link' => false]),
                'amount'          => (float)$entry['amount'],
                'formattedamount' => number_format((float)$entry['amount'], 2),
                'currency'        => $entry['currency'] ?? self::get_wallet_currency(),
            ];
            $index++;
        }

        return $items;
    }

    /**
     * Add a course for a child into the session Family Cart.
     *
     * @param int $parentid The parent user ID.
     * @param int $courseid The course ID.
     * @param int $childid The child user ID.
     * @return array Result with status and message.
     */
    public static function add_to_cart(int $parentid, int $courseid, int $childid): array {
        global $SESSION, $DB;

        // Verify parent-child relationship.
        if (!$DB->record_exists('local_parentportal_children', ['parentid' => $parentid, 'childid' => $childid])) {
            return [
                'success' => false,
                'message' => get_string('error_noaccess', 'local_parentportal'),
            ];
        }

        $course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1]);
        if (!$course || $course->id == SITEID) {
            return [
                'success' => false,
                'message' => get_string('invalidcourse', 'error'),
            ];
        }

        $child = $DB->get_record('user', ['id' => $childid, 'deleted' => 0]);
        if (!$child) {
            return [
                'success' => false,
                'message' => get_string('invaliduser', 'error'),
            ];
        }

        // Check if child is already actively enrolled in this course.
        $sqlenrolled = "SELECT COUNT(ue.id)
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                         WHERE ue.userid = :childid
                           AND e.courseid = :courseid
                           AND ue.status = :activestatus";
        $isenrolled = $DB->count_records_sql($sqlenrolled, [
            'childid'      => $childid,
            'courseid'     => $courseid,
            'activestatus' => ENROL_USER_ACTIVE,
        ]);

        if ($isenrolled > 0) {
            $a = (object)[
                'child'  => fullname($child),
                'course' => format_string($course->fullname),
            ];
            return [
                'success' => false,
                'message' => get_string('cart_already_enrolled', 'local_parentportal', $a),
            ];
        }

        // Initialize session cart.
        if (empty($SESSION->parentportal_cart) || !is_array($SESSION->parentportal_cart)) {
            $SESSION->parentportal_cart = [];
        }

        // Check if item is already in cart.
        foreach ($SESSION->parentportal_cart as $item) {
            if ($item['courseid'] == $courseid && $item['childid'] == $childid) {
                return [
                    'success' => false,
                    'message' => get_string('cart_already_in_cart', 'local_parentportal'),
                ];
            }
        }

        // Determine price.
        $costinfo = self::get_course_enrol_cost($courseid);

        $SESSION->parentportal_cart[] = [
            'courseid'        => $courseid,
            'childid'         => $childid,
            'enrolinstanceid' => $costinfo['instanceid'],
            'amount'          => $costinfo['cost'],
            'currency'        => $costinfo['currency'],
            'timeadded'       => time(),
        ];

        $a = (object)[
            'child'  => fullname($child),
            'course' => format_string($course->fullname),
        ];

        return [
            'success' => true,
            'message' => get_string('cart_added', 'local_parentportal', $a),
        ];
    }

    /**
     * Remove an item from the Family Cart by its index.
     *
     * @param int $index The cart index to remove.
     * @return bool
     */
    public static function remove_from_cart(int $index): bool {
        global $SESSION;

        if (isset($SESSION->parentportal_cart[$index])) {
            unset($SESSION->parentportal_cart[$index]);
            $SESSION->parentportal_cart = array_values($SESSION->parentportal_cart);
            return true;
        }

        return false;
    }

    /**
     * Clear all items from the Family Cart.
     */
    public static function clear_cart(): void {
        global $SESSION;
        $SESSION->parentportal_cart = [];
    }

    /**
     * Calculate cart financial totals and checkout eligibility.
     *
     * @param int $parentid The parent user ID.
     * @return array Financial metrics.
     */
    public static function get_cart_totals(int $parentid): array {
        $cart = self::get_cart();
        $subtotal = 0.0;
        $currency = self::get_wallet_currency();

        foreach ($cart as $item) {
            $subtotal += (float)$item['amount'];
            if (!empty($item['currency'])) {
                $currency = $item['currency'];
            }
        }

        $walletbalance = self::get_parent_wallet_balance($parentid);
        $balanceafter = $walletbalance - $subtotal;
        $cancheckout = (count($cart) > 0 && $walletbalance >= $subtotal);
        $missing = ($subtotal > $walletbalance) ? round($subtotal - $walletbalance, 2) : 0.0;

        return [
            'items_count'        => count($cart),
            'subtotal'           => $subtotal,
            'formatted_subtotal' => number_format($subtotal, 2),
            'currency'           => $currency,
            'wallet_balance'     => $walletbalance,
            'formatted_wallet'   => number_format($walletbalance, 2),
            'balance_after'      => $balanceafter,
            'formatted_after'    => number_format($balanceafter, 2),
            'can_checkout'       => $cancheckout,
            'has_insufficient'   => ($subtotal > $walletbalance),
            'missing_amount'     => $missing,
            'formatted_missing'  => number_format($missing, 2),
        ];
    }

    /**
     * Checkout the Family Cart: Debits parent wallet and enrolls children atomically.
     *
     * @param int $parentid The parent user ID.
     * @return array Result of checkout.
     */
    public static function checkout_cart(int $parentid): array {
        global $DB, $CFG, $SESSION;

        $cart = self::get_cart();
        if (empty($cart)) {
            return [
                'success' => false,
                'message' => get_string('cart_empty', 'local_parentportal'),
            ];
        }

        $totals = self::get_cart_totals($parentid);
        if (!$totals['can_checkout']) {
            return [
                'success' => false,
                'message' => get_string('cart_insufficient_balance', 'local_parentportal'),
            ];
        }

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $transaction = $DB->start_delegated_transaction();

        $enrolledcount = 0;

        try {
            foreach ($cart as $item) {
                $courseid = $item['courseid'];
                $childid  = $item['childid'];
                $amount   = (float)$item['amount'];
                $course   = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
                $child    = $DB->get_record('user', ['id' => $childid], '*', MUST_EXIST);

                // Resolve course enrol instance.
                $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'wallet', 'status' => ENROL_INSTANCE_ENABLED]);
                $pluginname = 'wallet';

                if (!$instance) {
                    $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual', 'status' => ENROL_INSTANCE_ENABLED]);
                    $pluginname = 'manual';
                }

                if (!$instance) {
                    $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'status' => ENROL_INSTANCE_ENABLED]);
                    $pluginname = $instance ? $instance->enrol : 'manual';
                }

                // 1. Debit parent from enrol_wallet if amount > 0.
                if ($amount > 0 && file_exists($CFG->dirroot . '/enrol/wallet/locallib.php')) {
                    require_once($CFG->dirroot . '/enrol/wallet/locallib.php');
                    $categoryid = $course->category ?? 0;
                    $op = new \enrol_wallet\local\wallet\balance_op($parentid, $categoryid);

                    if ($pluginname === 'wallet' && $instance) {
                        $thingid = (int)$instance->id;
                        $debitby = \enrol_wallet\local\wallet\balance_op::D_ENROL_INSTANCE;
                    } else {
                        $thingid = (int)$courseid;
                        $debitby = \enrol_wallet\local\wallet\balance_op::D_ENROL_COURSE;
                    }

                    $debited = $op->debit(
                        $amount,
                        $debitby,
                        $thingid,
                        "Family purchase: {$course->shortname} for child {$child->id}"
                    );

                    if (!$debited) {
                        throw new \moodle_exception('cart_insufficient_balance', 'local_parentportal');
                    }
                }

                // 2. Perform enrollment for the child.

                if ($instance) {
                    $plugin = enrol_get_plugin($pluginname);
                    if ($plugin) {
                        $roleid = $instance->roleid ?: 5; // Student.
                        $plugin->enrol_user($instance, $childid, $roleid, time(), 0, null, true);
                    }
                }

                // 3. Record order in local_parentportal_orders.
                if ($DB->get_manager()->table_exists('local_parentportal_orders')) {
                    $order = new \stdClass();
                    $order->parentid        = $parentid;
                    $order->childid         = $childid;
                    $order->courseid        = $courseid;
                    $order->enrolinstanceid = $instance ? $instance->id : null;
                    $order->amount          = $amount;
                    $order->currency        = $item['currency'] ?? self::get_wallet_currency();
                    $order->paymentmethod   = 'wallet';
                    $order->status          = 'completed';
                    $order->timecreated     = time();
                    $DB->insert_record('local_parentportal_orders', $order);
                }

                $enrolledcount++;
            }

            $transaction->allow_commit();

            // Clear session cart on success.
            self::clear_cart();

            return [
                'success' => true,
                'count'   => $enrolledcount,
                'message' => get_string('checkout_success', 'local_parentportal', $enrolledcount),
            ];
        } catch (\Throwable $e) {
            $transaction->rollback($e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get billing and order history for a parent.
     *
     * @param int $parentid The parent user ID.
     * @return array Orders list.
     */
    public static function get_parent_orders(int $parentid): array {
        global $DB;

        if (!$DB->get_manager()->table_exists('local_parentportal_orders')) {
            return [];
        }

        $sql = "SELECT o.id, o.parentid, o.childid, o.courseid, o.amount, o.currency,
                       o.paymentmethod, o.status, o.timecreated,
                       c.fullname AS coursename,
                       u.firstname AS childfirstname, u.lastname AS childlastname
                  FROM {local_parentportal_orders} o
                  JOIN {course} c ON c.id = o.courseid
                  JOIN {user} u ON u.id = o.childid
                 WHERE o.parentid = :parentid
              ORDER BY o.timecreated DESC";

        $records = $DB->get_records_sql($sql, ['parentid' => $parentid]);
        $orders = [];

        foreach ($records as $r) {
            $r->coursename = format_string($r->coursename);
            $r->childname  = fullname((object)['firstname' => $r->childfirstname, 'lastname' => $r->childlastname]);
            $r->formattedamount = number_format((float)$r->amount, 2) . ' ' . $r->currency;
            $r->formatteddate   = userdate($r->timecreated, get_string('strftimedate', 'langconfig'));
            $r->statusbadge     = ($r->status === 'completed') ? 'bg-success' : 'bg-secondary';
            $orders[] = $r;
        }

        return $orders;
    }

    /**
     * Check if a user is a confirmed parent of a specific child.
     *
     * @param int $parentid The parent user ID.
     * @param int $childid The child user ID.
     * @return bool
     */
    public static function is_parent_of(int $parentid, int $childid): bool {
        global $DB;
        return $DB->record_exists('local_parentportal_children', [
            'parentid' => $parentid,
            'childid'  => $childid,
        ]);
    }

    /**
     * Get all teachers instructing courses that a child is enrolled in.
     *
     * @param int $childid The child user ID.
     * @return array Unique list of teachers with course details and contact links.
     */
    public static function get_child_teachers(int $childid): array {
        global $DB, $OUTPUT;

        // 1. Get all active courses the child is enrolled in.
        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname
                  FROM {course} c
                  JOIN {enrol} e ON e.courseid = c.id
                  JOIN {user_enrolments} ue ON ue.enrolid = e.id
                 WHERE ue.userid = :childid
                   AND c.id <> :siteid
                   AND ue.status = :status
              ORDER BY c.fullname ASC";

        $courses = $DB->get_records_sql($sql, [
            'childid' => $childid,
            'siteid'  => SITEID,
            'status'  => ENROL_USER_ACTIVE,
        ]);

        if (empty($courses)) {
            return [];
        }

        $teacherfields = 'u.id, u.firstname, u.lastname, u.email, u.picture, u.imagealt, u.phone1, u.phone2';
        $teachersbyid = [];

        foreach ($courses as $c) {
            $coursecontext = \context_course::instance($c->id);
            $rawteachers = get_enrolled_users($coursecontext, 'moodle/course:update', 0, $teacherfields, 'u.lastname, u.firstname');
            if (empty($rawteachers)) {
                $rawteachers = get_enrolled_users($coursecontext, 'moodle/grade:viewall', 0, $teacherfields, 'u.lastname, u.firstname');
            }

            foreach ($rawteachers as $t) {
                if (!isset($teachersbyid[$t->id])) {
                    $phone = !empty($t->phone1) ? $t->phone1 : (!empty($t->phone2) ? $t->phone2 : '');
                    $cleanphone = preg_replace('/[^0-9]/', '', $phone);
                    $whatsappurl = !empty($cleanphone) ? 'https://wa.me/' . $cleanphone : null;

                    $teachersbyid[$t->id] = (object)[
                        'id'          => $t->id,
                        'fullname'    => fullname($t),
                        'firstname'   => $t->firstname,
                        'email'       => $t->email,
                        'phone'       => $phone,
                        'hasphone'    => !empty($phone),
                        'whatsappurl' => $whatsappurl,
                        'avatar'      => $OUTPUT->user_picture($t, ['size' => 60, 'link' => false]),
                        'avatarsmall' => $OUTPUT->user_picture($t, ['size' => 32, 'link' => false]),
                        'moodlechat'  => (new \moodle_url('/message/index.php', ['id' => $t->id]))->out(false),
                        'courses'     => [],
                    ];
                }

                $teachersbyid[$t->id]->courses[] = [
                    'id'        => $c->id,
                    'fullname'  => format_string($c->fullname),
                    'shortname' => format_string($c->shortname),
                ];
            }
        }

        foreach ($teachersbyid as &$teacher) {
            $teacher->coursescount = count($teacher->courses);
            $teacher->coursesnames = implode(', ', array_column($teacher->courses, 'fullname'));
        }

        return array_values($teachersbyid);
    }

    /**
     * Send an inquiry message from parent to teacher regarding a child.
     *
     * @param int $parentid The parent user ID.
     * @param int $teacherid The teacher user ID.
     * @param int $childid The child user ID.
     * @param int $courseid The course ID.
     * @param string $subject The message subject.
     * @param string $body The message body.
     * @return array Result with status and message.
     */
    public static function send_teacher_inquiry(
        int $parentid,
        int $teacherid,
        int $childid,
        int $courseid,
        string $subject,
        string $body
    ): array {
        global $DB;

        if (!self::is_parent_of($parentid, $childid)) {
            return [
                'success' => false,
                'message' => get_string('error_notparent', 'local_parentportal'),
            ];
        }

        $teacher = $DB->get_record('user', ['id' => $teacherid, 'deleted' => 0]);
        if (!$teacher) {
            return [
                'success' => false,
                'message' => get_string('error_invalidteacher', 'local_parentportal'),
            ];
        }

        $child = $DB->get_record('user', ['id' => $childid, 'deleted' => 0]);
        $course = $DB->get_record('course', ['id' => $courseid]);

        $childname = fullname($child);
        $coursename = $course ? format_string($course->fullname) : '';

        // Prepare message header and body.
        $header = "📢 **[" . get_string('pluginname', 'local_parentportal') . "]**\n"
                . "👤 **" . get_string('child', 'local_parentportal') . ":** {$childname}\n"
                . "📚 **" . get_string('course') . ":** {$coursename}\n";

        if (!empty(trim($subject))) {
            $header .= "📌 **" . get_string('subject', 'local_parentportal') . ":** " . trim($subject) . "\n";
        }

        $fullmessage = $header . "\n" . trim($body);

        $parent = $DB->get_record('user', ['id' => $parentid, 'deleted' => 0]);
        if (!$parent) {
            return [
                'success' => false,
                'message' => get_string('error_notparent', 'local_parentportal'),
            ];
        }

        try {
            $eventdata = new \core\message\message();
            $eventdata->component         = 'moodle';
            $eventdata->name              = 'instantmessage';
            $eventdata->userfrom          = $parent;
            $eventdata->userto            = $teacher;
            $eventdata->subject           = !empty($subject) ? $subject : get_string('messages_title', 'local_parentportal');
            $eventdata->fullmessage       = $fullmessage;
            $eventdata->fullmessageformat = FORMAT_MARKDOWN;
            $eventdata->fullmessagehtml   = markdown_to_html($fullmessage);
            $eventdata->smallmessage      = !empty($subject) ? $subject : substr($body, 0, 100);
            $eventdata->notification      = 0;
            $eventdata->courseid          = $courseid;

            $messageid = message_send($eventdata);

            if ($messageid) {
                return [
                    'success'   => true,
                    'message'   => get_string('message_sent_success', 'local_parentportal', fullname($teacher)),
                    'messageid' => $messageid,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to deliver message via Moodle messaging system.',
                ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
