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
 * Main page for the parent portal - Teaching Center Family Hub.
 *
 * Single-Page Application controller featuring 5 main tabs:
 * - Dashboard (Executive family summary & active child KPIs)
 * - My Children (Account roster, Add child modal, Link child modal)
 * - Academics (Course progress, grades, report_studentgrades & smartdashboard launchers)
 * - Finances & Wallet (Family wallet balance via enrol_wallet, Cart & billing)
 * - Messages (Teacher directory per child, direct inquiries, WhatsApp)
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

$parentid = (int)$USER->id;

// Parameters.
$tab = optional_param('tab', 'dashboard', PARAM_ALPHA);
$validtabs = ['dashboard', 'children', 'academics', 'finances', 'messages'];
if (!in_array($tab, $validtabs)) {
    $tab = 'dashboard';
}

$childidparam = optional_param('childid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUMEXT);

// ── Early Page Setup (Required by Moodle before form processing, formatting, or redirects) ──
$pageparams = ['tab' => $tab];
if ($childidparam > 0) {
    $pageparams['childid'] = $childidparam;
}
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/parentportal/index.php', $pageparams));

// ── Action: Link Existing Child ──────────────────────────────────────────────
if ($action === 'linkchild' && data_submitted() && confirm_sesskey()) {
    $identifier = required_param('identifier', PARAM_RAW);
    $password   = required_param('password', PARAM_RAW);

    $res = manager::link_existing_child($parentid, $identifier, $password);
    if ($res['success']) {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_SUCCESS);
        $redirectparams = ['tab' => 'children'];
        if (!empty($res['child'])) {
            $redirectparams['childid'] = $res['child']->id;
        }
        redirect(new moodle_url('/local/parentportal/index.php', $redirectparams));
    } else {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
        redirect(new moodle_url('/local/parentportal/index.php', ['tab' => $tab]));
    }
}

// ── Action: Add to Family Cart ───────────────────────────────────────────────
if ($action === 'addtocart' && confirm_sesskey()) {
    $courseid = required_param('courseid', PARAM_INT);
    $childid  = optional_param('childid', 0, PARAM_INT);
    $childids = optional_param_array('childids', [], PARAM_INT);

    if (!empty($childids)) {
        $added = 0;
        foreach ($childids as $cid) {
            $cid = (int)$cid;
            if ($cid > 0) {
                $res = manager::add_to_cart($parentid, $courseid, $cid);
                if ($res['success']) {
                    $added++;
                } else {
                    \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
                }
            }
        }
        if ($added > 0) {
            \core\notification::add(get_string('courses_added_to_cart', 'local_parentportal', $added), \core\output\notification::NOTIFY_SUCCESS);
        }
    } else {
        if ($childid <= 0) {
            $activechild = manager::get_active_child($parentid);
            if ($activechild) {
                $childid = (int)$activechild->id;
            }
        }

        if ($childid > 0) {
            $res = manager::add_to_cart($parentid, $courseid, $childid);
            if ($res['success']) {
                \core\notification::add($res['message'], \core\output\notification::NOTIFY_SUCCESS);
            } else {
                \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
            }
        } else {
            \core\notification::add(get_string('nochildren', 'local_parentportal'), \core\output\notification::NOTIFY_WARNING);
        }
    }
    redirect(new moodle_url('/local/parentportal/index.php', ['tab' => 'finances']));
}

// ── Action: Remove from Family Cart ──────────────────────────────────────────
if ($action === 'removefromcart' && confirm_sesskey()) {
    $cartindex = required_param('cartindex', PARAM_INT);

    if (manager::remove_from_cart($cartindex)) {
        \core\notification::add(get_string('cart_item_removed', 'local_parentportal'), \core\output\notification::NOTIFY_INFO);
    }
    redirect(new moodle_url('/local/parentportal/index.php', ['tab' => 'finances']));
}

