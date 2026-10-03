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
$string['addnewchild'] = 'Add new child';
$string['mychildren'] = 'My children';
$string['nochildren'] = 'You have not added any children yet.';

// Tabs.
$string['tab_dashboard'] = 'Dashboard';
$string['tab_children'] = 'My children';
$string['tab_academics'] = 'Academics';
$string['tab_finances'] = 'Finances & Wallet';
$string['tab_messages'] = 'Messages';

// Header & Navigation.
$string['welcomeparent'] = 'Welcome, {$a}';
$string['familyoverview'] = 'Teaching Center Family Hub';
$string['activechild'] = 'Active child';
$string['switchchild'] = 'Switch child';
$string['allchildren'] = 'All children';
$string['walletbalance'] = 'Wallet balance';
$string['topupwallet'] = 'Top up';
$string['viewstudentcard'] = 'View card';

// Dashboard KPIs & Actions.
$string['kpi_enrolledcourses'] = 'Enrolled courses';
$string['kpi_averagegrade'] = 'Average grade';
$string['kpi_attendance'] = 'Attendance rate';
$string['kpi_deadlines'] = 'Upcoming deadlines';
$string['kpi_nodeadlines'] = 'No upcoming deadlines';
$string['quickactions'] = 'Quick actions';
$string['browsecourses'] = 'Browse courses';
$string['viewgradebook'] = 'Grade report';
$string['askteacher'] = 'Ask teacher';
$string['activechilddetails'] = 'Active child details';
$string['upcomingdeadlines'] = 'Upcoming deadlines';
$string['recentprogress'] = 'Recent academic summary';

// Children Management.
$string['child'] = 'Child';
$string['linkchild'] = 'Link existing child';
$string['linkchild_desc'] = 'If your child already has an account created by the school, enter their username or email along with their password to link them to your family account.';
$string['childusernameoremail'] = 'Child username or email';
$string['childpassword'] = 'Child password';
$string['childlinked'] = 'Child "{$a}" has been linked to your account successfully.';
$string['error_already_linked'] = 'This child account is already linked to your account.';
$string['error_invalid_credentials'] = 'Could not verify child credentials. Please check the username/email and password.';
$string['nocoursesenrolled'] = 'Not enrolled in any courses yet.';
$string['enrolledin'] = '{$a} courses enrolled';
$string['managechildren'] = 'Manage children';
$string['childroster'] = 'Children roster';
$string['editdetails'] = 'Edit details';
$string['cancel'] = 'Cancel';
$string['close'] = 'Close';
$string['save'] = 'Save';

