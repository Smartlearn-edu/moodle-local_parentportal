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
 * Arabic language strings for the parent portal plugin.
 *
 * @package     local_parentportal
 * @copyright   2026 Mohammad Nabil <mohammad@smartlearn.education>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General.
$string['pluginname'] = 'بوابة ولي الأمر';
$string['parentportal:addchild'] = 'إضافة ابن عبر بوابة ولي الأمر';
$string['pagetitle'] = 'بوابة ولي الأمر';
$string['addchild'] = 'إضافة ابن';
$string['addnewchild'] = 'إضافة ابن جديد';
$string['mychildren'] = 'أبنائي';
$string['nochildren'] = 'لم تقم بإضافة أي أبناء بعد.';

// Tabs.
$string['tab_dashboard'] = 'لوحة التحكم';
$string['tab_children'] = 'أبنائي';
$string['tab_academics'] = 'الأكاديميات';
$string['tab_finances'] = 'المحفظة والمالية';
$string['tab_messages'] = 'الرسائل والتواصل';

// Header & Navigation.
$string['welcomeparent'] = 'مرحباً، {$a}';
$string['familyoverview'] = 'المركز التعليمي - البوابة العائلية';
$string['activechild'] = 'الابن المختار';
$string['switchchild'] = 'تبديل الابن';
$string['allchildren'] = 'جميع الأبناء';
$string['walletbalance'] = 'رصيد المحفظة';
$string['topupwallet'] = 'شحن المحفظة';
$string['viewstudentcard'] = 'عرض البطاقة';

// Dashboard KPIs & Actions.
$string['kpi_enrolledcourses'] = 'المقررات المسجلة';
$string['kpi_averagegrade'] = 'متوسط الدرجات';
$string['kpi_attendance'] = 'نسبة الحضور';
$string['kpi_deadlines'] = 'المواعيد القادمة';
$string['kpi_nodeadlines'] = 'لا توجد مواعيد قادمة';
$string['quickactions'] = 'إجراءات سريعة';
$string['browsecourses'] = 'استعراض المقررات';
$string['viewgradebook'] = 'كشف الدرجات';
$string['askteacher'] = 'اسأل المعلم';
$string['activechilddetails'] = 'بيانات الابن المختار';
$string['upcomingdeadlines'] = 'المواعيد النهائية القادمة';
$string['recentprogress'] = 'ملخص الأداء الأكاديمي';

// Children Management.
$string['child'] = 'الابن';
$string['linkchild'] = 'ربط حساب ابن موجود';
$string['linkchild_desc'] = 'إذا كان لابنك حساب تم إنشاؤه مسبقاً من قِبل المدرسة أو المركز، أدخل اسم المستخدم أو البريد الإلكتروني مع كلمة المرور لربطه بحسابك العائلي.';
$string['childusernameoremail'] = 'اسم المستخدم أو البريد الإلكتروني للابن';
$string['childpassword'] = 'كلمة مرور حساب الابن';
$string['childlinked'] = 'تم ربط حساب الابن "{$a}" بحسابك بنجاح.';
$string['error_already_linked'] = 'هذا الحساب مرتبط بالفعل بحسابك العائلي.';
$string['error_invalid_credentials'] = 'تعذر التحقق من بيانات الابن. يرجى التأكد من اسم المستخدم أو البريد الإلكتروني وكلمة المرور.';
$string['nocoursesenrolled'] = 'غير مسجل في أي مقررات بعد.';
$string['enrolledin'] = 'مسجل في {$a} مقررات';
$string['managechildren'] = 'إدارة الأبناء';
$string['childroster'] = 'قائمة الأبناء';
$string['editdetails'] = 'تعديل البيانات';
$string['cancel'] = 'إلغاء';
$string['close'] = 'إغلاق';
$string['save'] = 'حفظ';

