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
 * @package   theme_lambda2
 * @copyright 2025 redPIthemes
 *
 */

defined('MOODLE_INTERNAL') || die();

function theme_lambda2_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $theme = theme_config::load('lambda2');

    if ($context->contextlevel == CONTEXT_SYSTEM ) {
        if ($filearea === 'page_bg_img') {
        	return $theme->setting_file_serve('page_bg_img', $args, $forcedownload, $options);
        } else if (preg_match("/slideshow_image_[1-9][0-9]*/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'login_bg_img_1') {
            return $theme->setting_file_serve('login_bg_img_1', $args, $forcedownload, $options);
    	} else if ($filearea === 'login_bg_img_2') {
            return $theme->setting_file_serve('login_bg_img_2', $args, $forcedownload, $options);
    	} else if ($filearea === 'login_bg_img_3') {
            return $theme->setting_file_serve('login_bg_img_3', $args, $forcedownload, $options);
    	} else if ($filearea === 'login_bg_img_4') {
            return $theme->setting_file_serve('login_bg_img_4', $args, $forcedownload, $options);
        } else if ($filearea === 'dashboard_banner_img') {
            return $theme->setting_file_serve('dashboard_banner_img', $args, $forcedownload, $options);
        } else if ($filearea === 'mycourses_banner_img') {
            return $theme->setting_file_serve('mycourses_banner_img', $args, $forcedownload, $options);
    	} else if ($filearea === 'login_bg_vid') {
            return $theme->setting_file_serve('login_bg_vid', $args, $forcedownload, $options);
    	} else if ($filearea === 'favicon') {
            return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
    	} else if ($filearea === 'header_background') {
        	return $theme->setting_file_serve('header_background', $args, $forcedownload, $options);
    	} else if ($filearea === 'fonts_file_body') {
        	return $theme->setting_file_serve('fonts_file_body', $args, $forcedownload, $options);
    	} else if ($filearea === 'fonts_file_headings') {
        	return $theme->setting_file_serve('fonts_file_headings', $args, $forcedownload, $options);
		} else {
        	send_file_not_found();
    	}
    } else {
        send_file_not_found();
    }
}

function theme_lambda2_get_main_scss_content($theme) {
    global $CFG;
  	global $OUTPUT;

    $scss = file_get_contents($CFG->dirroot . '/theme/lambda2/scss/main.scss');
	$logoheight = $theme->settings->logo_height;
	$google_fonts = $theme->settings->fonts_source;
		if ($google_fonts == 1) {
			$font_body = $theme->settings->font_body;
			$font_body = str_replace("+", " ", $font_body);
			$font_heading = $theme->settings->font_heading;
			$bodyweight = 400;
			$headingweight = 700;
			switch ($font_heading) {
				case "Abril+Fatface":
				$headingweight = 400;
				break;
				case "Bevan":
				$headingweight = 400;
				break;
				case "Bree+Serif":
				$headingweight = 400;
				break;
				case "Cantata+One":
				$headingweight = 400;
				break;
				case "Imprima":
				$headingweight = 400;
				break;
				case "Lobster":
				$headingweight = 400;
				break;
				case "Pacifico":
				$headingweight = 400;
				break;
				case "Pontano+Sans":
				$headingweight = 400;
				break;
				case "Raleway":
				$headingweight = 500;
				break;
				case "Roboto":
				$headingweight = 500;
				break;
				case "Sansita+One":
				$headingweight = 400;
				break;
			}
			$font_heading = str_replace("+", " ", $font_heading);
		}
		else {
			$font_heading = 'custom_heading_font';
			$headingweight = $theme->settings->font_headings_weight;
            switch ($headingweight) {
                case 1:
                    $headingweight = 700;
                    break;
                case 2:
                    $headingweight = 400;
                    break;
                case 3:
                    $headingweight = 300;
                    break;
            }
            if ($headingweight )
			if (!is_null($theme->setting_file_url('fonts_file_headings', 'fonts_file_headings'))) {
                $font_heading_src = "url(".$theme->setting_file_url('fonts_file_headings', 'fonts_file_headings').")";
            }
			$font_body = 'custom_body_font';
			if (!is_null($theme->setting_file_url('fonts_file_body', 'fonts_file_body'))) {
                $font_body_src = "url(".$theme->setting_file_url('fonts_file_body', 'fonts_file_body').")";
            }
		}

        $scss = theme_lambda2_set_headingfont($scss, $font_heading, $headingweight);
        $scss = theme_lambda2_set_bodyfont($scss, $font_body);
        $scss = theme_lambda2_set_fontface($scss, $google_fonts, $font_heading, $font_body, $font_heading_src, $font_body_src);
		
	$page_layout = $theme->settings->page_layout;
	
	if (!is_null($theme->setting_file_url('page_bg_img', 'page_bg_img'))) {
		$pagebackground = $theme->setting_file_url('page_bg_img', 'page_bg_img');
		if ($theme->settings->page_bg_repeat == 0) {$repeat = 'no-repeat fixed 0 0'; $size = 'cover';}
		else {$repeat = 'repeat fixed 0 0'; $size = 'auto';}
        $opacity = $theme->settings->page_bg_img_opacity;
        $bgcolor = $theme->settings->page_bg_color;
		$scss = theme_lambda2_set_backgroundimage($scss, $page_layout, $pagebackground, $repeat, $size, $bgcolor, $opacity);
	}
	
	if (!is_null($theme->setting_file_url('header_background', 'header_background'))) {
		$headerbackground = $theme->setting_file_url('header_background', 'header_background');
		$repeat = $theme->settings->header_bg_repeat;
		$scss = theme_lambda2_set_headerbackgroundimage($scss, $headerbackground, $repeat);
	}
	
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/lambda2/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/lambda2/scss/preset/plain.scss');
    } else if ($filename == 'legacy.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/lambda2/scss/preset/legacy.scss');
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/lambda2/scss/preset/default.scss');
    }

    return $scss;
}

