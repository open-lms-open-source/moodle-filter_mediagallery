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
 * Media gallery embed filter
 *
 * @package    filter_mediagallery
 * @copyright  2014 NetSpot Pty Ltd {@link http://netspot.com.au}
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_mediagallery extends moodle_text_filter {

    private $renderer;


    public function filter($text, array $options = array()) {
        return preg_replace_callback('#<a.*class="filter_mediagallery".*><img.*alt="(\d+)"\s.*></a>#i',
                                    'filter_mediagallery::replace',
                                    $text);
    }

    private function replace(array $matches) {
        global $CFG, $PAGE;

        require_once($CFG->dirroot.'/mod/mediagallery/locallib.php');

        if (!$this->renderer) {
            $this->renderer = $PAGE->get_renderer('mod_mediagallery');
        }

        try {
            $gallery = new \mod_mediagallery\gallery($matches[1]);
        } catch (dml_missing_record_exception $e) {
            $string = '';
            if (has_capability('moodle/course:manageactivities', $PAGE->context)) {
                $string = get_string('gallerymissing', 'filter_mediagallery');
            }
            return $string;
        }

        $ret = $this->renderer->view_carousel($gallery, array('filter' => true));

        return $ret;
    }
}