// ── Action: Clear Family Cart ────────────────────────────────────────────────
if ($action === 'clearcart' && confirm_sesskey()) {
    manager::clear_cart();
    \core\notification::add(get_string('cart_cleared', 'local_parentportal'), \core\output\notification::NOTIFY_INFO);
    redirect(new moodle_url('/local/parentportal/index.php', ['tab' => 'finances']));
}

// ── Action: Checkout Cart (Wallet Debit + Enrollments) ───────────────────────
if ($action === 'checkout' && confirm_sesskey()) {
    $res = manager::checkout_cart($parentid);
    if ($res['success']) {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_SUCCESS);
    } else {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
    }
    redirect(new moodle_url('/local/parentportal/index.php', ['tab' => 'finances']));
}

// ── Action: Send Teacher Inquiry ─────────────────────────────────────────────
if ($action === 'sendinquiry' && confirm_sesskey()) {
    $teacherid = required_param('teacherid', PARAM_INT);
    $childid   = required_param('childid', PARAM_INT);
    $courseid  = required_param('courseid', PARAM_INT);
    $subject   = optional_param('subject', '', PARAM_TEXT);
    $body      = required_param('body', PARAM_RAW);

    $res = manager::send_teacher_inquiry($parentid, $teacherid, $childid, $courseid, $subject, $body);
    if ($res['success']) {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_SUCCESS);
    } else {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
    }
    redirect(new moodle_url('/local/parentportal/index.php', [
        'tab'     => 'messages',
        'childid' => $childid,
    ]));
}

// ── Action: Submit Absence Excuse Note ───────────────────────────────────────
if ($action === 'submitexcuse' && confirm_sesskey()) {
    $childid       = required_param('childid', PARAM_INT);
    $courseid      = optional_param('courseid', 0, PARAM_INT);
    $startdate_raw = optional_param('startdate', '', PARAM_RAW_TRIMMED);
    $enddate_raw   = optional_param('enddate', '', PARAM_RAW_TRIMMED);
    $reason        = optional_param('reason', 'medical', PARAM_ALPHA);
    $details       = optional_param('details', '', PARAM_TEXT);

    $startdate = !empty($startdate_raw) ? strtotime($startdate_raw) : time();
    $enddate   = !empty($enddate_raw) ? strtotime($enddate_raw) : $startdate;

    $res = manager::submit_absence_excuse(
        $parentid,
        $childid,
        $courseid,
        $startdate,
        $enddate,
        $reason,
        $details
    );

    if ($res['success']) {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_SUCCESS);
    } else {
        \core\notification::add($res['message'], \core\output\notification::NOTIFY_ERROR);
    }
    redirect(new moodle_url('/local/parentportal/index.php', [
        'tab'     => 'academics',
        'childid' => $childid,
    ]));
}

// ── Action: Add Child Form Processing ────────────────────────────────────────
$formurl = new moodle_url('/local/parentportal/index.php', ['tab' => $tab]);
$addform = new add_child_form($formurl);

if ($addform->is_cancelled()) {
    redirect(new moodle_url('/local/parentportal/index.php', ['tab' => $tab]));
} else if ($formdata = $addform->get_data()) {
    try {
        $child = manager::create_child($formdata, $parentid);
        $fullname = fullname($child);
        $message = get_string('childcreated', 'local_parentportal', $fullname);
        \core\notification::add($message, \core\output\notification::NOTIFY_SUCCESS);
        redirect(new moodle_url('/local/parentportal/index.php', ['tab' => 'children', 'childid' => $child->id]));
    } catch (\Exception $e) {
        \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
        redirect(new moodle_url('/local/parentportal/index.php', ['tab' => $tab]));
    }
}

// ── Data Resolution ──────────────────────────────────────────────────────────
$children = manager::get_children($parentid);
$haschildren = !empty($children);
$activechild = manager::get_active_child($parentid, $childidparam);

$childrenlist = [];
foreach ($children as $c) {
    $c->isactive = ($activechild && $activechild->childid == $c->childid);
    $childrenlist[] = $c;
}

