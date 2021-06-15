<?php
require_once('../../config.php');
require_once('sideblock_form.php');
global $DB,$OUTPUT,$PAGE;
 
$PAGE->set_url('/blocks/sideblock/view.php', array('id' => $courseid));
// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT); 
$blockid = required_param('blockid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) 
{
    print_error('invalidcourse', 'block_sideblock', $courseid);
}
require_login($course);
require_capability('block/sideblock:managepages', context_course::instance($courseid));