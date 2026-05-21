<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Version information for Lambda2.
 *
 * @package   theme_lambda2
 * @copyright 2025 redPIthemes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026052200;
$plugin->requires  = 2026042000;
$plugin->component = 'theme_lambda2';
$plugin->release   = '2.5.00-moodle52';
$plugin->maturity  = MATURITY_STABLE;
$plugin->dependencies = [
    'theme_boost' => 2026042000,
];
