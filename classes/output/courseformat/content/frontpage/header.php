<?php

namespace format_moointopics\output\courseformat\content\frontpage;

use renderable;


/**
 * Base class to render the course frontpage header.
 *
 * @package   format_moointopics
 * @copyright 2023 ISy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header implements renderable {

    public function export_for_template(\renderer_base $output)
    {

        $headerimageurl = "http://localhost:8888/moodle401/theme/image.php?theme=mooin4&component=theme&image=.%2Fheader_placeholder_desktop";

        $data = (object)[
            'headerimageURL' => $headerimageurl,
        ];

        return $data;
    }
}
