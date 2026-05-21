<?php
defined('MOODLE_INTERNAL') || die;

$functions = [
    'theme_lambda2_userpreference_lightdarkmode' => [
        'classname' => '\theme_lambda2\external\userpreference',
        'classpath' => 'theme/lambda2/classes/external/userpreference.php',
        'methodname' => 'layout',
        'description' => 'Save user preference Layout',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
    ],
];
