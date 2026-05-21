<?php

namespace theme_lambda2\external;

defined('MOODLE_INTERNAL') || die;
require_once("{$CFG->libdir}/externallib.php");

class userpreference extends \external_api {

    public static function layout_parameters() {
        return new \external_function_parameters([
            "lightdarkmode" => new \external_value(PARAM_TEXT, "The layout mode"),
        ]);
    }

    public static function layout($lightdarkmode) {
        global $CFG, $USER;

        set_user_preference("lightdarkmode", $lightdarkmode);
        $cache = \cache::make("theme_lambda2", "lightdarkmode_cache");
        $cachekey = "html_attributes-{$USER->id}";
        $cache->set($cachekey, null);

        return ["status" => true];
    }

    public static function layout_returns() {
        return new \external_single_structure([
            "status" => new \external_value(PARAM_BOOL, "the status"),
        ]);
    }
}
