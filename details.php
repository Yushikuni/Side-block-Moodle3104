<?php
//zkopirovano z course completion status
require_once(__DIR__.'/../../config.php');
//require_once("{$CFG->libdir}/sideblock.php");

// Load data.
$id = required_param('course', PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);

// Load course.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Load user.
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
} else {
    $user = $USER;
}

// Check permissions.
require_login();

// Display page.

$PAGE->set_context(context_course::instance($course->id));

// Print header.
$page = get_string('completionprogressdetails', 'block_completionstatus');
$title = format_string($course->fullname) . ': ' . $page;

$PAGE->navbar->add($page);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/completionstatus/details.php', array('course' => $course->id, 'user' => $user->id));
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Is course complete?
$coursecomplete = $info->is_course_complete($user->id);

// Print table.
$last_type = '';
$agg_type = false;
$oddeven = 0;

foreach ($rows as $row) 
{
    echo html_writer::start_tag('tr', array('class' => 'r' . $oddeven));
    // Criteria group.
    echo html_writer::start_tag('td', array('class' => 'cell c0'));
    if ($last_type !== $row['details']['type']) 
    {
        $last_type = $row['details']['type'];
        echo $last_type;

        // Reset agg type.
        $agg_type = true;
    } 
    else 
    {
        // Display aggregation type.
        if ($agg_type)
        {
            $agg = $info->get_aggregation_method($row['type']);
            echo '('. html_writer::start_tag('i');
            if ($agg == COMPLETION_AGGREGATION_ALL) 
            {
                echo core_text::strtolower(get_string('all', 'completion'));
            } else 
            {
                echo core_text::strtolower(get_string('any', 'completion'));
            }

            echo ' ' . html_writer::end_tag('i') .core_text::strtolower(get_string('required')).')';
            $agg_type = false;
        }
    }
    echo html_writer::end_tag('td');

    // Status.
    echo html_writer::start_tag('td', array('class' => 'cell c3'));
    echo $row['details']['status'];
    echo html_writer::end_tag('td');

    // Is complete.
    echo html_writer::start_tag('td', array('class' => 'cell c4'));
    echo $row['complete'] ? get_string('yes') : get_string('no');
    echo html_writer::end_tag('td');

    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
    // For row striping.
    $oddeven = $oddeven ? 0 : 1;
}
echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');
$courseurl = new moodle_url("/course/view.php", array('id' => $course->id));
echo html_writer::start_tag('div', array('class' => 'buttons'));
echo $OUTPUT->single_button($courseurl, get_string('returntocourse', 'block_completionstatus'), 'get');
echo html_writer::end_tag('div');
echo $OUTPUT->footer();