<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);

$test_type = 'normal';

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['title']        = $addslashes(trim($_POST['title']));
	$_POST['num']	       = intval($_POST['num']);
	$_POST['num_takes']	   = intval($_POST['num_takes']);
	$_POST['content_id']   = intval($_POST['content_id']);
	$_POST['num_takes']    = intval($_POST['num_takes']);
	$_POST['anonymous']    = intval($_POST['anonymous']);
	$_POST['instructions'] = $addslashes($_POST['instructions']);


	// currently these options are ignored for tests:
	$_POST['format']       = 0;  //intval($_POST['format']);
	$_POST['order']	       = 1;  //intval($_POST['order']);
	$_POST['difficulty']   = 0;  //intval($_POST['difficulty']); 	/* avman */
	    
	if ($_POST['title'] == '') {
		$msg->addError('NO_TITLE');
	}

	$day_start	= intval($_POST['day_start']);
	$month_start= intval($_POST['month_start']);
	$year_start	= intval($_POST['year_start']);
	$hour_start	= intval($_POST['hour_start']);
	$min_start	= intval($_POST['min_start']);

	$day_end	= intval($_POST['day_end']);
	$month_end	= intval($_POST['month_end']);
	$year_end	= intval($_POST['year_end']);
	$hour_end	= intval($_POST['hour_end']);
	$min_end	= intval($_POST['min_end']);

	if (!checkdate($month_start, $day_start, $year_start)) {
		$msg->addError('START_DATE_INVALID');
	}

	if (!checkdate($month_end, $day_end, $year_end)) {
		$msg->addError('END_DATE_INVALID');
	}

	if (mktime($hour_end,   $min_end,   $month_end,   $day_end,   $year_end) < 
		mktime($hour_start, $min_start, $month_start, $day_start, $year_start)) {
			$msg->addError('END_DATE_INVALID');
	}

	if (!$msg->containsErrors()) {
		if (strlen($month_start) == 1){
			$month_start = "0$month_start";
		}
		if (strlen($day_start) == 1){
			$day_start = "0$day_start";
		}
		if (strlen($hour_start) == 1){
			$hour_start = "0$hour_start";
		}
		if (strlen($min_start) == 1){
			$min_start = "0$min_start";
		}

		if (strlen($month_end) == 1){
			$month_end = "0$month_end";
		}
		if (strlen($day_end) == 1){
			$day_end = "0$day_end";
		}
		if (strlen($hour_end) == 1){
			$hour_end = "0$hour_end";
		}
		if (strlen($min_end) == 1){
			$min_end = "0$min_end";
		}

		$start_date = "$year_start-$month_start-$day_start $hour_start:$min_start:00";
		$end_date	= "$year_end-$month_end-$day_end $hour_end:$min_end:00";

		$sql = "INSERT INTO ".TABLE_PREFIX."tests VALUES (0, $_SESSION[course_id], '$_POST[title]', $_POST[format], '$start_date', '$end_date', $_POST[order], $_POST[num], '$_POST[instructions]', $_POST[content_id], $_POST[result_release], $_POST[random], $_POST[difficulty], $_POST[num_takes], $_POST[anonymous], '')";

		$result = mysql_query($sql, $db);
		$tid = mysql_insert_id($db);
		
		if (isset($_POST['groups']) && $tid) {
			$sql = "INSERT INTO ".TABLE_PREFIX."tests_groups VALUES ";
			foreach ($_POST['groups'] as $group) {
				$group = intval($group);
				$sql .= "($tid, $group),";
			}
			$sql = substr($sql, 0, -1);
			$result = mysql_query($sql, $db);
		}

		$msg->addFeedback('TEST_ADDED');
		header('Location: index.php');
		exit;
	}
}