function theme_lambda2_get_pre_scss($theme) {
    global $CFG;
	static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('lambda2');
    }

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'maincolor' => ['maincolor'],
        'page_bg_color' => ['page_bg_color'],
		'link_color' => ['link_color'],
		'secondcolor' => ['secondcolor'],
        'button_border_radius' => ['button_border_radius'],
		'headercolor' => ['headercolor'],
        'headerfontcolor' => ['headerfontcolor'],
		'menufirstlevelcolor' => ['menufirstlevelcolor'],
		'menufirstlevel_linkcolor' => ['menufirstlevel_linkcolor'],
		'menusecondlevelcolor' => ['menusecondlevelcolor'],
		'menusecondlevel_linkcolor' => ['menusecondlevel_linkcolor'],
        'drawer_bg_color' => ['drawer_bg_color'],
        'drawer_font_color' => ['drawer_font_color'],
		'socials_icons_color' => ['socials_icons_color'],
        'socials_text_color' => ['socials_text_color'],
		'font_body_size' => ['font_body_size'],
        'body_color' => ['body_color'],
        'heading_color' => ['heading_color'],
		'footercolor' => ['footercolor'],
		'footerheadingcolor' => ['footerheadingcolor'],
		'footertextcolor' => ['footertextcolor'],
		'copyrightcolor' => ['copyrightcolor'],
		'copyright_textcolor' => ['copyright_textcolor'],
        'page_width' => ['page_width'],
        'logo_height' => ['logo_height'],
        'cbanner_height' => ['cbanner_height'],
        'compact_logo_height' => ['compact_logo_height'],
        'header_border_width' => ['header_border_width'],
        'footer_border_width' => ['footer_border_width'],
        'dark_mode_heading_color' => ['dark_mode_heading_color'],
        'dark_mode_body_color' => ['dark_mode_body_color'],
        'dark_mode_link_color' => ['dark_mode_link_color'],
        'dark_mode_headerbgcolor' => ['dark_mode_headerbgcolor'],
        'dark_mode_page_bg_color' => ['dark_mode_page_bg_color'],
        'dark_mode_footerbgcolor' => ['dark_mode_footerbgcolor'],
        'dark_mode_fcpbgcolor' => ['dark_mode_fcpbgcolor'],
        'dark_mode_footertxtcolor' => ['dark_mode_footertxtcolor']
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    if($theme->settings->header_border == 2) {
        $scss .= '$' . 'header_border_width' . ': ' . '0' . ";\n";
    }

    return $scss;
}