// Tab placeholders.
$string['academics_title'] = 'Academic progress & performance';
$string['academics_desc'] = 'Track course progress, grades, and access comprehensive student transcripts and AI analytics.';
$string['academics_summary'] = 'Academic summary';
$string['total_courses'] = 'Enrolled courses';
$string['completed_courses'] = 'Completed';
$string['inprogress_courses'] = 'In progress';
$string['overall_average_grade'] = 'Overall average grade';
$string['current_grade'] = 'Current grade';
$string['course_completion'] = 'Course completion';
$string['course_instructors'] = 'Course teachers';
$string['no_instructors'] = 'No teacher assigned';
$string['gotocourse'] = 'Enter course';
$string['viewcoursedetails'] = 'Course info';
$string['report_gradebook_title'] = 'Academic transcript & GPA';
$string['report_gradebook_desc'] = 'View multi-course grade breakdown, detailed criteria, and export official PDF transcripts.';
$string['report_gradebook_btn'] = 'View transcript';
$string['report_ai_title'] = 'AI academic insights';
$string['report_ai_desc'] = 'Generate intelligent performance analysis, student strengths, and tailored study recommendations.';
$string['report_ai_btn'] = 'Run AI evaluation';
$string['report_analytics_title'] = '360° visual analytics';
$string['report_analytics_desc'] = 'Explore interactive subject mastery radars, grade progression curves, and engagement heatmaps.';
$string['report_analytics_btn'] = 'Open Smart Dashboard';
$string['deadlines_title'] = 'Upcoming deadlines';
$string['deadlines_desc'] = 'Pending assignments, quizzes, and course milestones.';
$string['nodeadlines_title'] = 'All caught up!';
$string['nodeadlines_desc'] = 'No upcoming assignments or deadlines scheduled for this child.';
$string['due_soon'] = 'Due soon';
$string['attendance_summary'] = 'Attendance summary';
$string['attendance_sessions'] = 'Recorded sessions';
$string['attendance_present'] = 'Present';
$string['attendance_absent'] = 'Absent';
$string['attendance_status_excellent'] = 'Excellent';
$string['attendance_status_good'] = 'Good';
$string['attendance_status_attention'] = 'Needs Attention';
$string['nocourses_title'] = 'No courses enrolled yet';
$string['nocourses_desc'] = 'Your child is not currently enrolled in any courses. Explore our course catalog to find the right learning path.';
$string['explorecourses'] = 'Explore courses';
// Finances & Family Cart.
$string['finances_title'] = 'Family wallet & course purchases';
$string['finances_desc'] = 'Manage your family wallet balance, view cart items, and purchase courses for your children.';
$string['family_wallet_title'] = 'Family wallet balance';
$string['family_wallet_desc'] = 'Prepaid wallet balance used for instant course enrollments across all children.';
$string['topup_btn'] = 'Top up wallet';
$string['family_cart_title'] = 'Family Enrollment Cart';
$string['family_cart_desc'] = 'Courses selected for your children awaiting enrollment.';
$string['cart_empty_title'] = 'Your family cart is empty';
$string['cart_empty_desc'] = 'Add courses for your children from the course catalog or course pages to enroll them together in one transaction.';
$string['cart_subtotal'] = 'Cart subtotal';
$string['cart_balance_after'] = 'Remaining balance after purchase';
$string['cart_insufficient_warning'] = 'Your wallet balance is insufficient for this purchase. Please top up at least {$a} to proceed.';
$string['cart_checkout_btn'] = 'Pay from Wallet & Enroll Children';
$string['cart_added'] = 'Added "{$a->course}" for {$a->child} to your family cart.';
$string['courses_added_to_cart'] = 'Added {$a} item(s) to your family cart.';
$string['cart_already_in_cart'] = 'This course is already in your family cart for this child.';
$string['cart_already_enrolled'] = '{$a->child} is already enrolled in {$a->course}.';
$string['cart_item_removed'] = 'Course removed from family cart.';
$string['cart_cleared'] = 'Family cart has been cleared.';
$string['cart_empty'] = 'Your family cart is empty.';
$string['cart_insufficient_balance'] = 'Insufficient wallet balance to complete enrollment. Please top up your wallet.';
$string['checkout_success'] = 'Successfully purchased and enrolled {$a} course(s) for your children!';
$string['billing_history_title'] = 'Billing & Order History';
$string['no_orders_recorded'] = 'No previous purchase transactions recorded yet.';
$string['order_date'] = 'Date';
$string['order_child'] = 'Child';
$string['order_course'] = 'Course';
$string['order_amount'] = 'Amount';
$string['order_status'] = 'Status';
$string['order_method'] = 'Method';
$string['order_completed'] = 'Enrolled';
$string['action_remove'] = 'Remove';
$string['action_clearcart'] = 'Clear cart';
$string['messages_title'] = 'Teacher communication';
$string['messages_desc'] = 'Connect directly with teachers instructing your children.';
$string['teachers_directory'] = 'Teachers directory';
$string['teachers_directory_desc'] = 'Direct contact and inquiry channels with instructors teaching {$a}.';
$string['send_message'] = 'Send message';
$string['contact_teacher'] = 'Contact teacher';
$string['chat_whatsapp'] = 'WhatsApp';
$string['open_moodle_chat'] = 'Moodle chat';
$string['inquiry_modal_title'] = 'Send inquiry to teacher';
$string['inquiry_modal_desc'] = 'Your message will be sent directly to the teacher\'s Moodle inbox and email, tagged with student and course details.';
$string['regarding_child'] = 'Regarding student';
$string['select_course'] = 'Related course';
$string['subject'] = 'Subject';
$string['subject_placeholder'] = 'e.g. Question regarding upcoming quiz or progress';
$string['message_body'] = 'Message';
$string['message_placeholder'] = 'Write your inquiry or question to the teacher here...';
$string['send_inquiry_btn'] = 'Send inquiry';
$string['message_sent_success'] = 'Your message has been sent to {$a} successfully.';
$string['no_teachers_found'] = 'No instructors found';
$string['no_teachers_desc'] = 'No teachers have been assigned yet to your child\'s enrolled courses.';
$string['error_notparent'] = 'You are not authorized to perform actions for this student.';
$string['error_invalidteacher'] = 'The selected teacher could not be found.';
$string['courses_taught'] = 'Courses taught';

// Inquiries History (Tab 5).
$string['inquiry_history_title'] = 'Inquiry & Feedback History';
$string['inquiry_history_desc'] = 'Record of sent inquiries and teacher replies.';
$string['inquiry_status_pending'] = 'Awaiting reply';
$string['inquiry_status_replied'] = 'Replied';
$string['inquiry_status_closed'] = 'Closed';
$string['inquiry_no_history'] = 'No inquiries sent yet.';
$string['inquiry_sent_on'] = 'Sent on {$a}';
$string['inquiry_replied_on'] = 'Replied on {$a}';
$string['teacher_reply'] = 'Teacher reply';
$string['view_inquiry'] = 'View details';