if (isset($_POST['num']) && ($_POST['num'] === 0)) {
	$_POST['num'] = '';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="test_type" value="<?php echo $test_type; ?>" />
<div class="input-form">
	<div class="row">
		<label for="title"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" size="30" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row">
		<label for="num_t"><?php echo _AT('num_takes_test'); ?></label><br />
		<select name="num_takes" id="num_t">
			<option value="<?php echo AT_TESTS_TAKE_UNLIMITED; ?>" <?php if ($_POST['num_takes'] == AT_TESTS_TAKE_UNLIMITED) { echo 'selected="selected"'; } ?>><?php echo _AT('unlimited'); ?></option>
		
			<option value="1"<?php if ($_POST['num_takes'] == 1) { echo ' selected="selected"'; } ?>>1</option>
			<option value="2"<?php if ($_POST['num_takes'] == 2) { echo ' selected="selected"'; } ?>>2</option>
			<option value="3"<?php if ($_POST['num_takes'] == 3) { echo ' selected="selected"'; } ?>>3</option>
			<option value="4"<?php if ($_POST['num_takes'] == 4) { echo ' selected="selected"'; } ?>>4</option>
			<option value="5"<?php if ($_POST['num_takes'] == 5) { echo ' selected="selected"'; } ?>>5</option>
			<option value="6"<?php if ($_POST['num_takes'] == 6) { echo ' selected="selected"'; } ?>>6</option>
			<option value="7"<?php if ($_POST['num_takes'] == 7) { echo ' selected="selected"'; } ?>>7</option>
			<option value="8"<?php if ($_POST['num_takes'] == 8) { echo ' selected="selected"'; } ?>>8</option>
			<option value="9"<?php if ($_POST['num_takes'] == 9) { echo ' selected="selected"'; } ?>>9</option>
			<option value="10"<?php if ($_POST['num_takes'] == 10) { echo ' selected="selected"'; } ?>>10</option>
			<option value="15"<?php if ($_POST['num_takes'] == 15) { echo ' selected="selected"'; } ?>>15</option>
			<option value="20"<?php if ($_POST['num_takes'] == 20) { echo ' selected="selected"'; } ?>>20</option>
			<option value="25"<?php if ($_POST['num_takes'] == 25) { echo ' selected="selected"'; } ?>>25</option>
			<option value="30"<?php if ($_POST['num_takes'] == 30) { echo ' selected="selected"'; } ?>>30</option>	<option value="40"<?php if ($_POST['num_takes'] == 40) { echo ' selected="selected"'; } ?>>40</option>
			<option value="50"<?php if ($_POST['num_takes'] == 50) { echo ' selected="selected"'; } ?>>50</option>
			<option value="60"<?php if ($_POST['num_takes'] == 60) { echo ' selected="selected"'; } ?>>60</option>
			<option value="70"<?php if ($_POST['num_takes'] == 70) { echo ' selected="selected"'; } ?>>70</option>
			<option value="80"<?php if ($_POST['num_takes'] == 80) { echo ' selected="selected"'; } ?>>80</option>
			<option value="90"<?php if ($_POST['num_takes'] == 90) { echo ' selected="selected"'; } ?>>90</option>
			<option value="100"<?php if ($_POST['num_takes'] == 100) { echo ' selected="selected"'; } ?>>100</option>
		</select>
	</div>

	<div class="row">
		<?php echo _AT('anonymous_test'); ?><br />
		<?php 
			if ($_POST['anonymous'] == 1) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
			}
		?>

		<input type="radio" name="anonymous" id="anonN" value="0" <?php echo $n; ?> /><label for="anonN"><?php echo _AT('no1'); ?></label> 
		<input type="radio" name="anonymous" value="1" id="anonY" <?php echo $y; ?> /><label for="anonY"><?php echo _AT('yes1'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('result_release'); ?><br />
		<?php 
			if ($_POST['result_release'] == AT_RELEASE_IMMEDIATE) {
				$check_marked = $check_never = '';
				$check_immediate = 'checked="checked"';
		
			} else if ($_POST['result_release'] == AT_RELEASE_MARKED) {
				$check_immediate = $check_never = '';
				$check_marked = 'checked="checked"';

			} else if ($_POST['result_release'] == AT_RELEASE_NEVER) {
				$check_immediate = $check_marked = '';
				$check_never = 'checked="checked"';
			}
		?>
		<input type="radio" name="result_release" id="release1" value="<?php echo AT_RELEASE_IMMEDIATE; ?>" <?php echo $check_immediate; ?> /><label for="release1"><?php echo _AT('release_immediate'); ?></label><br />
		<input type="radio" name="result_release" id="release2" value="<?php echo AT_RELEASE_MARKED; ?>" <?php echo $check_marked; ?> /><label for="release2"><?php echo _AT('release_marked'); ?></label><br />
		<input type="radio" name="result_release" id="release3" value="<?php echo AT_RELEASE_NEVER; ?>" <?php echo $check_never; ?>/><label for="release3"><?php echo _AT('release_never'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('randomize_questions'); ?><br />
		<?php 
			if ($_POST['random'] == 1) {
				$y = 'checked="checked"';
				$n = $disabled = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
				$disabled = 'disabled="disabled" ';
			}
		?>
		<input type="radio" name="random" id="random" value="0" checked="checked" onfocus="document.form.num.disabled=true;" /><label for="random"><?php echo _AT('no1'); ?></label>. <input type="radio" name="random" value="1" id="ry" onfocus="document.form.num.disabled=false;" <?php echo $y; ?> /><label for="ry"><?php echo _AT('yes1'); ?></label>, <input type="text" name="num" id="num" class="formfieldR" size="2" value="<?php echo $_POST['num']; ?>" <?php echo $disabled . $n; ?> /> <label for="num"><?php echo _AT('num_questions_per_test'); ?></label>
	</div>


	<div class="row">
		<?php echo _AT('start_date');  ?><br />
		<?php
			$today_day  = date('d');
			$today_mon  = date('m');
			$today_year = date('Y');
			$today_hour = date('H');
			$today_min  = 0;

			$name = '_start';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');

		?>
	</div>

	<div class="row">
		<?php echo _AT('end_date');  ?><br />
		<?php
			$today_day  = date('d');
			$today_mon  = date('m');
			$today_year = date('Y');
			$today_hour = date('H');
			$today_min  = 0;
					
			$name = '_end';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</div>

	<div class="row">
		<label for="inst"><?php echo _AT('limit_to_group'); ?></label><br />
		<?php
			//show groups
			$sql	= "SELECT * FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] ORDER BY title";
			$result	= mysql_query($sql, $db);

			echo _AT('everyone');
		
			if ($row = mysql_fetch_assoc($result)) { 
				echo ' <strong>'._AT('or').'</strong><br />';
	
				do {
					echo '<label><input type="checkbox" value="'.$row['group_id'].'" name="groups['.$row['group_id'].']" '; 
					if (is_array($current_groups) && in_array($row['group_id'], $current_groups)) {
						echo 'checked="checked"';
					}
					echo '/>'.$row['title'].'</label><br />';
				} while ($row = mysql_fetch_assoc($result));
			}
		?>
	</div>

	<div class="row">
		<label for="inst"><?php echo _AT('special_instructions'); ?></label><br />
		<textarea name="instructions" cols="35" rows="3" id="inst"><?php echo htmlspecialchars($_POST['instructions']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>" name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>