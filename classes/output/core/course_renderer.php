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

namespace theme_lambda2\output\core;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use html_writer;
use core_course_category;
use coursecat_helper;
use stdClass;
use core_course_list_element;

class course_renderer extends \core_course_renderer {

	protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG, $DB;
		$catlayout = theme_lambda2_get_setting('category_layout');
		$catlayoutclass = 'list';
        $content = '';
        $coursecat = '';
        
		if ($catlayout == '1') {$catlayoutclass = 'grid';}
        if ($catlayout == '2') {$catlayoutclass = 'grid min';}
        if ($catlayout == '3') {$catlayoutclass = 'list min';}
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        $catid = $course->category;
        if (!empty($catid)) {
            $category = $DB->get_record('course_categories', ['id' => $catid]);
            $coursecat = $category->name;
        }/*
		//Moodle internal
        if ($category->visible == 0 AND !(has_capability('moodle/category:viewhiddencategories', \context_system::instance()))) {
            return '';
        }*/

        $classes = trim('coursebox '.$catlayoutclass.' clearfix '.$additionalclasses);
        $nametag = 'h3';

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));

        $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .info

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->theme_lambda2_coursecat_coursebox_content($chelper, $course, $coursecat);
        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }
	
	protected function theme_lambda2_coursecat_coursebox_content($chelper, $course, $coursecat) {
        global $CFG;

        $catlayout = theme_lambda2_get_setting('category_layout');
        $check_enrolment_page = theme_lambda2_check_enrolment();
        
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $coursenameclasses = 'coursename';
        $cp = theme_lambda2_get_courseprice($course->id);
        $cp_badge = '';
        if ($cp->hascourseprice) {
			$cp_badge = '<span class="cp badge-price">'.$cp->courseprice->currency.' '.number_format($cp->courseprice->cost, 2, '.', '').'</span>';
            $coursenameclasses .= ' text-left';
		} else if (!empty(theme_lambda2_get_setting('course_free_txt')) or theme_lambda2_get_setting('course_free_txt') != "") {
            $cp_badge = '<span class="cp badge-price free">'.format_string(theme_lambda2_get_setting('course_free_txt')).'</span>';
            $coursenameclasses .= ' text-left';
		}

        $coursecatdiv = '<div class="text-muted coursecat"><span class="sr-only">';
        $coursecatdiv .= get_string('coursecategory');
        $coursecatdiv .= '</span><span class="categoryname text-truncate">'.$coursecat.'</span></div>';

        $content = '';
		
		// display course overview files
        $contentimage = '';
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
        $contentimage .= '<a href="'.new moodle_url('/course/view.php', array('id' => $course->id)).'">';
        $contentimage .= '<div class="courseimage" data-src="'.$imgurl.'" uk-img></div>';
        $contentimage .= '</a>';
        if ($catlayout != 3) {
            $content .= $contentimage;
        }

        // display course summary
            if ($catlayout == 1 || $catlayout == 2) {
                $content .= $cp_badge;
            }
            $content .= html_writer::start_tag('div', array('class' => $course->visible ? 'summary' : 'summary dimmed'));
            if ($catlayout == 0 || $catlayout == 3) {
                $content .= $cp_badge;
            }
			$coursename = $chelper->get_course_formatted_name($course);
        	$coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
            $content .= html_writer::tag('h3', $coursenamelink, array('class' => $coursenameclasses));
            $content .= $coursecatdiv;

            if ($catlayout == 0 || $check_enrolment_page > 0) {
                $content .= '<div>'.$chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false)).'</div>';
            }
            else if ($catlayout == 1 ||  $catlayout == 3) {
                $summary = $course->summary;
                $rep = array("</p>", "<br>", "</div>");
                $summary = str_replace($rep, " ", $summary);
                $summary = format_string($summary);
                if (strlen($summary) > 200) {
                    $summary = substr($summary, 0, 200);
                    $summary .= '...';  
                }
                $content .= '<div><p>'.$summary.'</p></div>';
            }
			
            $content .= html_writer::end_tag('div'); // .summary

        // display course contacts. See course_in_list::get_course_contacts()
        if ($course->has_course_contacts()) {
            if ($catlayout == 0 || $check_enrolment_page > 0) {
                $content .= '<div class="teachers">';
                $current_role = '';
                $i = 0;
                $list_course_contacts = $course->get_course_contacts();
                
                foreach ($list_course_contacts as $userid => $coursecontact) {
                    if ($i == 0) {
                        $current_role = $coursecontact['rolename'];
                        $content .= $current_role.': ';
                        $name = html_writer::link(new moodle_url('/user/view.php', array('id' => $userid, 'course' => SITEID)), $coursecontact['username']);
                        $content .= $name;
                        }
                    if (($i > 0) AND ($coursecontact['rolename'] == $current_role)) {
                        $content .= ', ';
                        $name = html_writer::link(new moodle_url('/user/view.php', array('id' => $userid, 'course' => SITEID)), $coursecontact['username']);
                        $content .= $name;
                    }
                    else if ($i > 0) {
                        $content .= '</div>';
                        $content .= '<div class="teachers">';
                        $current_role = $coursecontact['rolename'];
                        $content .= $current_role.': ';
                        $name = html_writer::link(new moodle_url('/user/view.php', array('id' => $userid, 'course' => SITEID)), $coursecontact['username']);
                        $content .= $name;
                    }
                    $i++;
                }
                $content .= '</div>'; // .teachers
            }
        }

        // Display custom fields.
        if ($course->has_custom_fields()) {
            if (($catlayout == 0 && !theme_lambda2_get_setting('enrolment_coursecustomfields')) || (theme_lambda2_check_enrolment() && theme_lambda2_get_setting('course_enrolment_page') == 1)) {
                $content .= '<div class="custom_fields">';
                $handler = \core_course\customfield\course_handler::create();
                $customfields = $handler->display_custom_fields_data($course->get_custom_fields());
                $content .= \html_writer::tag('div', $customfields, ['class' => 'customfields-container']);
                $content .= '</div>';
            }
        }
		
        if ($catlayout == 0) {
		    $content .= '<div class="course-btn"><p><a class="btn btn-primary" href="'.new moodle_url('/course/view.php', array('id' => $course->id)).'">'.get_string('entercourse').'</a></p></div>';
        }

        return $content;
    }

    protected function theme_lambda2_get_courses() {
        global $CFG, $OUTPUT;
    
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(255)->set_courses_display_options([
            'recursive' => true
        ]);
        $chelper->set_attributes(['class' => 'frontpage-course-list-all']);
        $perpage = (int)$CFG->coursesperpage;
    
        $url = $_SERVER['REQUEST_URI'];
        $catid = 0;
        $page = 1;
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParameters);
            if (isset($queryParameters['categoryid'])) {
                $catid = intval($queryParameters['categoryid']);
            }
            if (isset($queryParameters['page'])) {
                $page = intval($queryParameters['page']);
            }
        }
    
        $header = '<div class="card-deck me-0">';
        $content = '';
        $footer = '</div>';
        $pagination = '';

        try {
            //$coursecat = core_course_category::get($catid);
            $coursecat = core_course_category::get($catid, IGNORE_MISSING, true);
            $courses = $coursecat->get_courses($chelper->get_courses_display_options());
        } catch (Exception $e) {
            $courses = [];
        }

        $numcourses = count($courses);
        if($numcourses > $perpage) {
            $pages = ceil($numcourses / $perpage);
            $paginurl = $CFG->wwwroot.'/course/index.php?categoryid='.$catid.'&amp;browse=courses&amp;perpage='.$perpage.'&amp;page=';
            $pagination .= '<nav aria-label="Page" class="pagination pagination-centered justify-content-center"><ul class="mt-1 pagination " data-page-size="'.$perpage.'">';
            if ($page != 1) {
                $prev = $page - 1;
                $pagination .= '<li class="page-item" data-page-number="'.$prev.'">
                <a href="'.$paginurl.$prev.'" class="page-link" aria-label="Previous page">
                    <span aria-hidden="true"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                    <span class="sr-only">Previous page</span>
                </a>
                </li>';
            }
            for ($i = 1; $i <= $pages; $i++) {
                $active = '';
                $paginurl = $CFG->wwwroot.'/course/index.php?categoryid='.$catid.'&amp;browse=courses&amp;perpage='.$perpage.'&amp;page=';
                if ($page > 1 && $page == $i) {
                    $active = 'active';
                }
                else if ($page == 1 && $i == 1) {
                    $active = 'active';
                }
                if ($active == 'active') {
                    $paginurl = '#';
                }
                $current = '';
                if ($active != 'active') {
                    $current = '<span class="sr-only">(current)</span>';
                }
                $pagination .= '<li class="page-item '.$active.'" data-page-number="'.$i.'">';
                $pagination .= '<a href="'.$paginurl.$i.'" class="page-link">'.$i.$current.'</a></li>';
            }
            if ($page <  $pages) {
                $next = $page + 1;
                $pagination .= '<li class="page-item" data-page-number="'.$next.'">
                <a href="'.$paginurl.$next.'" class="page-link" aria-label="Next page">
                    <span aria-hidden="true"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                    <span class="sr-only">Next page</span>
                </a>
                </li>';
            }
            $pagination .= '</ul></nav>';
            $start = (int)(($page - 1) * $perpage);
            $courses = array_slice($courses, $start, $perpage, true);
        }

        foreach ($courses as $course) {
            $content .= $this->coursecat_coursebox($chelper, $course);
        }

        $output = $header . $content . $footer . $pagination;
        return $output; 
    }

    public function theme_lambda2_categories_list() {
        global $CFG;

        $coursecat = core_course_category::top();
        if (!$coursecat->get_children_count()) {
            return '';
        }
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth(1)->set_show_courses(self::COURSECAT_SHOW_COURSES_COUNT)->set_categories_display_options(array('limit' => 0, 'viewmoreurl' => new moodle_url('/course/index.php', array('browse' => 'categories', 'page' => 1))))->set_attributes(array('class' => 'frontpage-category-names'));
        $this->categoryexpandedonload = false;
        $categorycontent = parent::coursecat_subcategories($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div', $attributes);
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));
        $content .= html_writer::end_tag('div');
        return $content;
    }

    protected function coursecat_category_content(coursecat_helper $chelper, $coursecat, $depth) {
        if (theme_lambda2_is_course_index()) {
            if (theme_lambda2_get_setting('category_style') == '0') {
                return $this->theme_lambda2_get_courses();
            }
        }
        return parent::coursecat_category_content($chelper, $coursecat, $depth);
    }

    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        if (theme_lambda2_get_setting('category_style') == '2' || theme_lambda2_get_setting('category_style') == '3') {
                if (theme_lambda2_is_course_index()) {
                global $DB;
                $pre = '';
                $post = '';
                $catid = 0;
                $url = $_SERVER['REQUEST_URI'];
                $parts = parse_url($url);
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $queryParameters);
                    if (isset($queryParameters['categoryid'])) {
                        $catid = $queryParameters['categoryid'];
                    }
                }
                $coursescount = theme_lambda2_get_courses_count($catid);
                $sql = "SELECT COUNT(cc.id) AS total, cc.parent FROM {course_categories} cc WHERE cc.parent = $catid AND cc.visible = 1 GROUP BY cc.parent";
                $subcatscount = $DB->get_field_sql($sql);
                if ($coursescount > 0) {
                    $post = '<h4 class="heading-courses mb-3">'.get_string('courses').'</h4>';
                }
                if ($subcatscount > 0) {
                    $pre = '<h4 class="heading-courses mb-3">'.get_string('categories').'</h4>';
                }
                return $pre.parent::coursecat_subcategories($chelper, $coursecat, $depth).$post;
            }
        }
        return parent::coursecat_subcategories($chelper, $coursecat, $depth);
    }

    protected function theme_lambda2_get_category_image($coursecat) {
        $chelper = new coursecat_helper();
        $catimage = '';

        if ($catdesc = $chelper->get_category_formatted_description($coursecat)) {
            $dom = new \DOMDocument();
            $dom->loadHTML($catdesc);
            $images = $dom->getElementsByTagName('img');
            if ($images->length > 0) {
                $catimage = $images->item(0)->getAttribute('src');
            }
        }
        if ($catimage == '') {
            global $CFG;
            $children_courses = $coursecat->get_courses();
            foreach($children_courses as $child_course) {
                if ($child_course === reset($children_courses)) {
                        foreach ($child_course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        $url = moodle_url::make_pluginfile_url(
                            $file->get_contextid(),
                            $file->get_component(),
                            $file->get_filearea(),
                            $file->get_filepath(),
                            $file->get_filename(),
                            !$isimage
                        );
                        if ($isimage) {
                            $catimage = $url;
                        }
                    }
                }
            }
            if ($catimage == '') {
                global $OUTPUT;
                $random = mt_rand(0, 10);
                $catimage = $OUTPUT->get_generated_image_for_id($random);
            }
        }
        return $catimage;
    }

    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        if (theme_lambda2_get_setting('category_style') == '2' || theme_lambda2_get_setting('category_style') == '3') {
            $content = '';
            $catname = $coursecat->get_formatted_name();
            $countcourses = count($coursecat->get_courses());
            $countsubcats = $coursecat->get_children_count();
            $coursestxt = '';
            $subcategoriestxt = '';
            $items_count = '';

            if ($countsubcats > 0) {
                $subcategoriestxt .= $countsubcats;
                if ($countsubcats > 1) {
                    $subcategoriestxt .= ' '.get_string('categories');
                } else {
                    $subcategoriestxt .= ' '.get_string('category');
                }
            }
            if ($countcourses > 0) {
                $coursestxt .= $countcourses;
                if ($countcourses > 1) {
                    $coursestxt .= ' '.get_string('courses');
                } else {
                    $coursestxt .= ' '.get_string('course');
                }
            }
            $items_count .=  $subcategoriestxt;
            if ($subcategoriestxt !== '' && $coursestxt !== '') {
                $items_count .= ' - ';
            }
            $items_count .=  $coursestxt;
            
            if (theme_lambda2_get_setting('category_style') == '2') {
                $caticon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="category-icon bi bi-folder" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z"/></svg>';
                $content .= '<a class="category-card col-xl-4 col-lg-6 col-md-6 col-12" href="' . new moodle_url('/course/index.php', ['categoryid' => $coursecat->id]) . '"><div class="single-category"><div class="caticon-wrapper me-3 mb-2">'.$caticon.'</div><div class="catdesc-wrapper"><h5 class="mb-0">'.$coursecat->name.'</h5><p class="mb-0 text-muted">'.$items_count.'</p></div></div></a>'.PHP_EOL;
            } else {
                $content .= '<div class="single-category coursebox grid min clearfix" data-courseid="4" data-type="1"><div class="content"><a href="' . new moodle_url('/course/index.php', ['categoryid' => $coursecat->id]) . '"><div class="courseimage" data-src="'.$this->theme_lambda2_get_category_image($coursecat).'" uk-img></div></a><div class="summary"><h3 class="coursename"><a class="" href="' . new moodle_url('/course/index.php', ['categoryid' => $coursecat->id]) . '">'.$catname.'</a></h3><div class="text-muted coursecat info"><span class="text-truncate">'.$items_count.'</span></div></div></div></div>'.PHP_EOL;
            }
            return $content;
        }
        return parent::coursecat_category($chelper, $coursecat, $depth);    
    }

}
