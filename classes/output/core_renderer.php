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

namespace theme_lambda2\output;
defined('MOODLE_INTERNAL') || die;

use html_writer;
use stdClass;
use moodle_url;
use context_course;
use theme_config;
use core_tag_collection;
use core_tag_tag;
use core_course_category;
use core_course_list_element;
use block_online_users\fetcher;

class core_renderer extends \theme_boost\output\core_renderer {

	public function headerloginform() {
		global $CFG;
		
		$wwwroot = '';
		if (empty($CFG->loginhttps)) {$wwwroot = $CFG->wwwroot;}
		else {$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);}
	
		$headerloginform = '';	
		$login_link_url = '';
		$login_link_txt = '';
		if (theme_lambda2_get_setting('login_link') == '1') {$login_link_url = $wwwroot.'/login/signup.php'; $login_link_txt = get_string('startsignup');}
		else if (theme_lambda2_get_setting('login_link') == '2') {$login_link_url = $wwwroot.'/login/forgot_password.php'; $login_link_txt = get_string('forgotten');}
		else if (theme_lambda2_get_setting('login_link') == '3') {$login_link_url = $wwwroot.'/login/index.php'; $login_link_txt = get_string('moodle_login_page','theme_lambda2');}
		if (theme_lambda2_get_setting('custom_login_link_url') != '') {$login_link_url = theme_lambda2_get_setting('custom_login_link_url');}
		if (theme_lambda2_get_setting('custom_login_link_txt') != '') {$login_link_txt = theme_lambda2_get_setting('custom_login_link_txt');}
		$lambda_login_link = '';
		if ($login_link_url != '' && $login_link_txt != '') {$lambda_login_link = '<a target="_self" href="'.$login_link_url.'">'.$login_link_txt.'</a>';}
		
		$headerloginoauth2 = '';
		if (theme_lambda2_get_setting('auth_googleoauth2')) {
			$authsequence = get_enabled_auth_plugins(true);
            $potentialidps = array();
            foreach ($authsequence as $authname) {
            	$authplugin = get_auth_plugin($authname);
   	    		$potentialidps = array_merge($potentialidps, $authplugin->loginpage_idp_list($this->page->url->out(false)));
       		}
			if (!empty($potentialidps)) {
				$headerloginoauth2 .= '<div class="lambda-oauth2"><h6 class="mb-0">'.get_string('potentialidps', 'auth').'</h6><div class="potentialidplist">';
				foreach ($potentialidps as $idp) {
					$headerloginoauth2 .= '<div class="potentialidp">';
					$headerloginoauth2 .= '<a class="btn login-identityprovider-btn btn-block" href="'.$idp['url']->out().'" title="'.s($idp['name']).'">';
					if (!empty($idp['iconurl'])) {
						$headerloginoauth2 .= '<img src="'.s($idp['iconurl']).'" width="24" height="24" class="me-2"/>';
					}
					$headerloginoauth2 .= s($idp['name']);
					$headerloginoauth2 .= '</a></div>';
				}
				$headerloginoauth2 .= '</div>';
			}			
			$headerloginoauth2 .= '<div class="forgotpass oauth2">'.$lambda_login_link.'</div>';
			if (!empty($potentialidps)) {$headerloginoauth2 .= '</div>';}
		}
		
		$lambdaloginform = '<form class="navbar-form pull-right" method="post" action="'.$wwwroot.'/login/index.php">
									<div id="block-login">
										<div class="user-form">
											<div id="user"><i class="lambda icon-person" aria-hidden="true"></i></div>
											<label for="inputName" class="lambda-sr-only">'.get_string('username').'</label>
											<input type="hidden" name="logintoken" value="'.s(\core\session\manager::get_login_token()).'" />
											<input id="inputName" type="text" name="username" placeholder="'.get_string('username').'" class="me-2">
										</div>
										<div class="pw-form">
											<div id="pass"><i class="lambda icon-https" aria-hidden="true"></i></div>
											<label for="inputPassword" class="lambda-sr-only">'.get_string('password').'</label>
											<input id="inputPassword" type="password" name="password" placeholder="'.get_string('password').'" class="me-2">
											<button type="submit" id="submit"><span class="lambda-sr-only">'.get_string('login').'</span><i class="fa fa-angle-right" aria-hidden="true"></i></button>
										</div>
									</div>
									<div class="forgotpass">'.$lambda_login_link.'</div>
									</form>';
		
			if (theme_lambda2_get_setting('auth_googleoauth2')) {
				$headerloginform .= $headerloginoauth2;
			} else {
				$headerloginform .=	$lambdaloginform;
			}
		
