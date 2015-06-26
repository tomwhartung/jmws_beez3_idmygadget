<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 * 
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');

// Check modules
$showRightColumn = ($this->countModules('position-3') or $this->countModules('position-6') or $this->countModules('position-8'));
$showbottom      = ($this->countModules('position-9') or $this->countModules('position-10') or $this->countModules('position-11'));
$showleft        = ($this->countModules('position-4') or $this->countModules('position-7') or $this->countModules('position-5'));

if ($showRightColumn == 0 and $showleft == 0)
{
	$showno = 0;
}

JHtml::_('behavior.framework', true);

// Get params
$color          = $this->params->get('templatecolor');
$navposition    = $this->params->get('navposition');
$headerImage    = $this->params->get('headerImage');
$doc            = JFactory::getDocument();
$app            = JFactory::getApplication();
$templateparams	= $app->getTemplate(true)->params;
$config         = JFactory::getConfig();
$bootstrap      = explode(',', $templateparams->get('bootstrap'));
$jinput         = JFactory::getApplication()->input;
$option         = $jinput->get('option', '', 'cmd');

if (in_array($option, $bootstrap))
{
	// Load optional rtl Bootstrap css and Bootstrap bugfixes
	JHtml::_('bootstrap.loadCss', true, $this->direction);
}

$doc->addStyleSheet($this->baseurl . '/templates/system/css/system.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/position.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/layout.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/print.css', $type = 'text/css', $media = 'print');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/general.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color) . '.css', $type = 'text/css', $media = 'screen,projection');

if ($this->direction == 'rtl')
{
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template_rtl.css');
	if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/css/' . $color . '_rtl.css'))
	{
		$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color) . '_rtl.css');
	}
}

JHtml::_('bootstrap.framework');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/md_stylechanger.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/hide.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/respond.src.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/template.js', 'text/javascript');

//
// Initialize Device Detection
//
global $jmwsIdMyGadget;
$jmwsIdMyGadget = null;
require_once 'jmws_idMyGadget_for_joomla/JmwsIdMyGadget.php';
$gadgetDetector = $this->params->get('gadgetDetector');

if ( $gadgetDetector == 'mobile_detect' )
{
	$jmwsIdMyGadget = new JmwsIdMyGadget( 'mobile_detect' );
}
else if ( $gadgetDetector == 'tera_wurfl' )
{
	$jmwsIdMyGadget = new JmwsIdMyGadget( 'tera_wurfl' );
}
else
{
	$jmwsIdMyGadget = new JmwsIdMyGadget( 'detect_mobile_browsers' );
}
//
// If device is a phone, add in the jquery mobile css and library
//
if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE )
{
	$doc->addStyleSheet( JmwsIdMyGadget::JQUERY_MOBILE_CSS_URL );
	$doc->addScript( JmwsIdMyGadget::JQUERY_MOBILE_JS_URL );
}
//
// Set the logo (file) and sitetitle and sitedescription (text) to one of the device-specific values
// This is also an excellent place to set other values as appropriate
//
$includeFontsizeDiv = TRUE;
if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE )
{
	$includeFontsizeDiv = FALSE;
	$logo = $this->params->get('logoFilePhone');
	$sitetitle = $this->params->get('sitetitlePhone');
	$sitedescription = $this->params->get('sitedescriptionPhone');
}
else if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_TABLET )
{
	$logo = $this->params->get('logoFileTablet');
	$sitetitle = $this->params->get('sitetitleTablet');
	$sitedescription = $this->params->get('sitedescriptionTablet');
}
else   // default to/assume we are on a desktop browser
{
	$logo = $this->params->get('logoFileDesktop');
	$sitetitle = $this->params->get('sitetitleDesktop');
	$sitedescription = $this->params->get('sitedescriptionDesktop');
}
//
// Set data-role attributes to be used with jQuery Mobile
//
$jqm_data_role_page = '';
$jqm_data_role_header = '';
$jqm_data_role_content = '';
$jqm_data_role_footer = '';
$jqm_data_theme = 'x';