function theme_lambda2_get_extra_scss($theme) {
    global $SITE, $CFG;
    $xscss = '';
	
	$socials_lambda_bg = $theme->settings->socials_lambda_bg;
    if($theme->settings->socials_position == '1') {
        $xscss .= '#footer-top > .container-fluid {background: ';
    } else if($theme->settings->socials_position == '2') {
        $xscss .= '.socials-lambda {background: ';
    }
    if ($xscss !== '') {
        switch ($socials_lambda_bg) {
            case 0:
                $xscss .= 'transparent;}';
                break;
            case 1:
                $xscss .= 'rgba(0,0,0,0.025);}';
                //$xscss .= '.layout-boxed #page #main-header {background: #fff;}';
                break;
            case 2:
                $xscss .= $theme->settings->maincolor.';}';
                break;
            case 3:
                $xscss .= $theme->settings->secondcolor.';}';
                break;
            case 4:
                $xscss .= $theme->settings->copyrightcolor.';}';
                break;
        }
    }

    $infotxt = get_string('info');
    $xscss .= '.path-mod #page-content .activity-header.has-content:before {content: "'.$infotxt.'";}';

    $home_button = $theme->settings->home_button;
    $shortsitename = format_string($SITE->shortname);
    if ($home_button == 1) {
        $xscss .= '.lambda-nav .moremenu .navbar-nav > li[data-key="home"] > a::before {content: "'.$shortsitename.'"; font-size: 1rem;} .lambda-nav .moremenu .navbar-nav > li[data-key="home"] > a {font-size: 0;}';
    }
    if ($home_button == 2) {
        $xscss .= '.lambda-nav .moremenu .navbar-nav > li[data-key="home"] > a::before {content: "\e907"; font-family: lambda-icons; font-size: 1.15rem; border-radius: 50%; width: 30px; height: 30px; text-align: center; line-height: 30px;} .lambda-nav .moremenu .navbar-nav > li[data-key="home"] > a {font-size: 0;}';
    }
    if ($home_button == 3) {
        $xscss .= '.lambda-nav .moremenu .navbar-nav > li[data-key="home"] {display: none;}';
    }

    $activity_icons = $theme->settings->activity_icons;
    if ($activity_icons == 2) {
        $xscss .= '.activityiconcontainer {background-color: transparent !important; padding: 0; width: 24px !important; height: 24px !important;} .activityiconcontainer img.activityicon {filter: none !important;} .activityiconcontainer img.activityicon[title="H5P"] {filter: brightness(0.05);} .text-uppercase.small {display: none;} .pagelayout-incourse .page-context-header {align-items: center;} .pagelayout-incourse .page-context-header .page-header-image {display: flex;} .activity-item .activity-grid {align-items: baseline !important;}';
    }
    if ($activity_icons == 3) {
        $xscss .= '.activityiconcontainer {background-color: '.$theme->settings->maincolor.' !important;} .activityiconcontainer img.activityicon {filter: none !important;} .activityiconcontainer.isbranded img.activityicon {filter: brightness(0.05);} .text-uppercase.small {display: none;} .pagelayout-incourse .page-context-header {align-items: center;} .pagelayout-incourse .page-context-header .page-header-image {display: flex;} .activity-item .activity-grid {align-items: baseline !important;} .activity-item .activity-grid .activityiconcontainer {margin-right: 16px !important;} .activity-item .activityiconcontainer {width: 24px !important; height: 24px !important; min-width: 24px !important;} .page-context-header .activityiconcontainer {width: 26px !important; height: 26px !important; min-width: 26px !important;}';
    }
    if ($activity_icons == 4) {
        $xscss .= '.activityiconcontainer {background-color: '.$theme->settings->maincolor.' !important;} .activityiconcontainer img.activityicon {filter: brightness(0) invert(1) !important;} .text-uppercase.small {display: none;} .pagelayout-incourse .page-context-header {align-items: center;} .pagelayout-incourse .page-context-header .page-header-image {display: flex;} .activity-item .activity-grid {align-items: baseline !important;} .activity-item .activity-grid .activityiconcontainer {margin-right: 14px !important;} .activity-item .activityiconcontainer {width: 28px !important; height: 28px !important; min-width: 28px !important;} .page-context-header .activityiconcontainer {width: 34px !important; height: 34px !important; min-width: 34px !important;}';
    }
    if ($activity_icons == 5) {
        $xscss .= '.activityiconcontainer {background-color: '.$theme->settings->secondcolor.' !important;} .activityiconcontainer img.activityicon {filter: none !important;} .activityiconcontainer.isbranded img.activityicon {filter: brightness(0.05);} .text-uppercase.small {display: none;} .pagelayout-incourse .page-context-header {align-items: center;} .pagelayout-incourse .page-context-header .page-header-image {display: flex;} .activity-item .activity-grid {align-items: baseline !important;} .activity-item .activity-grid .activityiconcontainer {margin-right: 16px !important;} .activity-item .activityiconcontainer {width: 24px !important; height: 24px !important; min-width: 24px !important;} .page-context-header .activityiconcontainer {width: 26px !important; height: 26px !important; min-width: 26px !important;}';
    }
    if ($activity_icons == 6) {
        $xscss .= '.activityiconcontainer {background-color: '.$theme->settings->secondcolor.' !important;} .activityiconcontainer img.activityicon {filter: brightness(0) invert(1) !important;} .text-uppercase.small {display: none;} .pagelayout-incourse .page-context-header {align-items: center;} .pagelayout-incourse .page-context-header .page-header-image {display: flex;} .activity-item .activity-grid {align-items: baseline !important;} .activity-item .activity-grid .activityiconcontainer {margin-right: 14px !important;} .activity-item .activityiconcontainer {width: 28px !important; height: 28px !important; min-width: 28px !important;} .page-context-header .activityiconcontainer {width: 34px !important; height: 34px !important; min-width: 34px !important;}';
    }
    if ($activity_icons == 7) {
        $xscss .= '.activityiconcontainer:not(.isbranded) .activityicon:not(.nofilter) {filter: brightness(0) invert(1) !important;} .activityiconcontainer.smaller {width: 40px; height: 40px; max-width: 40px; max-height: 40px;} .activityiconcontainer.smaller img.activityicon {width: 30px; height: auto;} .activityiconcontainer.isbranded {background-color: #d4d4d4 !important;} .activityiconcontainer.assessment {background-color: #eb66a2;} .activityiconcontainer.content, .activityiconcontainer.interactivecontent {background-color: #399be2;} .activityiconcontainer.communication {background-color: #11a676;} .activityiconcontainer.collaboration {background-color: #f7634d;}';       
    }

    $category_layout = $theme->settings->category_layout;
    if ($category_layout != 0) {
        $xscss .= '.frontpage-course-list-enrolled, .courses.frontpage-course-list-all, .course_category_tree .category-browse, .course_category_tree .content .courses {display: flex; flex-wrap: wrap; justify-content: space-evenly;} #frontpage-category-combo .course_category_tree .content .courses {justify-content: flex-start;}';
    }

    $dashboard_layout = $theme->settings->dashboard_block_columns;

    if ($dashboard_layout > 0) {
        $xscss .= '.pagelayout-mydashboard #block-region-content {column-count: 2; column-gap: 2rem; margin: 1rem; padding: 0;}';
        $xscss .= '.pagelayout-mydashboard #block-region-content section.block {display: flex; width: 100%;}';
	}
    if ($dashboard_layout == 2) {
        $xscss .= '.pagelayout-mydashboard #block-region-content section.block:first-of-type {display: block; width: 100%; column-span: all;}';
	}
    if ($dashboard_layout == 3) {
        $xscss .= '.pagelayout-mydashboard #block-region-content section.block:last-of-type {display: block; width: 100%; column-span: all;}';
	}
    
    if (!is_null($theme->setting_file_url('dashboard_banner_img', 'dashboard_banner_img'))) {
        $dashboard_banner_img = $theme->setting_file_url('dashboard_banner_img', 'dashboard_banner_img');
        $xscss .= '#page-my-index.pagelayout-mydashboard #page-header.header-maxwidth {background-image: url("'.$dashboard_banner_img.'"); background-size: cover; background-position-y: 50%; border-radius: 4px; min-height: '.$theme->settings->banner_height.'; position: relative; margin-top: 1.5rem; margin-bottom: 1.5rem;}';
        $xscss .= '#page-my-index.pagelayout-mydashboard #page-header h1.h2 {color: #fff; padding: .75rem 1.5rem !important; font-weight: 400 !important; background: '.$theme->settings->maincolor.'; border-radius: .2rem; line-height: 1; position: absolute; bottom: 2rem; left: 2rem;}';
        $xscss .= '#page-my-index.pagelayout-mydashboard #page-header h1.h2, #page-my-index.pagelayout-mydashboard #page-header h1.h2.mb-3.mt-3 {margin-top: 0 !important; margin-bottom: 0 !important;}';
        $xscss .= '#page-my-index.pagelayout-mydashboard.page-header-style-02 #lambda-page-header #page-header .w-100 {justify-content: end !important;} #page-my-index.pagelayout-mydashboard.editing #lambda-page-header #page-header .singlebutton {margin: .5rem;}' ;
    }

    if (!is_null($theme->setting_file_url('mycourses_banner_img', 'mycourses_banner_img'))) {
        $mycourses_banner_img = $theme->setting_file_url('mycourses_banner_img', 'mycourses_banner_img');
        $xscss .= '#page-my-index.pagelayout-mycourses #page-header.header-maxwidth {background-image: url("'.$mycourses_banner_img.'"); background-size: cover; background-position-y: 50%; border-radius: 4px; min-height: '.$theme->settings->banner_height.'; position: relative; margin-top: 1.5rem; margin-bottom: 1.5rem;}';
        $xscss .= '#page-my-index.pagelayout-mycourses #page-header h1.h2 {color: #fff; padding: .75rem 1.5rem !important; font-weight: 400 !important; background: '.$theme->settings->maincolor.'; border-radius: .2rem; line-height: 1; position: absolute; bottom: 2rem; left: 2rem;}';
        $xscss .= '#page-my-index.pagelayout-mycourses #page-header h1.h2, #page-my-index.pagelayout-mycourses #page-header h1.h2.mb-3.mt-3 {margin-top: 0 !important; margin-bottom: 0 !important;}';
        $xscss .= '#page-my-index.pagelayout-mycourses #page-header .d-flex.align-items-center {margin: .5rem 1rem;} #page-my-index.pagelayout-mycourses.page-header-style-02 #lambda-page-header #page-header .w-100 {display: flex; justify-content: end !important;}';
    }

    $login_bg_type = $theme->settings->login_bg_type;

    if (($login_bg_type == 0) && (!is_null($theme->setting_file_url('login_bg_img_1', 'login_bg_img_1')))) {
		$loginbgimg = $theme->setting_file_url('login_bg_img_1', 'login_bg_img_1');
		$repeat = $theme->settings->login_bg_repeat;
        $xscss .= '.pagelayout-login.lambda-login.bg-img #page {background: url("'.$loginbgimg.'")';
        if ($repeat == 0) {$xscss .= ' no-repeat 0 50%; background-size: cover; background-attachment: fixed';}
        $xscss .= ';}';
	}
    if ($login_bg_type == 3) {
		$col1 = $theme->settings->login_bgcolor_gradient;
		$col2 = $theme->settings->login_bgcolor;
        $xscss .= '.pagelayout-login.lambda-login.bg-color {background-image: linear-gradient(to right,'.$col1.' 0%,'.$col2.' 100%);}';
	}

    $maincolor = $theme->settings->maincolor;
    $headerborderwidth = $theme->settings->header_border_width;
    $headerbordercolor = $theme->settings->header_border_color;
    switch ($headerbordercolor) {
        case 0:
            $bordercolor = $maincolor;
            break;
        case 1:
            $bordercolor = $theme->settings->secondcolor;
            break;
        case 2:
            $bordercolor = 'rgba(0,0,0,.1)';
            break;
        case 3:
            $bordercolor = 'rgba(255,255,255,.1)';
            break;
    }

	if($theme->settings->header_border == 0) {
        if($theme->settings->header_style >= 2) {
            $xscss .= '.lambda-nav {border-top: '.$headerborderwidth.' solid '.$bordercolor.';}';
        } else {
            if (theme_lambda2_get_setting('page_layout') == 1) {
                $xscss .= '.layout-boxed #main-header #header-logo {border-top: '.$headerborderwidth.' solid '.$bordercolor.';}';
            } else {
                $xscss .= '.wrapper-lambda {border-top: '.$headerborderwidth.' solid '.$bordercolor.';}';                
            }
        }
    } else if($theme->settings->header_border == 1) {
        if($theme->settings->header_style >= 2) {
            $xscss .= '.lambda-nav {border-bottom: '.$headerborderwidth.' solid '.$bordercolor.' !important;}';
            $xscss .= '.lambda-nav .nav-link, .lambda-nav li.nav-item {position: relative !important;} .lambda-nav.style-2 .nav-link.active, .lambda-nav.style-2 a.nav-link:hover {border-color: transparent !important; transition: 0s;} .lambda-nav .nav-item:not([data-key="home"]) .nav-link.active:before, .lambda-nav .nav-item:hover:before, #page-site-index .lambda-nav .nav-item[data-key="home"]:before {content: ""; width: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 8px solid '.$bordercolor.'; position: absolute; left: 0; right: 0; margin: auto; z-index: 1;} .lambda-nav .nav-link.active:before {bottom: -3px;} .lambda-nav .nav-item:hover:before, #page-site-index .lambda-nav .nav-item[data-key="home"]:before {bottom: 0;} .lambda-nav .lambda-search-bar:hover {border-color: transparent !important;} .lambda-nav.style-2 .nav-item a.nav-link:active, .lambda-nav.style-2 .nav-item a.nav-link:focus {border-color: transparent !important;}';
        } else {
            $xscss .= '#main-header {border-bottom: '.$headerborderwidth.' solid '.$bordercolor.';}';
        }
    }

    $footerborderwidth = $theme->settings->footer_border_width;
    $footerbordercolor = $theme->settings->footer_border_color;
    switch ($footerbordercolor) {
        case 0:
            $bordercolor = $maincolor;
            break;
        case 1:
            $bordercolor = $theme->settings->secondcolor;
            break;
        case 2:
            $bordercolor = 'rgba(0,0,0,.1)';
            break;
        case 3:
            $bordercolor = 'rgba(255,255,255,.1)';
            break;
    }
    $xscss .= '#page-footer {border-top: '.$footerborderwidth.' solid '.$bordercolor.';}';

    if($theme->settings->login_hide_userpw_form) {
        $xscss .= '#page-login-index form#login {display: none;}';
    }

    if($theme->settings->socials_monochrome) {
        $xscss .= '.socials-lambda .social_icons a.social {background: transparent !important;color: #a9a9a9 !important;width: auto;height: auto;border: none !important;}';
        $xscss .= '#main-header .socials-lambda .social_icons a.social {font-size: 1.35rem;}';
        $xscss .= '.socials-lambda .social_icons a.social:hover {color: '.$maincolor.' !important;}';
    }

    if($theme->settings->site_home_items_headings == '02') {
        $xscss .= '#frontpage-available-course-list > h2, #frontpage-category-names > h2, #frontpage-category-combo > h2, #site-news-forum > h2, #frontpage-course-list > h2 {margin-top: 2rem;}';
        $xscss .= '#frontpage-available-course-list > h2:after, #frontpage-category-names > h2:after, #frontpage-category-combo > h2:after, #site-news-forum > h2:after, #frontpage-course-list > h2:after {background: '.$maincolor.' none repeat scroll 0 0; border-radius: 4px; content: ""; display: block; height: 4px; position: relative; width: 85px; margin: 1rem 0 0 0;}';
    }
    if($theme->settings->site_home_items_headings == '03') {
        $xscss .= '#frontpage-available-course-list > h2, #frontpage-category-names > h2, #frontpage-category-combo > h2, #site-news-forum > h2, #frontpage-course-list > h2 {text-align: center;}';
    }
    if($theme->settings->site_home_items_headings == '04') {
        $xscss .= '#frontpage-available-course-list > h2, #frontpage-category-names > h2, #frontpage-category-combo > h2, #site-news-forum > h2, #frontpage-course-list > h2 {text-align: center; margin-top: 2rem;}';
        $xscss .= '#frontpage-available-course-list > h2:after, #frontpage-category-names > h2:after, #frontpage-category-combo > h2:after, #site-news-forum > h2:after, #frontpage-course-list > h2:after {background: '.$maincolor.' none repeat scroll 0 0; border-radius: 4px; content: ""; display: block; height: 4px; position: relative; width: 85px; margin: 1rem auto 0 auto;}';
    }
    
    if($theme->settings->fp_clean_layout) {
        $xscss .= '.pagelayout-frontpage #page, .pagelayout-incourse.course-1 #page {background: #fff !important;} .pagelayout-frontpage #page-content, .pagelayout-incourse.course-1 #page-content {border: none;} .pagelayout-incourse.course-1 #footer-top, .pagelayout-frontpage #footer-top {background-color: '.$theme->settings->page_bg_color.';}';
        if($theme->settings->header_style == 3) {
            $compact_logo_height = $theme->settings->compact_logo_height;
            $compact_logo_height = intval($compact_logo_height);
            $header_border_width = $theme->settings->header_border_width;
            $header_border_width = intval($header_border_width);
            $top = ($compact_logo_height + $header_border_width) * (-1);
            $xscss .= '#page-site-index.header-style-2.header-float #fp-slider-wrapper {top: '.$top.'px;} #page-site-index.header-style-2.header-float #page-footer, .header-style-2.header-float.pagelayout-incourse.course-1 #page-footer {width: 100% !important; max-width: unset;}';
        }
    }

    if($theme->settings->fp_no_page_header) {
        $xscss .= '.course-1.path-mod-page, .course-1.path-mod-book {#lambda-incourse-header {padding: 1rem 0;} #lambda-incourse-header #page-header {display: none;}}';
    }

    if($theme->settings->footerlinkcolor == '1') {
        $xscss .= '#page-footer a:not(.btn):not(.dropdown-item) {color: '.$maincolor.';}';
    }
    if($theme->settings->footerlinkcolor == '2') {
        $xscss .= '#page-footer a:not(.btn):not(.dropdown-item) {color: '.$theme->settings->footertextcolor.'; text-decoration: underline;}';
    }

    $iconsbg = 'transparent';
    $userinitialsbg = 'transparent';
    if($theme->settings->header_style >= 2) {
        $eval_bg = theme_lambda2_eval_brightness($theme->settings->headercolor);
        if ($eval_bg > 128) {
            $userinitialsbg = 'rgba(0,0,0,.05)';
            $iconsbg = 'rgba(0,0,0,.05)';
        } else if ($eval_bg > 0) {
            $userinitialsbg = 'rgba(255,255,255,.1)';
            $iconsbg = 'rgba(255,255,255,.1)';
        }
    } else {
        $eval_iconsbg = theme_lambda2_eval_brightness($theme->settings->menufirstlevelcolor);
        if ($eval_iconsbg > 128) {
            $iconsbg = 'rgba(0,0,0,.05)';
        } else if ($eval_iconsbg > 0) {
            $iconsbg = 'rgba(255,255,255,.1)';
        }
        $eval_userinitialsbg = theme_lambda2_eval_brightness($theme->settings->headercolor);
        if ($eval_userinitialsbg > 128) {
            $userinitialsbg = 'rgba(0,0,0,.05)';
        } else if ($eval_userinitialsbg > 0) {
            $userinitialsbg = 'rgba(255,255,255,.1)';
        }
    }

    if($theme->settings->home_button == 2) {
        $xscss .= '.lambda-nav .moremenu .navbar-nav li[data-key="home"]>a::before, .lambda-search-bar i.lambda {background-color: '.$iconsbg.' !important;}';
    }
    $xscss .= '.lambda-search-bar:focus-within #lambda-navbarsearch-form, .navbar .editmode-switch-form .form-check.form-switch .form-check-label:before {background-color: '.$iconsbg.';}';
    $xscss .= '.lambda-nav .userinitials {background-color: '.$userinitialsbg.';}';

    $pagecolor = $theme->settings->page_bg_color;
    $pagecolor = strtolower($pagecolor);
    if ($pagecolor === '#fff' || $pagecolor === '#ffffff') {
        $xscss .= '#page #page-content {border: none;}';
    }

    $headercolor = $theme->settings->headercolor;
    $headercolor = strtolower($headercolor);
    if (!($headercolor === '#fff' || $headercolor === '#ffffff')) {
        $xscss .= '.lambda-login input#username, .lambda-login input#password {background-color: rgba(255, 255, 255, .2) !important; border-radius: .2rem !important; color: '.$theme->settings->headerfontcolor.';} .lambda-login .login-form .login-form-username:before, .lambda-login .login-form .login-form-password:before {left: 3px !important;} .lambda-login input#username::placeholder, .lambda-login input#password::placeholder {color: '.$theme->settings->headerfontcolor.' !important; opacity: .75 !important}';
    }

    if ($theme->settings->logo_maincolor) {
        $header_style = $theme->settings->header_style;
        if ($header_style <= 1) {
            $xscss .= '#main-header .logo img {background-color: '.$maincolor.'; border-radius: 4px;}';
        }
        if ($header_style >= 2) {
            $xscss .= '.lambda-nav.style-2.fixed-top {padding-left: 0 !important;} .lambda-nav.style-2.fixed-top a.navbar-brand {background-color: '.$maincolor.' !important;}';
        }
        if ($header_style == 3) {
            $xscss .= '.lambda-nav.style-2.fixed-top .navbar-brand {border-radius: 4px 0 0 4px;}';
        }
        $xscss .= '.loginform img#logoimage {background-color: '.$maincolor.'; border-radius: 4px;}';
    }

    if (($theme->settings->expanded_logo) && ($theme->settings->header_style==2)) {
        $height = $theme->settings->compact_logo_height;
        $height = substr($height, 0, -2);
        $height = (int)$height + 20;
        $xscss .= '.lambda-nav.style-2.fixed-top.navbar-expand, #main-header {padding: 0 5% !important;} .lambda-nav.style-2.fixed-top a.navbar-brand {border-radius: 0 0 4px 4px; height: '.$height.'px;} .lambda-nav.style-2.navbar.fixed-top .navbar-brand .logo {max-height: '.$height.'px;} header#main-header.socials {background: #5f6870;} @media (min-width: 768px) {#main-header.socials>.socials-lambda .socials.row > .col-md-6 {width: auto;} #main-header.socials > .socials-lambda .socials.row {justify-self: end;}} @media (max-width: 767px) {#main-header.socials .socials-lambda .social_contact {margin-top: 1rem;}} .lambda-nav.style-2.fixed-top a.navbar-brand {background-color: '.$theme->settings->headercolor.';}';
    }

    if ($theme->settings->login_form_pos =='3' || $theme->settings->login_form_pos =='4') {
        $xscss .= '.lambda-login.form-left .login-container, .lambda-login.form-right .login-container {margin: 1rem !important; border-radius: 4px; min-height: calc(100vh - 2rem);}';
    }

    if ($theme->settings->dark_mode_switcher) {
        switch ($theme->settings->dark_mode_logobg) {
            case 1:
                $xscss .= '[data-lambda-color-mode="dark"] #header-logo .logo > img, [data-lambda-color-mode="dark"] .lambda-nav img.logo {background-color: rgba(255,255,255,.85); border-radius: 1rem;}';
                break;
            case 2:
                $xscss .= '[data-lambda-color-mode="dark"] #header-logo .logo > img, [data-lambda-color-mode="dark"] .lambda-nav img.logo {background-color: rgba(205,255,255,.25); border-radius: 1rem;}';
                break;
            case 3:
                $xscss .= '[data-lambda-color-mode="dark"] #header-logo .logo > img, [data-lambda-color-mode="dark"] .lambda-nav img.logo {background-color: '.$maincolor.'; border-radius: 1rem;}';
                break;
        }
    }

    if ($theme->settings->page_layout == '1' && !$theme->settings->fp_clean_layout) {
        $xscss .= '.lambda-nav.style-0, .lambda-nav.style-1 {background: #fff;}';
        $xscss .= '.lambda-nav.style-0 .navbar.navbar-expand, .lambda-nav.style-1 .navbar.navbar-expand {background-color: '.$theme->settings->menufirstlevelcolor.'; padding: 0 8px;}';
        $xscss .= '.lambda-nav.style-0 .navbar, .lambda-nav.style-0 .lambda-search-bar, .lambda-nav.style-1 .navbar, .lambda-nav.style-1 .lambda-search-bar {border-radius: 4px;}';
        $xscss .= '.lambda-nav.style-0 a.nav-link, .lambda-nav.style-0 .moremenu, .lambda-nav.style-0 .langmenu, .lambda-nav.style-1 a.nav-link, .lambda-nav.style-1 .moremenu, .lambda-nav.style-1 .langmenu, .lambda-search-bar, .lambda-search-bar .search-container, .lambda-search-bar:focus-within i.lambda, .lambda-search-bar:focus-within #navbarsearchbox {height: 60px;}';
        $xscss .= '.lambda-nav.style-0 .lambda-search-bar, .lambda-nav.style-1 .lambda-search-bar {margin-right: 8px;}';
        $xscss .= '.layout-boxed.pagelayout-frontpage #fp-slider-wrapper {border-radius: 4px; padding-left: 15px; padding-right: 15px; padding-top: 1.5rem;}';
        $xscss .= '.layout-boxed.pagelayout-frontpage #fp-slider-wrapper ul.uk-slideshow-items {border-radius: 4px;}';
    }
	
	$xscss .= $theme->settings->customscss;
	
    return $xscss;
}

