<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Theme Lambda2 configuration.
 *
 * @package   theme_lambda2
 * @copyright 2025 redPIthemes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/lib_moodle52.php');

$THEME->name = 'lambda2';
$THEME->parents = ['boost'];
$THEME->editor_sheets = ['editor'];
$THEME->scss = function($theme) {
    return theme_lambda2_get_main_scss_content_moodle52($theme);
};

$footerblockspos = isset($THEME->settings->footer_blocks_pos) ? (int)$THEME->settings->footer_blocks_pos : 0;

$blockregions = ['side-pre'];
if ($footerblockspos === 1) {
    $blockregions = ['side-pre', 'footer-middle'];
} else if ($footerblockspos === 2) {
    $blockregions = ['side-pre', 'footer-left', 'footer-right'];
} else if ($footerblockspos === 3) {
    $blockregions = ['side-pre', 'footer-left', 'footer-middle', 'footer-right'];
} else if ($footerblockspos === 4) {
    $blockregions = ['side-pre', 'footer-left', 'footer-middle', 'footer-middle-2', 'footer-right'];
}

$courseblockregions = $blockregions;
$courseblockregions[] = 'main-top';
$courseblockregions[] = 'main-bottom';

$frontpageblockregions = $courseblockregions;
$frontpageblockregions[] = 'admin-only';

$footerscripts = ['scripts'];
if (!empty($THEME->settings->bs4converter)) {
    $footerscripts[] = 'bsconvert';
}

$THEME->layouts = [
    'base' => [
        'file' => 'default.php',
        'regions' => [],
    ],
    'standard' => [
        'file' => 'default.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'course' => [
        'file' => 'course.php',
        'regions' => $courseblockregions,
        'defaultregion' => 'side-pre',
    ],
    'incourse' => [
        'file' => 'incourse.php',
        'regions' => $courseblockregions,
        'defaultregion' => 'side-pre',
    ],
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => $frontpageblockregions,
        'defaultregion' => 'side-pre',
    ],
    'admin' => [
        'file' => 'default.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'coursecategory' => [
        'file' => 'coursecategory.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'mydashboard' => [
        'file' => 'dashboard.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'mycourses' => [
        'file' => 'dashboard.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'mypublic' => [
        'file' => 'default.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],
    'report' => [
        'file' => 'default.php',
        'regions' => $blockregions,
        'defaultregion' => 'side-pre',
    ],
    'popup' => [
        'file' => 'default.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
    'frametop' => [
        'file' => 'default.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    'print' => [
        'file' => 'default.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
];

$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->javascripts = ['theme-uikit'];
$THEME->javascripts_footer = $footerscripts;
$THEME->enable_dock = false;
$THEME->prescsscallback = 'theme_lambda2_get_pre_scss';
$THEME->extrascsscallback = 'theme_lambda2_get_extra_scss';
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
if (defined('BLOCK_ADDBLOCK_POSITION_FLATNAV')) {
    $THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
}
$THEME->requiredblocks = '';
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
