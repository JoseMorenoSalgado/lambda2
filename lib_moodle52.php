<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Moodle 5.2 compatibility helpers for Lambda2.
 *
 * @package   theme_lambda2
 * @copyright 2025 redPIthemes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Builds the main SCSS content with PHP 8.3/8.4-safe defaults.
 *
 * This function intentionally mirrors theme_lambda2_get_main_scss_content(),
 * but it initializes optional font variables before they are passed to
 * theme_lambda2_set_fontface(). This avoids undefined variable warnings during
 * SCSS compilation on strict Moodle 5.2/PHP 8.3+ environments.
 *
 * @param theme_config $theme Theme configuration.
 * @return string SCSS content.
 */
function theme_lambda2_get_main_scss_content_moodle52($theme) {
    global $CFG;

    $scssfile = $CFG->dirroot . '/theme/lambda2/scss/main.scss';
    if (!is_readable($scssfile)) {
        debugging('Lambda2 main.scss not found or not readable: ' . $scssfile, DEBUG_DEVELOPER);
        return '';
    }

    $scss = file_get_contents($scssfile);

    $fontheadingsrc = '';
    $fontbodysrc = '';

    $googlefonts = isset($theme->settings->fonts_source) ? $theme->settings->fonts_source : 1;

    if ($googlefonts == 1) {
        $fontbody = isset($theme->settings->font_body) ? $theme->settings->font_body : 'Roboto';
        $fontbody = str_replace('+', ' ', $fontbody);

        $fontheading = isset($theme->settings->font_heading) ? $theme->settings->font_heading : 'Roboto';
        $headingweight = 700;

        switch ($fontheading) {
            case 'Abril+Fatface':
            case 'Bevan':
            case 'Bree+Serif':
            case 'Cantata+One':
            case 'Imprima':
            case 'Lobster':
            case 'Pacifico':
            case 'Pontano+Sans':
            case 'Sansita+One':
                $headingweight = 400;
                break;
            case 'Raleway':
            case 'Roboto':
                $headingweight = 500;
                break;
        }

        $fontheading = str_replace('+', ' ', $fontheading);
    } else {
        $fontheading = 'custom_heading_font';
        $headingweight = isset($theme->settings->font_headings_weight) ? (int)$theme->settings->font_headings_weight : 1;
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
            default:
                $headingweight = 700;
                break;
        }

        $headingurl = $theme->setting_file_url('fonts_file_headings', 'fonts_file_headings');
        if (!is_null($headingurl)) {
            $fontheadingsrc = 'url(' . $headingurl . ')';
        }

        $fontbody = 'custom_body_font';
        $bodyurl = $theme->setting_file_url('fonts_file_body', 'fonts_file_body');
        if (!is_null($bodyurl)) {
            $fontbodysrc = 'url(' . $bodyurl . ')';
        }
    }

    $scss = theme_lambda2_set_headingfont($scss, $fontheading, $headingweight);
    $scss = theme_lambda2_set_bodyfont($scss, $fontbody);
    $scss = theme_lambda2_set_fontface($scss, $googlefonts, $fontheading, $fontbody, $fontheadingsrc, $fontbodysrc);

    $pagelayout = isset($theme->settings->page_layout) ? $theme->settings->page_layout : 0;

    $pagebackground = $theme->setting_file_url('page_bg_img', 'page_bg_img');
    if (!is_null($pagebackground)) {
        if (!empty($theme->settings->page_bg_repeat) && $theme->settings->page_bg_repeat != 0) {
            $repeat = 'repeat fixed 0 0';
            $size = 'auto';
        } else {
            $repeat = 'no-repeat fixed 0 0';
            $size = 'cover';
        }
        $opacity = isset($theme->settings->page_bg_img_opacity) ? $theme->settings->page_bg_img_opacity : 1;
        $bgcolor = isset($theme->settings->page_bg_color) ? $theme->settings->page_bg_color : '#ffffff';
        $scss = theme_lambda2_set_backgroundimage($scss, $pagelayout, $pagebackground, $repeat, $size, $bgcolor, $opacity);
    }

    $headerbackground = $theme->setting_file_url('header_background', 'header_background');
    if (!is_null($headerbackground)) {
        $repeat = isset($theme->settings->header_bg_repeat) ? $theme->settings->header_bg_repeat : 0;
        $scss = theme_lambda2_set_headerbackgroundimage($scss, $headerbackground, $repeat);
    }

    $preset = !empty($theme->settings->preset) ? $theme->settings->preset : 'default.scss';
    $allowedpresets = ['default.scss', 'plain.scss', 'legacy.scss'];
    if (!in_array($preset, $allowedpresets, true)) {
        $preset = 'default.scss';
    }

    $presetfile = $CFG->dirroot . '/theme/lambda2/scss/preset/' . $preset;
    if (is_readable($presetfile)) {
        $scss .= file_get_contents($presetfile);
    } else {
        debugging('Lambda2 preset SCSS not found or not readable: ' . $presetfile, DEBUG_DEVELOPER);
    }

    return $scss;
}