$activechildkpis = null;
$childcourses = [];
$haschildcourses = false;
$childdeadlines = [];
$haschilddeadlines = false;
$childattendance = null;
$academicsummary = null;
$childteachers = [];
$haschildteachers = false;
$childexcuses = [];
$haschildexcuses = false;
$targetteacherid = optional_param('teacherid', 0, PARAM_INT);
$targetcourseid  = optional_param('courseid', 0, PARAM_INT);

if ($activechild) {
    $activechildkpis = manager::get_child_kpis($activechild->childid);
    $childcourses = manager::get_child_courses_progress($activechild->childid);
    $haschildcourses = !empty($childcourses);
    $childdeadlines = manager::get_child_deadlines($activechild->childid);
    $haschilddeadlines = !empty($childdeadlines);
    $childattendance = manager::get_child_attendance($activechild->childid);
    $academicsummary = manager::get_child_academic_summary($activechild->childid);
    $childteachers = manager::get_child_teachers($activechild->childid);
    $haschildteachers = !empty($childteachers);
    $childexcuses = manager::get_child_excuses($activechild->childid, $parentid);
    $haschildexcuses = !empty($childexcuses);
}

$walletbalance = manager::get_parent_wallet_balance($parentid);
$walletcurrency = manager::get_wallet_currency();

// Family Cart & Billing Data.
$cartitems = manager::get_cart();
$hascartitems = !empty($cartitems);
$carttotals = manager::get_cart_totals($parentid);
$parentorders = manager::get_parent_orders($parentid);
$hasorders = !empty($parentorders);

// Inquiries & Feedback History.
$inquiries = manager::get_parent_inquiries($parentid, $activechild ? $activechild->childid : null);
$hasinquiries = !empty($inquiries);

// Render add child form into string for modal.
ob_start();
$addform->display();
$addchildformhtml = ob_get_clean();

// ── Page Presentation ───────────────────────────────────────────────────────
$PAGE->set_title(get_string('pagetitle', 'local_parentportal'));
$PAGE->set_heading(get_string('pagetitle', 'local_parentportal'));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/parentportal/styles.css');

// ── Template Context ─────────────────────────────────────────────────────────
$templatecontext = [
    'parentname'        => fullname($USER),
    'walletbalance'     => number_format($walletbalance, 2),
    'walletcurrency'    => $walletcurrency,
    'currenttab'        => $tab,
    'is_tab_dashboard'  => ($tab === 'dashboard'),
    'is_tab_children'   => ($tab === 'children'),
    'is_tab_academics'  => ($tab === 'academics'),
    'is_tab_finances'   => ($tab === 'finances'),
    'is_tab_messages'   => ($tab === 'messages'),
    'haschildren'       => $haschildren,
    'childrenlist'      => $childrenlist,
    'activechild'       => $activechild,
    'activechildkpis'   => $activechildkpis,
    'childcourses'      => $childcourses,
    'haschildcourses'   => $haschildcourses,
    'childdeadlines'    => $childdeadlines,
    'haschilddeadlines' => $haschilddeadlines,
    'childattendance'   => $childattendance,
    'academicsummary'   => $academicsummary,
    'childteachers'     => $childteachers,
    'haschildteachers'  => $haschildteachers,
    'teacherscount'     => count($childteachers),
    'targetteacherid'   => $targetteacherid,
    'targetcourseid'    => $targetcourseid,
    'cartitems'         => $cartitems,
    'hascartitems'      => $hascartitems,
    'carttotals'        => $carttotals,
    'parentorders'      => $parentorders,
    'hasorders'         => $hasorders,
    'inquiries'         => $inquiries,
    'hasinquiries'      => $hasinquiries,
    'childexcuses'      => $childexcuses,
    'haschildexcuses'   => $haschildexcuses,
    'today_date'        => date('Y-m-d'),
    'addchildformhtml'  => $addchildformhtml,
    'sesskey'           => sesskey(),
    'config'            => [
        'wwwroot' => $CFG->wwwroot,
    ],
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_parentportal/portal_layout', $templatecontext);
echo $OUTPUT->footer();