function theme_lambda2_set_fontface($scss, $google_fonts, $font_heading, $font_body, $font_heading_src, $font_body_src) {
    global $CFG;

    $tag = '[[setting:fontface]]';
	$replacement = '';
    if ($google_fonts == 1) {
        if ($font_heading == 'Open Sans') {
            $replacement .= '@font-face {font-weight: bold; font-family: "Open Sans"; src: url([[font:theme|OpenSans-Bold.ttf]]) format("truetype");}';
        }
        if ($font_body == 'Open Sans') {
            $replacement .= '@font-face {font-weight: normal; font-family: "Open Sans"; src: url([[font:theme|OpenSans-Regular.ttf]]) format("truetype");}';
        }
    } else {
        if ($font_heading_src != '') {$replacement .= '@font-face {font-family: "custom_heading_font"; src: '.$font_heading_src.';}';}
        if ($font_body_src != '') {$replacement .= ' @font-face {font-family: "custom_body_font"; src: '.$font_body_src.';}';}
    }
    $scss = str_replace($tag, $replacement, $scss);

    return $scss;
}

function theme_lambda2_set_headingfont($scss, $font_heading, $headingweight) {
    $tag = '[[setting:font_heading]]';
    $replacement = $font_heading;
    if (is_null($replacement)) {
        $replacement = 'Open Sans';
    }
    $scss = str_replace($tag, $replacement, $scss);
    $tag = '[[setting:headingweight]]';
    $replacement = $headingweight;
    if (is_null($replacement)) {
        $replacement = '700';
    }
    $scss = str_replace($tag, $replacement, $scss);
    return $scss;
}

