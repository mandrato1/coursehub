<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
*
* @package    local
* @subpackage coursehub
* @copyright  2017	Mark Michaelsen (mmichaelsen678@gmail.com)
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(dirname(dirname(dirname(__FILE__))) . "/config.php");
require_once ("forms/forum_form.php");
global $CFG, $DB, $OUTPUT, $PAGE, $USER;

// User must be logged in.
require_login();
if (isguestuser()) {
	die();
}

$context = context_system::instance();

$url = new moodle_url("/local/coursehub/message.php");
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout("standard");
$PAGE->set_title(get_string("page_title", "local_coursehub"));
$PAGE->set_heading(get_string("page_heading", "local_coursehub"));
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin ( 'ui' );
$PAGE->requires->jquery_plugin ( 'ui-css' );

$forumform = new forum_form();

echo $OUTPUT->header();

if($forumform->is_cancelled()) {
	$indexurl = new moodle_url("/local/coursehub/index.php");
	redirect($indexurl);
}

else if($data = $forumform->get_data()) {
	$record = new stdClass();
	
	$record->academicperiodid = $perioddata[0];
	$record->academicperiodname = $perioddata[4];
	$record->categoryid = $data->category;
	$record->campus = $perioddata[1];
	if(isset(explode("-", $perioddata[1])[1])){
		$record->campusshort = explode("-", $perioddata[1])[1];
	}else{
		$record->campusshort = $perioddata[1];
	}
	$record->type = $perioddata[2];
	$record->year = $perioddata[3];
	$record->semester = $perioddata[5];
	$record->timecreated = time();
	$record->timemodified = $record->timecreated;
	$record->responsible = $data->responsible;
	$record->status = $data->status;
	
	$dataid = $DB->insert_record("sync_data", $record);
	
	$formurl = new moodle_url("/local/sync/record.php", array(
			"insert" => "success",
			"dataid" => $dataid
	));
	redirect($formurl);
}else {
	$forumform->display();
}

echo $OUTPUT->footer();