// Tab placeholders.
$string['academics_title'] = 'التقدم الأكاديمي ومستوى الأداء';
$string['academics_desc'] = 'متابعة تقدم المقررات والدرجات والوصول إلى السجل الأكاديمي والتحليلات الذكية للطالب.';
$string['academics_summary'] = 'الملخص الأكاديمي';
$string['total_courses'] = 'المقررات المسجلة';
$string['completed_courses'] = 'المكتملة';
$string['inprogress_courses'] = 'قيد الدراسة';
$string['overall_average_grade'] = 'المعدل العام';
$string['current_grade'] = 'الدرجة الحالية';
$string['course_completion'] = 'نسبة إكمال المقرر';
$string['course_instructors'] = 'معلمو المقرر';
$string['no_instructors'] = 'لم يتم تعيين معلم';
$string['gotocourse'] = 'دخول المقرر';
$string['viewcoursedetails'] = 'معلومات المقرر';
$string['report_gradebook_title'] = 'السجل الأكاديمي والمعدل';
$string['report_gradebook_desc'] = 'استعراض تفاصيل الدرجات لكل مادة وتصدير السجلات والشهادات الرسمية PDF.';
$string['report_gradebook_btn'] = 'عرض السجل الأكاديمي';
$string['report_ai_title'] = 'التقييم الذكي بالذكاء الاصطناعي';
$string['report_ai_desc'] = 'توليد تحليل ذكي لمستوى الطالب، ونقاط القوة والفرص التعليمية الموصى بها.';
$string['report_ai_btn'] = 'تشغيل تقييم AI';
$string['report_analytics_title'] = 'تحليلات بصرية شاملة 360°';
$string['report_analytics_desc'] = 'استكشاف مخططات إتقان المواد ومنحنى التطور الأكاديمي ومستوى التفاعل.';
$string['report_analytics_btn'] = 'فتح لوحة المتابعة الذكية';
$string['deadlines_title'] = 'المواعيد النهائية القادمة';
$string['deadlines_desc'] = 'الواجبات والاختبارات والمهام الدراسية القادمة.';
$string['nodeadlines_title'] = 'كل المهام منجزة!';
$string['nodeadlines_desc'] = 'لا توجد أي واجبات أو مواعيد نهائية مجدولة لهذا الابن حالياً.';
$string['due_soon'] = 'ينتهي قريباً';
$string['attendance_summary'] = 'ملخص الحضور والغياب';
$string['attendance_sessions'] = 'الجلسات المسجلة';
$string['attendance_present'] = 'حضور';
$string['attendance_absent'] = 'غياب';
$string['attendance_status_excellent'] = 'ممتاز';
$string['attendance_status_good'] = 'جيد';
$string['attendance_status_attention'] = 'بحاجة لمتابعة';
$string['nocourses_title'] = 'لا توجد مقررات مسجلة بعد';
$string['nocourses_desc'] = 'ابنك غير مسجل في أي مقرر حالياً. تصفح دليل المقررات لاختيار المسار التعليمي المناسب.';
$string['explorecourses'] = 'استعراض المقررات';

// Finances & Family Cart.
$string['finances_title'] = 'المحفظة العائلية ومشتريات المقررات';
$string['finances_desc'] = 'إدارة رصيد المحفظة العائلية، ومراجعة السلة، وشراء المقررات للأبناء.';
$string['family_wallet_title'] = 'رصيد المحفظة العائلية';
$string['family_wallet_desc'] = 'رصيد مسبق الدفع يُستخدم للتسجيل الفوري في المقررات لجميع الأبناء.';
$string['topup_btn'] = 'شحن المحفظة';
$string['family_cart_title'] = 'سلة التسجيل العائلي';
$string['family_cart_desc'] = 'المقررات المختارة لأبنائك بانتظار إتمام التسجيل.';
$string['cart_empty_title'] = 'سلة العائلة فارغة';
$string['cart_empty_desc'] = 'أضف مقررات لأبنائك من دليل المقررات أو صفحات المواد للتسجيل المجمع في عملية واحدة.';
$string['cart_subtotal'] = 'الإجمالي الفرعي';
$string['cart_balance_after'] = 'الرصيد المتبقي بعد الشراء';
$string['cart_insufficient_warning'] = 'رصيد محفظتك غير كافٍ لإتمام عملية الشراء. يرجى شحن ما لا يقل عن {$a} للمتابعة.';
$string['cart_checkout_btn'] = 'الدفع من المحفظة وتسجيل الأبناء';
$string['cart_added'] = 'تمت إضافة مقرر "{$a->course}" للطالب {$a->child} إلى سلة العائلة.';
$string['courses_added_to_cart'] = 'تمت إضافة {$a} مقرر(ات) إلى سلة العائلة.';
$string['cart_already_in_cart'] = 'هذا المقرر موجود بالفعل في سلة العائلة لهذا الابن.';
$string['cart_already_enrolled'] = '{$a->child} مسجل بالفعل في مقرر {$a->course}.';
$string['cart_item_removed'] = 'تم حذف المقرر من سلة العائلة.';
$string['cart_cleared'] = 'تم إفراغ سلة العائلة.';
$string['cart_empty'] = 'سلة العائلة فارغة.';
$string['cart_insufficient_balance'] = 'رصيد المحفظة غير كافٍ لإتمام التسجيل. يرجى شحن المحفظة.';
$string['checkout_success'] = 'تم بنجاح شراء وتسجيل {$a} مقرر(ات) لأبنائك!';
$string['billing_history_title'] = 'سجل الفواتير والمشتريات';
$string['no_orders_recorded'] = 'لا توجد عمليات شراء سابقة مسجلة.';
$string['order_date'] = 'التاريخ';
$string['order_child'] = 'الابن';
$string['order_course'] = 'المقرر';
$string['order_amount'] = 'المبلغ';
$string['order_status'] = 'الحالة';
$string['order_method'] = 'طريقة الدفع';
$string['order_completed'] = 'مسجل';
$string['action_remove'] = 'حذف';
$string['action_clearcart'] = 'إفراغ السلة';

