<?php
	if (isset($menu) and isset($top)) {
		$_top_margin = 70;
	} elseif (isset($menu)) {
		$_top_margin = 25;
	} elseif (isset($top)) {
		$_top_margin = 45;
	} else {
		$_top_margin = 0;
	}

	echo $_xml_header;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo $_title?></title>

<?php foreach ($_javascript as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k?>"></script>
<?php endforeach; ?>

<?php foreach ($_css as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media))?>"></link>
<?php endforeach; ?>

<?php echo $this->getP4AJavascript() ?>
</head>

<body class="p4a_browser_<?php echo P4A::singleton()->getBrowser() ?>">
<div id='p4a_body' class='p4a_browser_<?php echo P4A::singleton()->getBrowserOS() ?>'>
<div id='p4a_loading'><img src='<?php echo P4A_ICONS_PATH?>/loading.gif' alt='' /> Loading... </div>
<div class='p4a_system_messages'>
	<?php foreach (P4A::singleton()->getRenderedMessages() as $message): ?>
	<div class='p4a_system_message'><?php echo $message ?></div>
	<?php endforeach; ?>
</div>
<?php echo $this->maskOpen()?>

<?php if (isset($sidebar_left)): $_sidebar_left_width='280';?>
<div id="p4a_sidebar_left" style="padding-top:<?php echo $_top_margin+10?>px; width:<?php echo $_sidebar_left_width?>px;">
	<?php echo $sidebar_left?>
</div>
<?php endif; ?>

<?php if (isset($sidebar_right)):  $_sidebar_right_width='280';?>
<div id="sidebar_right" style="padding-top:<?php echo $_top_margin+10?>px; width:<?php echo $_sidebar_right_width?>px;">
	<?php echo $sidebar_right?>
</div>
<?php endif; ?>

<!-- TOP -->
<div id="p4a_top_container">
	<?php if (isset($menu)): ?>
	<div id="p4a_menu">
		<?php echo $menu?>
		<div class="br"></div>
	</div>
	<?php endif; ?>

	<?php if (isset($top)): ?>
	<div id="p4a_top">
		<?php echo $top?>
	</div>
	<?php endif; ?>
</div>

<!-- MAIN  -->
<div id="p4a_main_container" style="margin-top:<?php echo $_top_margin?>px; <?php if (isset($_sidebar_left_width)) echo "margin-left:{$_sidebar_left_width}px;"?> <?php if (isset($_sidebar_right_width)) echo "margin-right:{$_sidebar_right_width}px;"?>">
	<?php if (strlen($_title)): ?>
	<h2><?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?></h2>
	<?php endif; ?>

	<?php if (isset($main)): ?>
	<div id="p4a_main_inner_container">
		<?php echo $main?>
	</div>
	<?php endif; ?>

	<!-- Removing the following section is forbidden -->
	<div id="p4a_footer">
		<?php if (!$this instanceof P4A_Login_Mask): ?>
		<a href="<?php echo P4A_APPLICATION_SOURCE_DOWNLOAD_URL ?>"><?php echo __("Download application's source code") ?></a>
		<br />
		<?php endif; ?>
		Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a> <?php echo P4A_VERSION?>
	</div>
</div>

<?php echo $this->maskClose()?>
</div>

</body>
</html>