function theme_lambda2_set_bodyfont($scss, $font_body) {
    $tag = '[[setting:font_body]]';
    $replacement = $font_body;
    if (is_null($replacement)) {
        $replacement = 'Open Sans';
    }
    $scss = str_replace($tag, $replacement, $scss);
    return $scss;
}

function theme_lambda2_set_backgroundimage($scss, $page_layout, $pagebackground, $repeat, $size, $bgcolor, $opacity) {
	$content = '';
    if ($page_layout == 0) {
        $content = '.layout-full #page {background: url("'.$pagebackground.'") '.$repeat.'; background-size: '.$size.';}';
        if ($opacity > 0) {$hexopac = dechex($opacity); $content .= '#page-wrapper-outer .wrapper-lambda {background: '.$bgcolor.$hexopac.';}';}
    }
	if ($page_layout == 1) {
        $content = '.layout-boxed {background: url("'.$pagebackground.'") '.$repeat.'; background-size: '.$size.';}';
        if ($opacity > 0) {$hexopac = dechex($opacity); $content .= '.wrapper-lambda-outer {background: '.$bgcolor.$hexopac.';}';}
    }
	$scss .= $content;
    return $scss;
}

function theme_lambda2_set_headerbackgroundimage($scss, $headerbackground, $repeat) {
	$content = '#main-header ';
    if (theme_lambda2_get_setting('page_layout') == 1) {
        $content = '#main-header #header-logo ';
    }
	if ($repeat == 0) {$content .= '{background-image: url("'.$headerbackground.'") no-repeat 0 50%; background-size: cover;}';}
	if ($repeat == 1) {$content .= '{background-image: url("'.$headerbackground.'");}';}
	$scss .= $content;
    return $scss;
}

