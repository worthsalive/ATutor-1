<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

if (($_REQUEST['popup'] == TRUE) || ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['save'])) {
	$content = str_replace("\r\n", "\n", $_POST['body_text']);
	$file = $_POST['file'];
	if (($f = @fopen($current_path.$pathext.$file, 'w')) && (@fwrite($f, $content) !== false) && @fclose($f)) {
		$msg->addFeedback(array('FILE_SAVED', $file));
		header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;		
	} else {
		$msg->addError('FILE_NOT_SAVED');
		header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;
	}
}

$file    = $_GET['file'];
$pathext = $_GET['pathext']; 
$popup   = $_GET['popup'];
$framed  = $_GET['framed'];

$filedata = stat($current_path.$pathext.$file);
$path_parts = pathinfo($current_path.$pathext.$file);
$ext = strtolower($path_parts['extension']);

// open file to edit
$real = realpath($current_path . $pathext . $file);

if (course_realpath($current_path . $pathext . $file) == FALSE) {
	// error: File does not exist
	$msg->addError('FILE_NOT_EXIST');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
} else if (is_dir($current_path.$pathext.$file)) {
	// error: cannot edit folder
	$msg->addError('BAD_FILE_TYPE');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
} else if (!is_readable($current_path.$pathext.$file)) {
	// error: File cannot open file
	$msg->addError(array('CANNOT_OPEN_FILE', $file));
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
} else if (in_array($ext, $editable_file_types)) {
	$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
} else {
	//error: bad file type
	$msg->addError('BAD_FILE_TYPE');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
}

require($_header_file);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="pathext" value="<?php echo $pathext; ?>" />
<input type="hidden" name="framed"  value="<?php echo $framed; ?>" />
<input type="hidden" name="popup"   value="<?php echo $popup; ?>" />
<input type="hidden" name="file"    value="<?php echo $file; ?>" />

<div class="input-form">
	<div class="row">
		<h3><?php echo $file; ?></h3>
	</div>
	<div class="row">
		<label for="body_text"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('body'); ?></label><br />
		<textarea  name="body_text" id="body_text" rows="25"><?php echo $_POST['body_text']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require($_footer_file); ?>