// Messages & Communication.
$string['messages_title'] = 'التواصل مع المعلمين';
$string['messages_desc'] = 'التواصل المباشر مع المعلمين القائمين على تدريس أبنائك.';
$string['teachers_directory'] = 'دليل المعلمين';
$string['teachers_directory_desc'] = 'قنوات التواصل المباشر والاستفسار مع معلمي {$a}.';
$string['send_message'] = 'إرسال رسالة';
$string['contact_teacher'] = 'التواصل مع المعلم';
$string['chat_whatsapp'] = 'واتساب';
$string['open_moodle_chat'] = 'محادثة مودل';
$string['inquiry_modal_title'] = 'إرسال استفسار للمعلم';
$string['inquiry_modal_desc'] = 'ستُرسل رسالتك مباشرة إلى بريد المعلم وإشعاراته في المنصة، مصحوبة ببيانات الطالب والمقرر.';
$string['regarding_child'] = 'بخصوص الطالب';
$string['select_course'] = 'المقرر المعني';
$string['subject'] = 'الموضوع';
$string['subject_placeholder'] = 'مثلاً: استفسار حول الواجب الأخير أو مستوى التحصيل';
$string['message_body'] = 'نص الرسالة';
$string['message_placeholder'] = 'اكتب استفسارك أو ملاحظتك للمعلم هنا...';
$string['send_inquiry_btn'] = 'إرسال الاستفسار';
$string['message_sent_success'] = 'تم إرسال رسالتك إلى المعلم {$a} بنجاح.';
$string['no_teachers_found'] = 'لم يتم العثور على معلمين';
$string['no_teachers_desc'] = 'لم يتم تعيين معلمين بعد للمقررات المسجل بها ابنك.';
$string['error_notparent'] = 'ليس لديك صلاحية تنفيذ هذا الإجراء لهذا الطالب.';
$string['error_invalidteacher'] = 'تعذر العثور على المعلم المحدد.';
$string['courses_taught'] = 'المقررات المسندة';

// Inquiries History (Tab 5).
$string['inquiry_history_title'] = 'سجل الاستفسارات والردود';
$string['inquiry_history_desc'] = 'سجل كامل لجميع الرسائل المرسلة للمعلمين وردودهم.';
$string['inquiry_status_pending'] = 'بانتظار الرد';
$string['inquiry_status_replied'] = 'تم الرد';
$string['inquiry_status_closed'] = 'مغلق';
$string['inquiry_no_history'] = 'لم يتم إرسال أي استفسارات بعد.';
$string['inquiry_sent_on'] = 'أُرسلت في {$a}';
$string['inquiry_replied_on'] = 'تم الرد في {$a}';
$string['teacher_reply'] = 'رد المعلم';
$string['view_inquiry'] = 'عرض التفاصيل';