function theme_lambda2_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('lambda2');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}

function theme_lambda2_get_pagetype() {
    global $PAGE;
    return $PAGE->pagetype;
}

function theme_lambda2_get_moodle_version() {
    global $CFG;
    if ($CFG->version >= 2026042000) {
        return 'm-52';
    } else if ($CFG->version >= 2025100600) {
        return 'm-51';
    } else {
        return 'm-50';
    }
}

function theme_lambda2_eval_brightness($color) {
    $hexcolor = ltrim($color, '#');
    if (preg_match('/^[0-9a-fA-F]{6}$/', $hexcolor)) {
        $red = hexdec(substr($hexcolor, 0, 2));
        $green = hexdec(substr($hexcolor, 2, 2));
        $blue = hexdec(substr($hexcolor, 4, 2));
        return (0.299 * $red) + (0.587 * $green) + (0.114 * $blue);
    }
    else {
        return -1;
    }
}

function theme_lambda2_check_enrolment() {
	global $PAGE, $COURSE;
	if ( $COURSE->id > 1 && $PAGE->pagetype === 'enrol-index')
	{
		return theme_lambda2_get_setting('course_enrolment_page');
	} else {
		return 0;
	}
}

function theme_lambda2_check_site_announcements() {
	global $PAGE, $COURSE;
	if ( $COURSE->id == 1 && $PAGE->pagetype === 'mod-forum-discuss')
	{
		return 1;
	} else {
		return 0;
	}
}

