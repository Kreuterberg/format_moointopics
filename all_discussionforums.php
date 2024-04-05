<?php
require_once('../../../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once('../../../mod/forum/lib.php');

use format_moointopics\local\chapterlib;
use format_moointopics\local\forumlib;



global $USER, $PAGE, $CFG, $DB;

$courseid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance(SITEID); // $course->id, MUST_EXIST

require_login($course);

$systemcontext = context_system::instance();

$PAGE->set_course($course);
$PAGE->set_pagelayout('course');
$PAGE->set_context(\context_course::instance($course->id));
$PAGE->set_title("$course->shortname: " . get_string('my_certificate', 'format_moointopics'));
$PAGE->set_heading($course->fullname);
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_url('/course/format/moointopics/all_discussionforums.php', array('id' => $course->id));

echo $OUTPUT->header();

$breadcrumb = chapterlib::subpage_navbar();
$oc_m = $DB->get_record('modules', array('name' => 'forum'));

$sql = 'SELECT * FROM mdl_forum WHERE course = :cid AND type != :tid ORDER BY ID DESC';
$param = array('cid' =>$COURSE->id, 'tid' => 'news');
$oc_foren = $DB->get_records_sql($sql, $param);
$s = 'SELECT * FROM mdl_forum_discussions WHERE course = :cid ORDER BY ID DESC';
$p = array('cid' =>$course->id);
$oc_f= $DB->get_records_sql($s,$p);

$forumslist = [];

$value = '1';

    if (count($oc_foren) >= 1) {
        foreach ($oc_foren as $key => $oc_forum) {
            $cm = get_coursemodule_from_instance('forum', $oc_forum->id, $course->id);

            if(is_object($cm)) {
                $forum = $DB->get_record("forum", array("id" => $cm->instance));
                $forum->istracked = forum_tp_is_tracked($oc_forum);
                if ($forum->istracked) {
                    $forum->unreadpostscount = forum_tp_count_forum_unread_posts($cm, $course);
                }

                $unreadposts = forumlib::count_unread_posts($USER->id, $course->id, false, $oc_forum->id);

                $oc_cm = $DB->get_record('course_modules', array('instance' => $oc_forum->id, 'course' => $course->id, 'module' => $oc_m->id), '*', $strictness=IGNORE_MISSING);

                //$oc_link = html_writer::link(new moodle_url('/course/format/mooin4/forums.php?f=' . $oc_forum->id .'&tab='.'1'), $oc_forum->name);
                $discussion_forum = new stdClass(); 
                $discussion_forum->name = $oc_forum->name;
                $discussion_forum->url = new moodle_url('/course/format/moointopics/forums.php?f=' . $oc_forum->id .'&tab='.'1');
                $discussion_forum->index = $value++;

                if (intval($oc_cm->visible) === 1) {
                    //$forum_element =  html_writer::div($value++  . ' ' .$oc_link, 'forum_title');
                    
                    array_push($forumslist, $discussion_forum);
                    if ($unreadposts >= 1) {
                        $discussion_forum->markasunreadlink = new moodle_url('/course/format/moointopics/forums.php?f='.$oc_forum->id.'&markasread=1&redirect=1');
                        //$markasunreadlink = html_writer::div($markasunreadlink, 'markasunreadlink d-none d-md-flex');
                        //$markasunreadlink_mobile = html_writer::link(new moodle_url('/course/format/mooin4/forums.php?f='.$oc_forum->id.'&markasread=1&redirect=1'),'', array('class'=>'markasunreadlink-mobile mooin4-btn d-flex d-md-none'));
                        //$markasunreadlink_mobile = html_writer::div($markasunreadlink_mobile, 'markasunreadlink-mobile mooin4-btn d-flex d-md-none');
                        //$forum_unread = html_writer::div($unreadposts, 'count-container d-inline-flex inline-badge fw-700');
                        //echo html_writer::start_span('forum_elemts_in_list') . $forum_element . ' ' . $forum_unread.' '.$markasunreadlink.$markasunreadlink_mobile.html_writer::end_span();
                    } else {
                        //echo html_writer::start_span('forum_elemts_in_list') . $forum_element . html_writer::end_span();
                    }
                }
                
            }

        }
        //echo html_writer::end_div(); //close border-card div
        
    } else {
        
        // No Forum image
    }
    
   

$data = [
    'breadcrumb' => $breadcrumb,
    'forumslist' => $forumslist
];

echo $OUTPUT->render_from_template('format_moointopics/local/content/subpages/all_discussionforums', $data);
echo $OUTPUT->footer();