// Absence & Excuse Requests (Tab 3).
$string['absence_request_title'] = 'طلبات وإشعارات الغياب';
$string['submit_absence_btn'] = 'تقديم عذر غياب';
$string['absence_modal_title'] = 'تقديم إشعار غياب بعذر';
$string['absence_modal_desc'] = 'إشعار إدارة المدرسة ومعلمي المقررات بغياب مبرر للابن.';
$string['absence_startdate'] = 'تاريخ بدء الغياب';
$string['absence_enddate'] = 'تاريخ نهاية الغياب';
$string['absence_reason'] = 'سبب الغياب';
$string['absence_reason_medical'] = 'عذر صحي / مرضي';
$string['absence_reason_family'] = 'ظرف عائلي طارئ';
$string['absence_reason_travel'] = 'سفر مبرر';
$string['absence_reason_appointment'] = 'موعد رسمي';
$string['absence_reason_other'] = 'سبب آخر';
$string['absence_details'] = 'التفاصيل / الملاحظات';
$string['absence_details_placeholder'] = 'وضح سبب الغياب أو أي تفاصيل مساندة...';
$string['absence_submitted_success'] = 'تم تقديم طلب عذر الغياب بنجاح.';
$string['absence_history_title'] = 'سجل إشعارات الغياب السابقة';
$string['absence_no_history'] = 'لا توجد إشعارات غياب سابقة.';
$string['absence_status_submitted'] = 'قيد المراجعة';
$string['absence_status_approved'] = 'مقبول';
$string['absence_status_acknowledged'] = 'تم الاطلاع';
$string['absence_course'] = 'المقرر المعني';
$string['absence_all_courses'] = 'جميع المقررات (غياب يوم كامل)';

// Catalog Quick Buy.
$string['buy_for_child'] = 'تسجيل الابن';
$string['select_children_to_enroll'] = 'اختر الأبناء المراد تسجيلهم';
$string['already_enrolled'] = 'مسجل مسبقاً';
$string['in_cart'] = 'في السلة';

// Form fields - add child.
$string['firstname'] = 'الاسم الأول';
$string['lastname'] = 'اسم العائلة';
$string['email'] = 'البريد الإلكتروني';
$string['password'] = 'كلمة المرور';
$string['sex'] = 'الجنس';
$string['male'] = 'ذكر';
$string['female'] = 'أنثى';
$string['birthdate'] = 'تاريخ الميلاد';
$string['timezone'] = 'المنطقة الزمنية';
$string['country'] = 'الدولة';
$string['grade'] = 'المرحلة الدراسية / الصف';
$string['curriculum'] = 'المنهج التعليمي';
$string['personalphoto'] = 'الصورة الشخصية';

// Grade options.
$string['grade_kg1'] = 'روضة أولى (KG1)';
$string['grade_kg2'] = 'روضة ثانية (KG2)';
$string['grade_1'] = 'الصف الأول';
$string['grade_2'] = 'الصف الثاني';
$string['grade_3'] = 'الصف الثالث';
$string['grade_4'] = 'الصف الرابع';
$string['grade_5'] = 'الصف الخامس';
$string['grade_6'] = 'الصف السادس';
$string['grade_7'] = 'الصف السابع';
$string['grade_8'] = 'الصف الثامن';
$string['grade_9'] = 'الصف التاسع';
$string['grade_10'] = 'الصف العاشر';
$string['grade_11'] = 'الصف الحادي عشر';
$string['grade_12'] = 'الصف الثاني عشر';

// Curriculum options.
$string['curriculum_american'] = 'المنهج الأمريكي';
$string['curriculum_canadian'] = 'المنهج الكندي';
$string['curriculum_transition'] = 'برنامج التأهيل الطلابي';
$string['curriculum_tunisian'] = 'المنهج التونسي';

// Settings.
$string['settings_parentrole'] = 'دور ولي الأمر';
$string['settings_parentrole_desc'] = 'اختر الدور الذي سيتم تعيينه لولي الأمر في سياق حساب الابن لربط العلاقة في مودل.';