function theme_lambda2_get_course_custom_fields() {
    global $COURSE;
    $courseid = $COURSE->id;

    $content = '';
    $hasvalues = FALSE;
    $customcoursefields = array();
	$handler = \core_customfield\handler::get_handler('core_course', 'course');
	$customcoursefields = $handler->get_instance_data( $courseid, FALSE);

	if (!count($customcoursefields))
	{
		return $content;
	}

	$content .= '<div class="customfields-container">';

	foreach ( $customcoursefields as $currentfield )
	{
        $currenttype = $currentfield->get_field()->get('type');
        $currentfieldvalue = '';
        if ($currenttype == 'checkbox') {
            $currentfieldvalue = $currentfield->get_value() == 1 ? get_string('yes') : get_string('no');
        } else if ($currenttype == 'select') {
                $currentfieldvalue = $currentfield->get_field()->get_options()[$currentfield->get_value()];
        } else if ($currenttype == 'text') {
                $currentfieldvalue = $currentfield->get_value();
        } else if ($currenttype == 'date') {
                $currentfieldvalue = userdate( $currentfield->get_value(), get_string('strftimedatefullshort'));
        } else {
                $currentfieldvalue = $currentfield->get_value();
        }
        if(!empty($currentfieldvalue)) {
            $hasvalues = TRUE;
            $content .= '<div class="customfield customfield_'.$currenttype.'">';
            $content .= '<span class="customfieldname">'.format_text($currentfield->get_field()->get('name'), FORMAT_PLAIN).': </span>';
            $content .= '<span class="customfieldvalue">'.format_text($currentfieldvalue, FORMAT_MOODLE).'</span>';
            $content .= '</div>';
        }
	}
    if(!$hasvalues) {return '';}
	$content .= '</div>';
	return $content;
}