// Absence & Excuse Requests (Tab 3).
$string['absence_request_title'] = 'Absence & Excuse Requests';
$string['submit_absence_btn'] = 'Submit Absence Request';
$string['absence_modal_title'] = 'Submit absence notice';
$string['absence_modal_desc'] = 'Notify the school and course teachers of an excused absence for your child.';
$string['absence_startdate'] = 'Start date';
$string['absence_enddate'] = 'End date';
$string['absence_reason'] = 'Reason';
$string['absence_reason_medical'] = 'Illness / Medical';
$string['absence_reason_family'] = 'Family Emergency';
$string['absence_reason_travel'] = 'Authorized Travel';
$string['absence_reason_appointment'] = 'Official Appointment';
$string['absence_reason_other'] = 'Other';
$string['absence_details'] = 'Details / Note';
$string['absence_details_placeholder'] = 'Explain the reason for absence...';
$string['absence_submitted_success'] = 'Absence request submitted successfully.';
$string['absence_history_title'] = 'Previous Absence Notices';
$string['absence_no_history'] = 'No absence requests submitted.';
$string['absence_status_submitted'] = 'Under review';
$string['absence_status_approved'] = 'Approved';
$string['absence_status_acknowledged'] = 'Acknowledged';
$string['absence_status_rejected'] = 'Declined';
$string['absence_course'] = 'Affected course';
$string['absence_all_courses'] = 'All courses (Full day)';

// Catalog Quick Buy.
$string['buy_for_child'] = 'Buy for Child';
$string['select_children_to_enroll'] = 'Select children to enroll';
$string['already_enrolled'] = 'Already enrolled';
$string['in_cart'] = 'In cart';

// Form fields - add child.
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
$string['error_noaccess'] = 'You do not have permission to access this student card.';

// Children table headers.
$string['childname'] = 'Name';
$string['childemail'] = 'Email';
$string['childgrade'] = 'Grade';
$string['childcurriculum'] = 'Curriculum';
$string['dateadded'] = 'Date added';
$string['actions'] = 'Actions';
$string['studentcard'] = 'Student card';
$string['editcard'] = 'Edit card';
$string['viewcard'] = 'View card';

// Student card page.
$string['studentcardfor'] = 'Student card for {$a}';
$string['editstudentcard'] = 'Edit student card';
$string['savecard'] = 'Save card';
$string['cardsaved'] = 'Student card has been saved successfully.';
$string['nocarddata'] = 'No information has been entered yet. Click "Edit card" to fill in the student card.';
$string['noacademicdata'] = 'No academic records available.';

// Student card sections.
$string['section_general'] = 'General';
$string['section_additional'] = 'Additional information';
$string['section_parent1'] = 'Parent 1 information';
$string['section_parent2'] = 'Parent 2 information';
$string['section_guardian'] = 'Legal guardian information';
$string['section_emergency'] = 'Emergency contact information';
$string['section_contact'] = 'Contact information';

// General fields.
$string['nationality'] = 'Nationality';
$string['passportid'] = 'Passport/ID #';
$string['address'] = 'Address line 1';
$string['city'] = 'City';
$string['state'] = 'State';
$string['zipcode'] = 'Zipcode';
$string['telephone'] = 'Telephone';
$string['healthconditions'] = 'Health conditions if any';
$string['medication'] = 'Medication';
$string['recentschool'] = 'Most recent school';

// Additional information.
$string['refnumber'] = 'Reference number';
$string['none'] = 'None';

// Contact information.
$string['studentphone'] = 'Student phone';

// Parent information fields.
$string['legalguardian'] = 'Legal guardian';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['parenttype'] = 'Type';
$string['father'] = 'Father';
$string['mother'] = 'Mother';
$string['other'] = 'Other';
$string['countryofresidence'] = 'Country of residence';
$string['educationlevel'] = 'Education level';
$string['occupation'] = 'Occupation';
$string['phone1'] = 'Phone 1';
$string['phone2'] = 'Phone 2';

// Education level options.
$string['edu_highschool'] = 'High school';
$string['edu_diploma'] = 'Diploma';
$string['edu_bachelors'] = 'Bachelor\'s degree';
$string['edu_masters'] = 'Master\'s degree';
$string['edu_phd'] = 'PhD';
$string['edu_other'] = 'Other';

// Guardian fields.
$string['relationshiptostudent'] = 'Relationship to student';

// Emergency contact fields.
$string['emergencycontactname'] = 'Emergency contact full name';
$string['emergencyrelationship'] = 'Relationship to student';
$string['emergencyphone'] = 'Phone';
$string['emergencyemail'] = 'Email';

// Confirmation section.
$string['section_confirmation'] = 'Confirmation';