		return $headerloginform;
    }

	public function lambda2loginbutton() {
		global $CFG;

		$wwwroot = '';
		if (empty($CFG->loginhttps)) {$wwwroot = $CFG->wwwroot;}
		else {$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);}
		
		$loginbuttontype = '';
		if (theme_lambda2_get_setting('login_button_dest') == '0') {
			$loginbuttontype .= '<a href="'.$wwwroot.'/login/index.php" id="btn-lambda-login" class="btn btn-primary"><span>'.get_string('login').'</span></a>';
		} else {
			$loginbuttontype = '<button type="button" id="btn-lambda-login" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lambdaModalLogin"><span>'.get_string('login').'</span></button>';
		}
		$loginbutton = '<div class="lambda-login-button">'.$loginbuttontype.'</div>';
			
		return $loginbutton;
	}

	public function lambda2loginmodal() {
		global $CFG;

		$wwwroot = '';
		if (empty($CFG->loginhttps)) {$wwwroot = $CFG->wwwroot;}
		else {$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);}

		$loginmodal = '';
		if (theme_lambda2_get_setting('login_button_dest') !== '0') {
			$login_link_url = '';
			$login_link_txt = '';
			if (theme_lambda2_get_setting('login_link') == '1') {$login_link_url = $wwwroot.'/login/signup.php'; $login_link_txt = get_string('startsignup');}
			else if (theme_lambda2_get_setting('login_link') == '2') {$login_link_url = $wwwroot.'/login/forgot_password.php'; $login_link_txt = get_string('forgotten');}
			else if (theme_lambda2_get_setting('login_link') == '3') {$login_link_url = $wwwroot.'/login/index.php'; $login_link_txt = get_string('moodle_login_page','theme_lambda2');}
			if (theme_lambda2_get_setting('custom_login_link_url') != '') {$login_link_url = theme_lambda2_get_setting('custom_login_link_url');}
			if (theme_lambda2_get_setting('custom_login_link_txt') != '') {$login_link_txt = theme_lambda2_get_setting('custom_login_link_txt');}
			$lambda_login_link = '';
			if ($login_link_url != '' && $login_link_txt != '') {$lambda_login_link = '<a target="_self" href="'.$login_link_url.'">'.$login_link_txt.'</a>';}
			
			$loginmodal .= '
			<div class="modal fade" id="lambdaModalLogin" tabindex="-1" aria-labelledby="lambdaModalLogin" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content lambda-login">
						<div class="modal-header">
							<h5 class="modal-title">Login</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						</div>
						<div class="modal-body">

						<form class="login-form" action="'.$wwwroot.'/login/index.php" method="post">
							<input type="hidden" name="logintoken" value="'.s(\core\session\manager::get_login_token()).'" />
							<div class="login-form-username form-group">
								<label for="username" class="sr-only">'.get_string('username').'</label>
								<input type="text" name="username" id="username" class="form-control form-control-lg" placeholder="'.get_string('username').'">
							</div>
							<div class="login-form-password form-group">
								<label for="password" class="sr-only">'.get_string('password').'</label>
								<input type="password" name="password" id="password" value="" class="form-control form-control-lg" placeholder="'.get_string('password').'">
							</div>
							<div class="login-form-submit form-group">
								<button class="btn btn-primary w-100 btn-lg my-2 mx-0" type="submit" id="loginbtn">'. get_string('login').'</button>
							</div>
							<div class="login-form-forgotpassword form-group">'.$lambda_login_link.'</div>
						</form>

				</div>
			</div>
			</div>
			</div>';
		}

		return $loginmodal;
	}

	public function lambdasearch() {
		global $CFG;
		
		if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', \context_system::instance())) {
            $moodle_global_search = 0;
        } else {
			$moodle_global_search = 1;
		}

		$search_action = $CFG->wwwroot.'/course/search.php';
		if ($moodle_global_search) {
			$search_action = $CFG->wwwroot.'/search/index.php';
		}
		$search_label = get_string('searchcourses');
		if ($moodle_global_search) {
			$search_label = get_string('search', 'search');
		}

		$navbarsearch = '<form id="lambda-navbarsearch-form" autocomplete="off" method="get" action="'.$search_action.'" >
							<div class="search-container">
								<i class="lambda icon-search1" aria-hidden="true"></i>
								<input id="navbarsearchbox" type="text" name="q" data-region="input" autocomplete="off" aria-label="'.$search_label.'" placeholder="'.$search_label.'">
								<label for="navbarsearchbox" class="lambda-sr-only">'.$search_label.'</label>
							</div>
						</form>';

		return $navbarsearch;
	}

	public function lambda2_get_main_logo_url() {
		global $CFG, $OUTPUT;
        $logo = get_config('core_admin', 'logo');
        if (!empty($logo)) {
			return $OUTPUT->get_logo_url();
		} else {
			$logo = get_config('core_admin', 'logocompact');
			if (!empty($logo)) {
				return $OUTPUT->get_compact_logo_url();
			}
		}
		return false;
	}

	public function lambda2_has_main_logo() {
        $logo = $this->lambda2_get_main_logo_url();
        return !empty($logo);
	}

	public function lambda2_socials_links() {
		$socials_links = '';
	
		for ($i = 1; $i <= 3; $i++) {
			$socials_links_i = '';
			if ((theme_lambda2_get_setting('socials_link_icon_'.$i) != '') || (theme_lambda2_get_setting('socials_link_text_'.$i) != '')) {
				$socials_links_i = theme_lambda2_get_setting('socials_link_icon_'.$i).' <span>'.theme_lambda2_get_setting('socials_link_text_'.$i).'</span>';
				if (theme_lambda2_get_setting('socials_link_url_'.$i) != '') {
					$socials_links_i = '<a href="'.theme_lambda2_get_setting('socials_link_url_'.$i).'" target="_blank">'.$socials_links_i.'</a>';
				}
			}
			$socials_links .= $socials_links_i;
		}
		return $socials_links;
	}

	public function lambda2_socials_icons() {
		$socials_icons = '';
	
		for ($i = 1; $i <= 5; $i++) {
			$socials_icons_i = '';
			if ((theme_lambda2_get_setting('socials_icontypes_'.$i) != '') && (theme_lambda2_get_setting('socials_url_'.$i) != '')) {
				$socials_icons_i = '<a class="social '.theme_lambda2_get_setting('socials_icontypes_'.$i).'" href="'.theme_lambda2_get_setting('socials_url_'.$i).'" target="_blank"> </a>';
			}
			$socials_icons .= $socials_icons_i;
		}
		return $socials_icons;
	}

	public function lambda2mobilemenuitems() {
		global $CFG;
		$content = '<li class="nav-item"><a class="nav-link" href="'.$CFG->wwwroot.'/?redirect=0">'.get_string('home').'</a></li>';	
		if ($CFG->enabledashboard) {
			if ((isloggedin() && !isguestuser()) || (isguestuser() && $CFG->allowguestmymoodle)) {
				$content .= '<li class="nav-item"><a class="nav-link" href="'.$CFG->wwwroot.'/my/">'.get_string('myhome').'</a></li>';
			}
		}
		if (isloggedin() && !isguestuser()) {
			$content .= '<li class="nav-item"><a class="nav-link" href="'.$CFG->wwwroot.'/my/courses.php">'.get_string('mycourses').'</a></li>';
		}
		if (is_siteadmin()) {
			$content .= '<li class="nav-item"><a class="nav-link" href="'.$CFG->wwwroot.'/admin/search.php">'.get_string('administrationsite').'</a></li>';
		}
		return $content;
	}

	public function lambda2mobileplugin_output() {
		global $CFG;
		$content = '';
		$content .= '<div class="mobile_navbar_plugin">';
		$content .= '<a class="btn btn-link" href="'.$CFG->wwwroot.'/message/output/popup/notifications.php" role="button"><i class="icon fa fa-bell-o fa-fw " role="img"></i></a>';
		$content .= '<a class="btn btn-link ps-0" href="'.$CFG->wwwroot.'/message/index.php" role="button"><i class="icon fa fa-comment-o fa-fw" role="img"></i></a>';
		$content .= '</div>';
		return $content;
	}

	public function lambda2_get_course_fullname() {
		global $PAGE;
		$course = $PAGE->course;
		$coursecontext = context_course::instance($PAGE->course->id);
		return format_string($course->fullname, true, ['context' => $coursecontext]);
	}

	public function lambda2_get_course_category() {
		global $PAGE, $DB;
		$catid = $PAGE->course->category;
		$coursecontext = context_course::instance($PAGE->course->id);
		$category = $DB->get_record('course_categories', ['id' => $catid]);
		return format_string($category->name, true, ['context' => $coursecontext]);
	}

	public function lambda2_course_header() {
		global $PAGE;		
		$lambda2_course_header = $this->full_header();
		$coursetitlesetting = theme_lambda2_get_setting('course_title');
		if ($coursetitlesetting == '03') {
			$lambda2_course_header = str_replace('<h1 class="h2', '<h1 class="h2 sr-only', $lambda2_course_header);
			$lambda2_course_header = str_replace('class="w-100"', 'class="w-100 my-3"', $lambda2_course_header);
		} else if ($coursetitlesetting == '02') {
			$course = $PAGE->course;
			$coursecontext = context_course::instance($course->id);
			$coursename = format_string($course->fullname, true, ['context' => $coursecontext]);
			if ($course instanceof stdClass) {
				$course = new \core_course_list_element($course);
			}
			$coursefiles = $course->get_course_overviewfiles();
			foreach ($coursefiles as $file) {
				if ($isimage = $file->is_valid_image()) {
					$imgurl = moodle_url::make_pluginfile_url(
						$file->get_contextid(),
						$file->get_component(),
						$file->get_filearea(),
						$file->get_filepath(),
						$file->get_filename(),
						!$isimage
					);
					break;
				}
			}
			if (empty($imgurl)) {
				global $OUTPUT;
				$imgurl = $OUTPUT->get_generated_image_for_id($course->id);
			}
			$lambda2_course_header = str_replace('id="page-header" class="', 'id="page-header" class="w-img ', $lambda2_course_header);
			$inlinestyle = 'style="background-image: url('.$imgurl.');"';
			$lambda2_course_header = str_replace('<div class="w-100">', '<div class="courseheaderimage my-4 w-100" '. $inlinestyle .'>', $lambda2_course_header);
		}
		return $lambda2_course_header;
    }

	function back_to_course_button($cobj = null) {
		global $PAGE, $COURSE;

		$course = $cobj ?: ($PAGE->course ?? $COURSE ?? null);
		if (empty($course) || empty($course->id) || (int)$course->id == SITEID) {
			return '';
		}
		$course = get_course($course->id);

		$currentcmid = 0;
		if (!empty($PAGE->context) && $PAGE->context->contextlevel == CONTEXT_MODULE) {
			$currentcmid = (int)$PAGE->context->instanceid;
		} else if (!empty($PAGE->cm) && !empty($PAGE->cm->id)) {
			$currentcmid = (int)$PAGE->cm->id;
		} else {
			$maybe = optional_param('id', 0, PARAM_INT);
			if ($maybe) { $currentcmid = (int)$maybe; }
		}
		
		$PAGE->requires->js_call_amd('theme_lambda2/activity_nav', 'init');
		$courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
		$backlabel = get_string('backto', '', get_string('course'));
		$out  = '<div class="text-center mt-4 lambda-activity-nav"'
			. ' data-current-cmid="' . (int)$currentcmid . '">';
		$out .= '<a href="#" class="btn btn-outline-secondary me-2 btn-prev disabled"'
			. ' aria-disabled="true" role="button" title="">'
			. '« ' . get_string('previous') . '</a>';
		$out .= '<a class="btn btn-primary me-2 btn-back"'
			. ' href="' . $courseurl->out(false) . '">'
			. $backlabel . '</a>';
		$out .= '<a href="#" class="btn btn-outline-secondary btn-next disabled"'
			. ' aria-disabled="true" role="button" title="">'
			. get_string('next') . ' »</a>';
		$out .= '</div>';

		return $out;
	}

	function theme_lambda2_get_coursename()
	{
		global $PAGE;
		$course = $PAGE->course;
		return format_string($course->fullname, true);
	}

	function theme_lambda2_get_coursestartdate() {
		global $PAGE;
		if (!empty($PAGE->course->startdate)) {
			$coursestartdate = get_string('activitydate:opens','course').' ';
			$coursestartdate .= userdate($PAGE->course->startdate, get_string('strftimedatefullshort'));
			return $coursestartdate;
		} else {
			return get_string('nocoursestarttime');
		}
	}

	function theme_lambda2_get_courseenddate() {
		global $PAGE;
		if (!empty($PAGE->course->enddate)) {
			$courseenddate = get_string('activitydate:closes','course'). ' ';
			$courseenddate .= userdate($PAGE->course->enddate, get_string('strftimedatefullshort'));
		} else {
			$courseenddate = get_string('nocourseendtime', 'course');
		}
		return $courseenddate;
	}

	function theme_lambda2_show_courseprice() {
		global $DB, $COURSE;
		$courseid = $COURSE->id;
		$out = '';

		$cp = theme_lambda2_get_courseprice($courseid);
		if ($cp->hascourseprice) {
			$out = '<h6 class="price font-weight-light">'.get_string('cost','enrol_fee'). ': <em>';
			if ($cp->multiplecourseprices) {
				$out .= get_string('startingfrom').' '.$cp->courseprice->currency.' '.number_format($cp->courseprice->cost, 2, '.', '').'</em></h6>';
			} else {
				$out .= $cp->courseprice->currency.' '.number_format($cp->courseprice->cost, 2, '.', '').'</em></h6>';
			}
			return $out;
		} else {
			if (empty(theme_lambda2_get_setting('course_free_txt')) or theme_lambda2_get_setting('course_free_txt') == "") {
				return;
			} else {
				return '<h6 class="price font-weight-light">'.format_string(theme_lambda2_get_setting('course_free_txt')).'</h6>';
			}
		}
	}

	function theme_lambda2_show_coursetags() {
		global $PAGE, $COURSE, $OUTPUT;
		//$contid = $PAGE->context->id;
		$context = context_course::instance($COURSE->id);
		$taglist = core_tag_collection::get_tag_cloud(0, FALSE, 80, 'name', '', 0, $context->id, 1);
		
		//$context = context_course::instance($COURSE->id);
		//$taglist = core_tag_collection::get_tag_cloud($context->id, 'core_course', 'course');

		
		$taglist = $OUTPUT->render_from_template('core_tag/tagcloud', $taglist->export_for_template($OUTPUT));
		$coursetags = strip_tags($taglist, '<ul><li>');
		return str_replace('<li> ', '<li>', $coursetags);
	}

	function theme_lambda2_show_sharebuttons() {
		global $PAGE, $CFG;
		$showfacebook = theme_lambda2_get_setting('sharebuttons_facebook');
		$showtwitterx = theme_lambda2_get_setting('sharebuttons_twitterx');
		//$showthreads = theme_lambda2_get_setting('sharebuttons_threads');
		$showwhatsapp = theme_lambda2_get_setting('sharebuttons_whatsapp');
		$showlinkedin = theme_lambda2_get_setting('sharebuttons_linkedin');
		$showpinterest = theme_lambda2_get_setting('sharebuttons_pinterest');
		$sharebuttons = '';
		$siteurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		$coursename = format_string($PAGE->course->fullname, true);
		if ($showfacebook || $showtwitter || $showwhatsapp || $showlinkedin || $showpinterest) {
			$sharebuttons .= '<ul class = "lambda-sharebuttons">';
			if ($showfacebook) {
				$sharebuttons .= '<li class="share-button facebook">';
				$sharebuttons .= '<a href="https://www.facebook.com/sharer/sharer.php?u'.$siteurl.'"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a></li>';
			}
			if ($showtwitterx) {
				$sharebuttons .= '<li class="share-button twitter-x">';
				$sharebuttons .= '<a href="https://twitter.com/intent/tweet?text='.$coursename.'&amp;url='.$siteurl.'"><i class="lambda icon-x-twitter" aria-hidden="true"></i> '.get_string('share_on','theme_lambda2').' X</a></li>';
			}
			if ($showwhatsapp) {
				$sharebuttons .= '<li class="share-button whatsapp">';
				$sharebuttons .= '<a href="whatsapp://send?text='.$coursename.'%20'.$siteurl.'"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</a></li>';
			}
			if ($showlinkedin) {
				$sharebuttons .= '<li class="share-button linkedin">';
				$sharebuttons .= '<a href="https://www.linkedin.com/shareArticle?mini=true&amp;summary=&amp;title='.$coursename.'&amp;url='.$siteurl.'"><i class="fa fa-linkedin" aria-hidden="true"></i> LinkedIn</a></li>';
			}
			/*
			if ($showthreads) {
				$sharebuttons .= '<li class="share-button threads">';
				$sharebuttons .= '<a href="https://twitter.com/intent/tweet?text='.$coursename.'&amp;url='.$siteurl.'"><i class="lambda icon-threads" aria-hidden="true"></i> Threads</a></li>';
			}
			*/
			if ($showpinterest) {
				$sharebuttons .= '<li class="share-button pinterest">';
				$logo = $this->lambda2_get_main_logo_url();
				if (empty($logo)) {
					$logo = $CFG->wwwroot.'/pix/moodlelogo.png';
				}
				$sharebuttons .= '<a href="http://pinterest.com/pin/create/button/?url='.$siteurl.'&media='.$logo.'&description='.$coursename.'"><i class="fa fa-pinterest" aria-hidden="true"></i> pinterest</a></li>';
			}
			$sharebuttons .= '</ul>';
		}
		return $sharebuttons;
	}

	function theme_lambda2_get_coursesummary() {
		global $PAGE;
		$courseid = $PAGE->course->id;
		$coursecontext = context_course::instance($courseid);
		$coursesummary = $PAGE->course->summary;
		$coursesummary = file_rewrite_pluginfile_urls( $coursesummary, 'pluginfile.php', $coursecontext->id, 'course', 'summary', NULL );
		return format_text($coursesummary, FORMAT_HTML, ['noclean' => true]);
	}

	public function theme_lambda2_get_courseimage() {
		global $PAGE;
		$course = $PAGE->course;
		$context = context_course::instance($course->id);
		if ($course instanceof stdClass) {
			$course = new \core_course_list_element($course);
		}
		$coursefiles = $course->get_course_overviewfiles();
		foreach ($coursefiles as $file) {
			if ($isimage = $file->is_valid_image()) {
				$imgurl = moodle_url::make_pluginfile_url(
					$file->get_contextid(),
					$file->get_component(),
					$file->get_filearea(),
					$file->get_filepath(),
					$file->get_filename(),
					!$isimage
				);
				break;
			}
		}
		if (empty($imgurl)) {
			global $OUTPUT;
			$imgurl = $OUTPUT->get_generated_image_for_id($course->id);
		}
		return $imgurl;
	}

	function theme_lambda2_get_coursecategory() {
		global $PAGE, $DB;
		$categoryname;

		if (empty($PAGE->course->category)) {
			$catid = optional_param('categoryid', 0, PARAM_INT);
		} else {
			$catid = $PAGE->course->category;
		}
		if (!empty($catid)) {
			$category = $DB->get_record('course_categories', ['id' => $catid]);
			$categoryname = $category->name;
		}

		return $categoryname;
	}

    public function fp_slideshow() {
	    global $PAGE;
		$theme = $PAGE->theme;
		
		$slideshow = '';
		
		$slideshowparams = '';
		$slideshowclasses = '';
		$ratio = theme_lambda2_get_fp_slideshow_ratio();
		$slideshowparams .= $ratio;
		if (theme_lambda2_get_setting('slideshow_autoplay') == 1) {
			$slideshowparams .= 'autoplay: true; pause-on-hover: true; ';
			$slideshowparams .= 'autoplay-interval: '.theme_lambda2_get_setting('slideshow_autoplay_delay').'; ';
		}
		$transitionfx = theme_lambda2_get_setting('slideshow_transition');
		switch ($transitionfx) {
    		case 1:
        		$slideshowparams .= 'animation: slide; ';
        		break;
    		case 2:
        		$slideshowparams .= 'animation: fade; ';
        		break;
    		case 3:
        		$slideshowparams .= 'animation: scale; ';
        		break;
			case 4:
				$slideshowparams .= 'animation: pull; ';
				break;
		}
		$navstyle = theme_lambda2_get_setting('slideshow_navigation_style');
		$slideshowclasses .= 'navstyle-'.$navstyle;
		if (theme_lambda2_get_setting('slideshow_nav_hover') == 1) {$slideshowclasses .= ' uk-visible-toggle';}
		
		$slideshow .= '<div class="uk-position-relative uk-visible-toggle-custom uk-slideshow '.$slideshowclasses.'" tabindex="-1" uk-slideshow="'.$slideshowparams.'">';
		$slideshow .= '<ul class="uk-slideshow-items">';
		
		$numberofslides = theme_lambda2_get_setting('slideshow_number_slides');
		$emptyslides = 0;
		for ($i = 1; $i <= $numberofslides; $i++) {
			$html_current_slide = '';
			$current_image = 'slideshow_image_'.$i;
			$fileurl = $theme->setting_file_url($current_image, $current_image);
			if ($fileurl == "") {
				$emptyslides++;
			} else {
				$slideshow .= $this->fp_slideshow_current_image($fileurl, $i);
			}
		}
		
		if ($emptyslides < $numberofslides) {
		$slideshow .= '</ul>';
		
		$slidenavleft = '';
		$slidenavright = '';
		if (($navstyle <= 3) OR ($navstyle >= 6)) {
			$slidenavleft .= ' uk-position-center-left';
			$slidenavright .= ' uk-position-center-right';
		}
		if (theme_lambda2_get_setting('slideshow_nav_hover')) {
			$slidenavleft .= ' uk-hidden-hover';
			$slidenavright .= ' uk-hidden-hover';
		}
		if (($navstyle >= 4) AND ($navstyle <= 5)) {$slideshow .= '<div class="uk-slidenav-container uk-position-bottom-right">';}
		$slideshow .= '<a class="uk-position-small'.$slidenavleft.'" href="#" uk-slidenav-previous uk-slideshow-item="previous">';
		if ($navstyle == 1) {
			$slideshow .= '<div class="slider-button-prev"><span>'.get_string('prev').'</span></div>';
		}		
		$slideshow .= '</a>';
		$slideshow .= '<a class="uk-position-small'.$slidenavright.'" href="#" uk-slidenav-next uk-slideshow-item="next">';
		if ($navstyle == 1) {
			$slideshow .= '<div class="slider-button-next"><span>'.get_string('next').'</span></div>';
		}		
		$slideshow .= '</a>';
		if (($navstyle >= 4) AND ($navstyle <= 5)) {$slideshow .= '</div>';}
		
		if ($navstyle == 6) {
			$slideshow .='<div class="uk-position-bottom-center">';
			$slideshow .='<ul class="uk-dotnav">';
			for ($i = 0; $i < $numberofslides - $emptyslides; $i++) {
				$slideshow .='<li uk-slideshow-item="'.$i.'"><a href="#">Slideshow item '.$i.'</a></li>';
			}
			$slideshow .='</ul></div>';
		}
		
		$slideshow .= '</div>';
		}
		
		if ($emptyslides < $numberofslides) {
			return $slideshow;
		}
		else {
			return '';
		}
    }

	protected function fp_slideshow_current_image($fileurl, $i) {
		$current_caption = 'slideshow_caption_'.$i;
		$current_animation = 'slideshow_caption_pos_'.$i;
		$current_background = 'slideshow_bg_'.$i;
		$current_background_full = 'slideshow_bg_slide_'.$i;
		$current_caption_anim = '';
		$current_caption_pos = '';
		$current_caption_overlay = '';
		$html = '';

		switch (theme_lambda2_get_setting($current_animation)) {
			case 1:
				$current_caption_anim = 'uk-animation-slide-top-small';
				$current_caption_pos = 'uk-position-center p-lg-5 p-md-3 d-flex w-100 justify-content-center';
				break;
			case 2:
				$current_caption_anim = 'uk-animation-slide-left';
				$current_caption_pos = 'uk-position-center p-lg-5 p-md-3 d-flex justify-content-start';
				break;
			case 3:
				$current_caption_anim = 'uk-animation-slide-right';
				$current_caption_pos = 'uk-position-center p-lg-5 p-md-3 d-flex justify-content-end';
				break;
			default:
				$current_caption_anim = 'uk-animation-slide-top-small';
				$current_caption_pos = 'uk-position-center p-lg-5 p-md-3 d-flex w-100 justify-content-center';
		}

		switch (theme_lambda2_get_setting($current_background)) {
			case 1:
				$current_caption_overlay = ' uk-overlay-default';				
				break;
			case 2:
				$current_caption_overlay = ' uk-overlay-primary';
				break;
			case 3:
				$current_caption_overlay = ' uk-overlay-main';
				break;
			case 4:
				$current_caption_overlay = '';
				break;
			case 5:
				$current_caption_overlay = ' overlay-lambda-light';
				break;
			case 6:
				$current_caption_overlay = ' overlay-lambda-dark';
				break;
			default:
				$current_caption_overlay = ' uk-overlay-default';
		}

		if (theme_lambda2_get_setting($current_background_full)) {
			$html = '<li class = "overlay-full'.$current_caption_overlay.'" tabindex="-1">';
			if (theme_lambda2_get_setting($current_background) < 4) {
				$current_caption_overlay = '';
			}
		} else {
			$html = '<li tabindex="-1">';
		}

		$kenburns = '';
		if (theme_lambda2_get_setting('slideshow_kenburns') == 1) {
			$kenburns = ' uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left';
		}
		$html .= '<div class="uk-position-cover'.$kenburns.'">';
		$filetype = 'mp4';
		$pos = strpos(strtolower($fileurl), $filetype);
		if ($pos !== false) {
			$html .= '<video src="'.$fileurl.'" class="slide" autoplay loop muted playsinline uk-cover></video>';
		} else {
			$html .= '<img src="'.$fileurl.'" class="slide" alt="slideshow banner" uk-cover>';
		}
		$html .= '</div>';

		$visibility = 'd-none d-md-block';
		if (!theme_lambda2_get_setting("slideshow_hide_captions")) {
			$visibility = 'd-block';
		}
		
		if (theme_lambda2_get_setting($current_caption)) {
			$html .= '<div class="slide-content '.$current_caption_pos.'">';
			$html .= '<div uk-scrollspy="cls: '.$current_caption_anim.'; delay: 750; repeat: false" class="'.$visibility.' uk-overlay '.$current_caption_overlay.'">';
			$html .= '<div>'.format_text(theme_lambda2_get_setting($current_caption), FORMAT_HTML, ['noclean' => true]).'</div>';
			$html .= '</div></div>';
		}

		$html .= '</li>';
		
		return $html;
	}

    public function lambda2_login_bg_vid() {
		global $PAGE;
		$theme = $PAGE->theme;

		$content = '';
        if (!empty($theme->settings->login_bg_vid)) {
            $vid_url = $theme->setting_file_url('login_bg_vid', 'login_bg_vid');
			$content .= '<video playsinline autoplay muted loop id="background-vid">';
			$content .= '<source src="'.$vid_url.'" type="video/mp4">';
			$content .= '</video>';
        }
        return $content;
    }
	
    public function lambda2_login_bg_slider() {
		global $PAGE;
		$theme = $PAGE->theme;

		$has_img_1 = $theme->settings->login_bg_img_1;
		$has_img_2 = $theme->settings->login_bg_img_2;
		$has_img_3 = $theme->settings->login_bg_img_3;
		$has_img_4 = $theme->settings->login_bg_img_4;
		$time = $theme->settings->login_bg_slider_time;

		$content = '';
        if ($has_img_1 || $has_img_2 || $has_img_3 || $has_img_4) {
            $content = '<div id="login_bg_slider" uk-slideshow="animation: fade;autoplay: true; autoplay-interval: '.$time.'" style="position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%;" class="uk-visible-toggle-custom" tabindex="-1">';
			$content .= '<ul class="uk-slideshow-items" style="height: 100vh;">';
			if ($has_img_1) {
				$content .= '<li><img src="'.$theme->setting_file_url('login_bg_img_1', 'login_bg_img_1').'" alt="" uk-cover></li>';
			}
			if ($has_img_2) {
				$content .= '<li><img src="'.$theme->setting_file_url('login_bg_img_2', 'login_bg_img_2').'" alt="" uk-cover></li>';
			}
			if ($has_img_3) {
				$content .= '<li><img src="'.$theme->setting_file_url('login_bg_img_3', 'login_bg_img_3').'" alt="" uk-cover></li>';
			}
			if ($has_img_4) {
				$content .= '<li><img src="'.$theme->setting_file_url('login_bg_img_4', 'login_bg_img_4').'" alt="" uk-cover></li>';
			}
			$content .= '</ul></div>';
        }
        return $content;
    }
	
    public function favicon() {
        if (theme_lambda2_get_moodle_version() == 'm-40') {
			if (!empty($this->page->theme->settings->favicon)) {return $this->page->theme->setting_file_url('favicon', 'favicon');}
        } else if (empty(get_config('core_admin', 'favicon'))) {
			if (!empty($this->page->theme->settings->favicon)) {return $this->page->theme->setting_file_url('favicon', 'favicon');}
		}
        return parent::favicon();
    }

    public function webfonts() {
		$webfonts = '';
		if (theme_lambda2_get_setting('fonts_source') == 1) {
			$font_languages = theme_lambda2_get_setting('font_languages');
			if ($font_languages != '') {$font_languages = '&subset='.$font_languages;}
			$bodyfont = theme_lambda2_get_setting('font_body');
			$headingfont = theme_lambda2_get_setting('font_heading');
			$bodyweight = 400;
			$headingweight = 400;
		
			switch ($headingfont) {
			case "Open+Sans":
				$headingweight = 700;
				break;
			case "Arvo":
				$headingweight = 700;
				break;
			case "Cabin":
				$headingweight = 700;
				break;
			case "Crimson+Pro":
				$headingweight = 700;
				break;
			case "Encode+Sans":
				$headingweight = 700;
				break;
			case "Enriqueta":
				$headingweight = 700;
				break;
			case "Gudea":
				$headingweight = 700;
				break;
			case "Josefin+Sans":
				$headingweight = 700;
				break;
			case "Lato":
				$headingweight = 700;
				break;
			case "Lekton":
				$headingweight = 700;
				break;
			case "Nunito":
				$headingweight = 700;
				break;
			case "Montserrat":
				$headingweight = 700;
				break;
			case "PT+Sans":
				$headingweight = 700;
				break;
			case "Raleway":
				$headingweight = 500;
				break;
			case "Roboto":
				$headingweight = 500;
				break;
			case "Rubik":
				$headingweight = 600;
				break;
			case "Solway":
				$headingweight = 700;
				break;
			case "Ubuntu":
				$headingweight = 700;
				break;
			case "Vollkorn":
				$headingweight = 700;
				break;
			case "Work+Sans":
				$headingweight = 700;
				break;
			}
		
			$webfonts = '';
			$googlefontslink = '';
			if ($bodyfont != 'Open+Sans') {
				$googlefontslink .= 'family='.$bodyfont.':wght@'.$bodyweight;
			}
			if ($headingfont != 'Open+Sans') {
				if (($bodyfont == $headingfont) && ($headingweight != 400)) {
					$googlefontslink .= ';'.$headingweight;
				} else {
					if ($bodyfont != 'Open+Sans') {
						$googlefontslink .= '&';
					}
					$googlefontslink .= 'family='.$headingfont.':wght@'.$headingweight;
				}
			}
			if ($googlefontslink != '') {
				$webfonts = '<link rel="preconnect" href="https://fonts.googleapis.com">';
				$webfonts .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
				$webfonts .= '<link href="https://fonts.googleapis.com/css2?'.$googlefontslink.'&display=swap" rel="stylesheet">';
			}
		}
		if (theme_lambda2_get_setting('use_linearicons')) {
			$webfonts .= '<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">';
		}
		return $webfonts;
	}

	public function theme_lambda2_get_user_fullname() {
		global $USER;
		return isloggedin() && !isguestuser() ? trim(get_string('fullnamedisplay', null, $USER)) : get_string('guestuser');
	}

	public function theme_lambda2_course_custom_fields() {
		$content = '';
		$customcoursefields = theme_lambda2_get_course_custom_fields();
		if($customcoursefields != '') {
			$content = '<div class="block card fake-block block_lambda_course_custom_fields mb-3">';
			$content .= '<div class="card-body p-3">';
			$content .= '<h5 class="card-title d-inline">'.get_string('coursedetails').'</h5>';
			$content .= '<div class="card-text content mt-3">';
			$content .= $customcoursefields;
			$content .= '<div class="footer"></div></div></div></div>';

		}
		return $content;
	}

	function theme_lambda2_course_categories_block() {
		$content = '';
		$courserenderer = $this->page->get_renderer('core', 'course');
		$content .= '<div class="block card fake-block block_lambda_course_categories">';
		$content .= '<div class="card-body p-3">';
		$content .= '<h5 class="card-title d-inline">'.get_string('categories').'</h5>';
		$content .= '<div class="card-text content mt-3">';
		$content .= $courserenderer->theme_lambda2_categories_list().'</div></div>';
		$content .= '<div class="footer"></div></div>';

		return $content;
	}

	function theme_lambda2_search_courses() {
		global $CFG;
		$content = '<div class="simplesearchform px-4">
		<form autocomplete="off" action="'.$CFG->wwwroot.'/course/search.php" method="get" accept-charset="utf-8" class="mform simplesearchform pt-3">
		<div class="input-group">
			<label for="searchinput-aside">
				<span class="sr-only">Search courses</span>
			</label>
			<input type="text" id="searchinput-aside" class="form-control" placeholder="Search courses" aria-label="Search courses" name="search" data-region="input" autocomplete="off" value="">
			<div class="input-group-append">
				<button type="submit" class="btn  btn-primary search-icon">
					<i class="icon fa fa-search fa-fw " aria-hidden="true"></i>
					<span class="sr-only">Search courses</span>
				</button>
			</div>
		</div>
		</form>
		</div>';
		return $content;
	}

	function theme_lambda2_enrol_course_sections() {
		$content = '';
		if (theme_lambda2_get_setting('enrolment_courseoverview')) {
					$content .= '<div class="details-section sections mt-5">
						<h4>'.get_string('courseoverview').'</h4>
						<div class="divider line-02"></div>
						<div class="coursedetails">
							<div class="list">'.theme_lambda2_get_enrol_course_sections().'</div>
						</div>
					</div>';
		}
		return $content;
	}

	function theme_lambda2_dashboard_info_stats() {
		global $DB, $PAGE, $USER;
		$content = '';
		$currentUrl = $_SERVER['REQUEST_URI'];

		if ((strpos($currentUrl, "/my/courses.php") === false) && theme_lambda2_get_setting('dashboard_info_stats')) {
			$iconcourses = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16"><path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"></path></svg>';
				
			if (theme_lambda2_check_adminrole()) {
				$allcourses = $DB->count_records('course', []);
				$allcourses -= 1;

				$allusers = $DB->count_records('user', ['deleted' => 0]);
				$allusers -= 2;

				$allcategories = $DB->count_records('course_categories', []);

				$usersonline = new fetcher(null, time(), 300, $PAGE->context, $PAGE->context->contextlevel, $PAGE->course->id);
				$countusersonline = $usersonline->count_users();

				$diskspace = @disk_free_space('.');
				$diskspace = $this->formatSize($diskspace);

				$iconusers = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"></path></svg>';
				$icondisk = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-hdd-network" viewBox="0 0 16 16"><path d="M4.5 5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1M3 4.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"></path><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8.5v3a1.5 1.5 0 0 1 1.5 1.5h5.5a.5.5 0 0 1 0 1H10A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5H.5a.5.5 0 0 1 0-1H6A1.5 1.5 0 0 1 7.5 10V7H2a2 2 0 0 1-2-2zm1 0v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1m6 7.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5"></path></svg>';
				$iconcategories = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z"/></svg>';

				$content .= '<div class="container-fluid">';
				$content .= '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 py-4">';
				$content .= $this->theme_lambda2_dashboard_infocard($allcourses, get_string('courses'), "#037979", $iconcourses, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($allcategories, get_string('categories'), "#037979", $iconcategories, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($allusers.'</span><span style="font-size: 1.275rem;"> / '.$countusersonline, get_string('users')." / ".strtolower(get_string('loggedin', 'message')), "#037979", $iconusers, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($diskspace, "Disk space", "#037979", $icondisk, "");

				$content .= '</div></div>';
			} else {
				$usercourses = 0;
				$userccompleted = 0;
				$useractcompleted = 0;
				$useractdue = 0;

				$userallcourses = enrol_get_users_courses($USER->id);
				foreach ($userallcourses as $key => $thiscourse) {
					$usercourses++;
					$completion = new \completion_info($thiscourse);
					$courseprogress = new \core_completion\progress();
					$courseprogress = $courseprogress->get_course_progress_percentage($thiscourse, $USER->id);
					if ($completion->is_enabled()) {
						$courseactivities = $completion->get_activities();
						foreach ($courseactivities as $thisactivity) {
							if ($completion->get_data($thisactivity, false, $USER->id)->completionstate == COMPLETION_INCOMPLETE) {
								$useractdue++;
							} else {
								$useractcompleted++;
							}
						}
						if ($courseprogress == "100") {
							$userccompleted++;
						}
					}
				}

				$iconccompleted = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-mortarboard" viewBox="0 0 16 16"><path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917zM8 8.46 1.758 5.965 8 3.052l6.242 2.913z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466zm-.068 1.873.22-.748 3.496 1.311a.5.5 0 0 0 .352 0l3.496-1.311.22.748L8 12.46z"/></svg>';
				$iconactcompleted = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/></svg>';
				$iconactdue = '<svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" fill="currentColor" class="bi bi-calendar2-x" viewBox="0 0 16 16"><path d="M6.146 8.146a.5.5 0 0 1 .708 0L8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 0 1 0-.708"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/></svg>';

				$content .= '<div class="container-fluid">';
				$content .= '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 py-4">';
				$content .= $this->theme_lambda2_dashboard_infocard($usercourses, get_string('courses'), "#037979", $iconcourses, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($userccompleted, get_string('courses')." ".strtolower(get_string('completed')), "#037979", $iconccompleted, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($useractcompleted, get_string('activities')." ".strtolower(get_string('completed')), "#037979", $iconactcompleted, "border-right");
				$content .= $this->theme_lambda2_dashboard_infocard($useractdue, get_string('indicator:activitiesdue'), "#037979", $iconactdue, "");

				$content .= '</div></div>';
			}
		}
	
		return $content;
	}

	function theme_lambda2_dashboard_infocard($stringtop, $stringbottom, $color, $icon, $border) {
		$infocard = '';
		$infocard .= '<div class="col '.$border.'">
		<div class="card card-body">
			<div class="d-inline-flex align-items-center">
				<div style="margin-right: 1rem;">
					<div class="px-2" style="color: #037979;">'.$icon.'</div>
				</div>
				<div>
					<span style="font-size: 2rem;font-weight: 700;">'.$stringtop.'</span>
					<p class="lead mb-0" style="opacity: .85;">'.$stringbottom.'</p>
				</div>
			</div>
		</div>
        </div>';

		return $infocard;
	}

	public function theme_lambda2_get_enrol_blocks(): string {
        global $COURSE, $PAGE, $DB, $CFG;
		$output = '';
		if (theme_lambda2_get_moodle_version() == 'm-45') {
			require_once($CFG->libdir.'/blocklib.php');

			if (strpos($PAGE->pagetype, 'enrol-') !== 0) {
				return '<p>no enrol</p>';
			}

			$context = \context_course::instance($COURSE->id);
			$instances = $DB->get_records('block_instances', [
				'blockname' => 'lambdacb',
				'parentcontextid' => $context->id
			]);

			foreach ($instances as $instance) {
				$block = block_instance('lambdacb', $instance);
				if ($block) {
					$c = $block->get_content();
					if (!empty($c->text)) {
						$output .= '<div class="block_lambdacb block no-header custom card mb-3">'.$c->text.'</div>';
					}
				}
			}
		}

        return $output;
    }

	function formatSize($bytes) {
		$units = ['MB', 'GB', 'TB'];
		$size = $bytes / 1024 / 1024;
		foreach ($units as $unit) {
			if ($size < 1024) {
				return round($size, 2) . " " . $unit;
			}
			$size /= 1024;
		}
		$size = $bytes / 1024 / 1024 / 1024 / 1024;
		return round($size, 2) . " TB";
	}
}