// Messages.
$string['childcreated'] = 'تم إنشاء حساب الابن "{$a}" بنجاح وربطه بحسابك.';
$string['error_emailexists'] = 'هذا البريد الإلكتروني مسجل بالفعل.';
$string['error_usernameexists'] = 'تعذر إنشاء اسم مستخدم فريد. يرجى تجربة اسم مختلف.';
$string['error_norole'] = 'لم يتم تكوين دور ولي الأمر في إعدادات الإضافة. يرجى مراجعة إدارة المنصة.';
$string['error_creationfailed'] = 'حدث خطأ أثناء إنشاء حساب الابن. يرجى المحاولة مرة أخرى.';
$string['error_noaccess'] = 'ليس لديك الصلاحية للوصول إلى بطاقة هذا الطالب.';

// Children table headers.
$string['childname'] = 'الاسم';
$string['childemail'] = 'البريد الإلكتروني';
$string['childgrade'] = 'الصف';
$string['childcurriculum'] = 'المنهج';
$string['dateadded'] = 'تاريخ الإضافة';
$string['actions'] = 'الإجراءات';
$string['studentcard'] = 'بطاقة الطالب';
$string['editcard'] = 'تعديل البطاقة';
$string['viewcard'] = 'عرض البطاقة';

// Student card page.
$string['studentcardfor'] = 'بطاقة الطالب: {$a}';
$string['editstudentcard'] = 'تعديل بطاقة الطالب';
$string['savecard'] = 'حفظ البطاقة';
$string['cardsaved'] = 'تم حفظ بيانات بطاقة الطالب بنجاح.';
$string['nocarddata'] = 'لم يتم إدخال بيانات بعد. انقر على "تعديل البطاقة" لملء البيانات.';
$string['noacademicdata'] = 'لا توجد سجلات أكاديمية متاحة.';

// Student card sections.
$string['section_general'] = 'عام';
$string['section_additional'] = 'معلومات إضافية';
$string['section_parent1'] = 'بيانات ولي الأمر 1';
$string['section_parent2'] = 'بيانات ولي الأمر 2';
$string['section_guardian'] = 'بيانات الوصي القانوني';
$string['section_emergency'] = 'بيانات الاتصال في حالات الطوارئ';
$string['section_contact'] = 'بيانات الاتصال';

// General fields.
$string['nationality'] = 'الجنسية';
$string['passportid'] = 'رقم الجواز / الهوية';
$string['address'] = 'عنوان السكن';
$string['city'] = 'المدينة';
$string['state'] = 'المنطقة / المحافظة';
$string['zipcode'] = 'الرمز البريدي';
$string['telephone'] = 'الهاتف';
$string['healthconditions'] = 'الحالات الصحية إن وجدت';
$string['medication'] = 'الأدوية المنتظمة';
$string['recentschool'] = 'المدرسة السابقة';

// Additional information.
$string['refnumber'] = 'الرقم المرجعي';
$string['none'] = 'لا يوجد';

// Contact information.
$string['studentphone'] = 'هاتف الطالب';

// Parent information fields.
$string['legalguardian'] = 'الوصي القانوني';
$string['yes'] = 'نعم';
$string['no'] = 'لا';
$string['parenttype'] = 'الصفة';
$string['father'] = 'الأب';
$string['mother'] = 'الأم';
$string['other'] = 'أخرى';
$string['countryofresidence'] = 'بلد الإقامة';
$string['educationlevel'] = 'المستوى التعليمي';
$string['occupation'] = 'المهنة';
$string['phone1'] = 'الهاتف 1';
$string['phone2'] = 'الهاتف 2';

// Education level options.
$string['edu_highschool'] = 'الثانوية العامة';
$string['edu_diploma'] = 'دبلوم';
$string['edu_bachelors'] = 'بكالوريوس';
$string['edu_masters'] = 'ماجستير';
$string['edu_phd'] = 'دكتوراه';
$string['edu_other'] = 'أخرى';

// Guardian fields.
$string['relationshiptostudent'] = 'صلة القرابة بالطالب';

// Emergency contact fields.
$string['emergencycontactname'] = 'الاسم الكامل لجهة الاتصال في الطوارئ';
$string['emergencyrelationship'] = 'صلة القرابة';
$string['emergencyphone'] = 'الهاتف';
$string['emergencyemail'] = 'البريد الإلكتروني';

// Confirmation section.
$string['section_confirmation'] = 'التأكيد والتعهد';
