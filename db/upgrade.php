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
 * Upgrade steps for the parent portal plugin.
 *
 * @package     local_parentportal
 * @copyright   2025 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for local_parentportal.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool True on success.
 */
function xmldb_local_parentportal_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025042801) {

        // --- Table: local_parentportal_student ---
        $table = new xmldb_table('local_parentportal_student');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('childid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('nationality', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('passportid', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('city', XMLDB_TYPE_CHAR, '120', null, null, null, null);
        $table->add_field('state', XMLDB_TYPE_CHAR, '120', null, null, null, null);
        $table->add_field('zipcode', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('telephone', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('healthconditions', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('medication', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('recentschool', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('refnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('studentphone', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('childid_fk', XMLDB_KEY_FOREIGN_UNIQUE, ['childid'], 'user', ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --- Table: local_parentportal_parentinfo ---
        $table = new xmldb_table('local_parentportal_parentinfo');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('childid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parentnum', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('legalguardian', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('nationality', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('passportid', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('countryofresidence', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('educationlevel', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('occupation', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('phone1', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('phone2', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('childid_fk', XMLDB_KEY_FOREIGN, ['childid'], 'user', ['id']);

        $table->add_index('childid_parentnum_idx', XMLDB_INDEX_UNIQUE, ['childid', 'parentnum']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --- Table: local_parentportal_guardian ---
        $table = new xmldb_table('local_parentportal_guardian');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('childid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('relationship', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('nationality', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('passportid', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('countryofresidence', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('educationlevel', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('occupation', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('phone1', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('phone2', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('childid_fk', XMLDB_KEY_FOREIGN_UNIQUE, ['childid'], 'user', ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --- Table: local_parentportal_emergency ---
        $table = new xmldb_table('local_parentportal_emergency');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('childid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('relationship', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('phone', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('childid_fk', XMLDB_KEY_FOREIGN_UNIQUE, ['childid'], 'user', ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025042801, 'local', 'parentportal');
    }

    return true;
}