if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE )
{
	$jqm_data_role_page = 'data-role="page"';
	$jqm_data_role_header = 'data-role="header"';
	$jqm_data_role_content = 'data-role="content"';
	$jqm_data_role_footer = 'data-role="footer"';
	$jqm_data_theme = '';
	if ( $this->countModules('phone-footer-nav') )
	{
		$mod_menu_idmygadget = JModuleHelper::getModule('mod_menu_idmygadget');
		$idMyGadgetParams = new JRegistry($mod_menu_idmygadget->params);
		$jqm_data_theme = 'data-theme="' . $idMyGadgetParams['jqm_data_theme'] . '"';
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<?php require __DIR__ . '/jsstrings.php';?>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes"/>
		<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />

		<jdoc:include type="head" />

		<!--[if IE 7]>
		<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
		<![endif]-->
	</head>
	<body id="shadow">
		<?php
			if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE )
			{
				print '<div ' .  $jqm_data_role_page . '>';
			}
		?>
		<?php if ($color == 'image'):?>
			<style type="text/css">
				.logoheader {
					background:url('<?php echo $this->baseurl . '/' . htmlspecialchars($headerImage); ?>') no-repeat right;
				}
				body {
					background: <?php echo $templateparams->get('backgroundcolor'); ?>;
				}
			</style>
		<?php endif; ?>

		<div id="all">
			<div id="back">
				<?php if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE ) : ?>
					<div <?php echo $jqm_data_role_header . ' ' . $jqm_data_theme ?> >
						<jdoc:include type="modules" name="phone-header-nav" style="none" />
					</div>
				<?php endif; ?>
				<header id="header">
					<div class="logoheader">
						<h1 id="logo">
						<?php if ($logo) : ?>
							<img src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($logo); ?>"
								  alt="<?php echo htmlspecialchars($sitetitle); ?>" />
						<?php endif;?>
						<?php if (!$logo AND $sitetitle) : ?>
							<?php echo htmlspecialchars($sitetitle); ?>
						<?php elseif (!$logo AND $config->get('sitename')) : ?>
							<?php echo htmlspecialchars($config->get('sitename')); ?>
						<?php endif; ?>
						<span class="header1">
						<?php echo htmlspecialchars($sitedescription); ?>
						</span></h1>
					</div><!-- end logoheader -->
					<ul class="skiplinks">
						<li><a href="#main" class="u2"><?php echo JText::_('TPL_BEEZ3_SKIP_TO_CONTENT'); ?></a></li>
						<li><a href="#nav" class="u2"><?php echo JText::_('TPL_BEEZ3_JUMP_TO_NAV'); ?></a></li>
						<?php if ($showRightColumn) : ?>
							<li><a href="#right" class="u2"><?php echo JText::_('TPL_BEEZ3_JUMP_TO_INFO'); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE ) : ?>
				<?php endif; ?>
					<h2 class="unseen"><?php echo JText::_('TPL_BEEZ3_NAV_VIEW_SEARCH'); ?></h2>
					<h3 class="unseen"><?php echo JText::_('TPL_BEEZ3_NAVIGATION'); ?></h3>
					<jdoc:include type="modules" name="position-1" />
					<div id="line">
						<?php if ( $includeFontsizeDiv ) : ?>
							<div id="fontsize"></div>
						<?php endif; ?>
						<h3 class="unseen"><?php echo JText::_('TPL_BEEZ3_SEARCH'); ?></h3>
						<jdoc:include type="modules" name="position-0" />
					</div> <!-- end line -->
				</header><!-- end header -->
				<div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>"
						<?php echo $jqm_data_role_content ?> >
					<div id="breadcrumbs">
						<jdoc:include type="modules" name="position-2" />
					</div>

					<?php if ($navposition == 'left' and $showleft) : ?>
						<nav class="left1 <?php if ($showRightColumn == null) { echo 'leftbigger';} ?>" id="nav">
							<jdoc:include type="modules" name="position-7" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
							<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
						</nav><!-- end navi -->
					<?php endif; ?>

					<div id="<?php echo $showRightColumn ? 'wrapper' : 'wrapper2'; ?>" <?php if (isset($showno)){echo 'class="shownocolumns"';}?>>
						<div id="main">

							<?php if ($this->countModules('position-12')) : ?>
								<div id="top">
									<jdoc:include type="modules" name="position-12" />
								</div>
							<?php endif; ?>

							<jdoc:include type="message" />
							<jdoc:include type="component" />

						</div><!-- end main -->
					</div><!-- end wrapper -->

					<?php if ($showRightColumn) : ?>
						<div id="close">
							<a href="#" onclick="auf('right')">
							<span id="bild">
								<?php echo JText::_('TPL_BEEZ3_TEXTRIGHTCLOSE'); ?>
							</span>
							</a>
						</div>

						<aside id="right">
							<h2 class="unseen"><?php echo JText::_('TPL_BEEZ3_ADDITIONAL_INFORMATION'); ?></h2>
							<jdoc:include type="modules" name="position-6" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-8" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-3" style="beezDivision" headerLevel="3" />
						</aside><!-- end right -->
					<?php endif; ?>

					<?php if ($navposition == 'center' and $showleft) : ?>
						<nav class="left <?php if ($showRightColumn == null) { echo 'leftbigger'; } ?>" id="nav" >

							<jdoc:include type="modules" name="position-7"  style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
							<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />

						</nav><!-- end navi -->
					<?php endif; ?>

					<div class="wrap"></div>
				</div> <!-- end contentarea -->
			</div><!-- back -->
		</div><!-- all -->

		<div id="footer-outer" <?php echo $jqm_data_role_footer . ' ' . $jqm_data_theme ?> >
			<?php if ($showbottom) : ?>
				<div id="footer-inner" >

					<div id="bottom">
						<div class="box box1"> <jdoc:include type="modules" name="position-9" style="beezDivision" headerlevel="3" /></div>
						<div class="box box2"> <jdoc:include type="modules" name="position-10" style="beezDivision" headerlevel="3" /></div>
						<div class="box box3"> <jdoc:include type="modules" name="position-11" style="beezDivision" headerlevel="3" /></div>
					</div>

				</div>
			<?php endif; ?>

			<div id="footer-sub">
				<footer id="footer">
					<jdoc:include type="modules" name="position-14" />
					<?php
						// If the gadget-detector is not installed, generate an error message
						//
						if ( ! $jmwsIdMyGadget->isInstalled() )
						{
							$linkToReadmeOnGithub =
								'<a href="' . $jmwsIdMyGadget->getLinkToReadme() . '" target="_blank">' .
								'the appropriate README.md file on github.</a>';
							$application = JFactory::getApplication();
							$application->enqueueMessage(
								JText::_('TPL_BEEZ3_IDMYGADGET_DETECTOR_NOT_INSTALLED') . $linkToReadmeOnGithub ,
								'error'
							);
						}
					?>
					<jdoc:include type="modules" name="phone-footer-nav" />
				</footer> <!-- end footer -->
			</div> <!-- #footer-sub -->
		</div> <!-- #footer-outer -->
		<jdoc:include type="modules" name="debug" />
		<?php
			if ( $jmwsIdMyGadget->getGadgetString() === JmwsIdMyGadget::GADGET_STRING_PHONE )
			{
				print '</div> <!-- ' .  $jqm_data_role_page . '-->';
			}
		?>
	</body>
</html>