function theme_lambda2_get_courseprice($courseid) {
    global $DB;

    $result = new stdClass();
    $result->hascourseprice = false;
    $result->courseprice = null;
    $result->multiplecourseprices = false;
    $paymentplugins = array('paypal', 'fee', 'stripepayment');

    list($insql, $inparams) = $DB->get_in_or_equal($paymentplugins, SQL_PARAMS_QM);
    $params = array_merge(array($courseid, ENROL_INSTANCE_ENABLED), $inparams);
    $records = $DB->get_records_sql("
        SELECT id, enrol, cost, currency
          FROM {enrol}
         WHERE courseid = ?
           AND status = ?
           AND enrol $insql
           AND cost IS NOT NULL
    ", $params);

    if (empty($records)) {
        return $result;
    }

    $valid = array_filter($records, function($r) {
        return is_numeric($r->cost) && (float)$r->cost >= 0;
    });

    if (empty($valid)) {
        return $result;
    }
    $result->hascourseprice = true;
    $min = null;
    $seen = array();
    foreach ($valid as $r) {
        $cost = (float)$r->cost;
        $currency = isset($r->currency) ? (string)$r->currency : '';
        $pricekey = $currency . ':' . number_format($cost, 2, '.', '');
        $seen[$pricekey] = true;
        if ($min === null || $cost < $min->cost) {
            $min = (object)[
                'cost' => $cost,
                'currency' => $currency
            ];
        }
    }
    $result->courseprice = $min;
    $result->multiplecourseprices = (count($valid) > 1 && count($seen) > 1);

    return $result;
}

function theme_lambda2_get_fp_slideshow_ratio() {
    $ratio = '';
    $numberofslides = theme_lambda2_get_setting('slideshow_number_slides');
    
    for ($i = 1; $i <= $numberofslides; $i++) {
        $current_image = 'slideshow_image_'.$i;
        if (!empty(theme_lambda2_get_setting($current_image))) {
            $context = context_system::instance();
            $filename = theme_lambda2_get_setting($current_image);
            $fs = get_file_storage();
            $file = $fs->get_file($context->id, 'theme_lambda2', $current_image, 0, '/', $filename);
            $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($fileExtension !== 'mp4') {
                $imageinfo = $file->get_imageinfo();
                $height = $imageinfo['height'];
                $width = $imageinfo['width'];
                $ratio = 'ratio: '.$width.':'.$height.'; ';
            }            
        }
            break;
    }
    if ($ratio == '') {
        $ratio = 'ratio: 16:9; ';
    }
    if (theme_lambda2_get_setting('slideshow_advanced_ratio') != '') {
        $ratio = 'ratio: '.theme_lambda2_get_setting('slideshow_advanced_ratio').'; ';
    }
    return $ratio;
}

function theme_lambda2_is_course_index() {
    $currentUrl = $_SERVER['REQUEST_URI'];
    if (strpos($currentUrl, "/course/index.php") !== false) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function theme_lambda2_get_enrol_course_sections() {
    global $COURSE, $OUTPUT;

    $modinfo     = get_fast_modinfo($COURSE);
    $allsections = $modinfo->get_section_info_all();
    $sectionsmap = $modinfo->get_sections();
    $orderednums = [];
    $seen        = [];

    $addsectionnum = function(int $sectionnum) use (
        &$addsectionnum, &$orderednums, &$seen,
        $modinfo, $allsections, $sectionsmap
    ) {
        if (!isset($allsections[$sectionnum])) {
            return;
        }
        if (isset($seen[$sectionnum])) {
            return;
        }
        $seen[$sectionnum] = true;
        $section = $allsections[$sectionnum];
        if (empty($section->visible)) {
            return;
        }
        $orderednums[] = $sectionnum;
        if (!empty($sectionsmap[$sectionnum])) {
            foreach ($sectionsmap[$sectionnum] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if ($cm->deletioninprogress || empty($cm->visible)) {
                    continue;
                }
                if ($cm->modname === 'subsection') {
                    $subsecinfo = $modinfo->get_section_info_by_component('mod_subsection', (int)$cm->instance);
                    if ($subsecinfo) {
                        $addsectionnum((int)$subsecinfo->section);
                    }
                }
            }
        }
    };
    foreach ($allsections as $secnum => $secinfo) {
        if (!empty($secinfo->component)) {
            continue;
        }
        $addsectionnum((int)$secnum);
    }

    $content = '<div class="accordion lambda style-02" id="course-content-accordion-' . (int)$COURSE->id . '">';
    foreach ($orderednums as $sectionnum) {
        $section = $allsections[$sectionnum];
        $headerid  = 'header-' . $sectionnum;
        $contentid = 'content-' . $sectionnum;
        $content .= '<div class="card">';
        $content .= '<div class="card-header" id="'.$headerid.'">';
        $content .= '<a class="btn-header-link collapsed" data-bs-toggle="collapse" data-bs-target="#'.$contentid.'" aria-expanded="false" aria-controls="'.$contentid.'">'
                 .  get_section_name($COURSE, $section)
                 .  '</a>';
        $content .= '</div>';
        $content .= '<div id="' . $contentid . '" class="collapse" aria-labelledby="'.$headerid.'" data-bs-parent="#course-content-accordion-'.(int)$COURSE->id.'">';
        $content .= '<div class="card-body">';
        $content .= '<ul>';
        if (!empty($sectionsmap[$sectionnum])) {
            foreach ($sectionsmap[$sectionnum] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if ($cm->deletioninprogress || empty($cm->visible) || !$cm->has_view()) {
                    continue;
                }
                $iconurl  = (string)$OUTPUT->image_url('icon', $cm->modname);
                $content .= '<li class="module-'.s($cm->modname).'"><img src="'.$iconurl.'" alt="'.s($cm->name).'">'
                         .  format_string($cm->name, true, ['context' => $cm->context])
                         .  '</li>';
            }
        }
        $content .= '</ul>';
        $content .= '</div></div></div>';
    }

    $content .= '</div>';
    return $content;
}

function theme_lambda2_check_adminrole() {
    if (is_siteadmin()) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function theme_lambda2_get_courses_count($catid) {
    $chelper = new coursecat_helper();
    $chelper->set_show_courses(20);
    try {
        $coursecat = core_course_category::get($catid);
        $courses = $coursecat->get_courses($chelper->get_courses_display_options());
    } catch (Exception $e) {
        $courses = [];
    }
    return count($courses);
}
