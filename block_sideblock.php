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
 * Strings for component 'block_html', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_sideblock
 * @copyright 2021 Husakova Kvetuse
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class block_sideblock extends block_base 
{
    public function init() 
    {
        $this->title = get_string('pluginname', 'block_sideblock');
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.
    public function has_config()
    {
        return true;
    }
    public function get_content() 
    {
        global $DB, $USER, $COURSE;

        if ($this->content !== null) 
        {
          return $this->content;
        }
        /*
            --------------------------------------------------------------------
            1. Zajistit funkčnost bloku - hotovo
            2. Co se tam má vypsat  a. Kdy byl kurz dokončen
                                    b. Echo že nebyl kurz dokončen
            ---------------------------------------------------------------------
            1. Připojení k databázi
            2. čtení z databáze
            3. načtení toho co chci z databáze 
                        -> chci time stamp
                        -> chci jej zobrazit v čitelné podobě pro oko
                        -> chci jej zobrazit pouze tam kde je kurz hotový
            4. výpis do side bloku v moodle
            ---------------------------------------------------------------------
        */  
        $userscomplete = $DB->get_record('course_completions', ['userid' => $USER->id, 'course' => $COURSE->id] );
        $content = get_string('infocourse', 'block_sideblock');

        if($userscomplete->timecompleted != null)
        {                        
            $content .= get_string('done','block_sideblock',date("d. m Y h:m:s",$userscomplete->timecompleted));
        }
        else
        {
            $content .= get_string('undone','block_sideblock');
        }

        $this->content         =  new stdClass;
        $footerContent = get_string('author','block_sideblock');
        $footerContent .=get_string('authorname','block_sideblock');
        $this->content->text = $content;//$textik;
        $this->content->footer = $footerContent;
     
        return $this->content;
    }
    public function instance_allow_multiple()
    {
        return true;
    }
    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() 
    {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();
        $pluginconfigs = (object) ['allowcssclasses' => $CFG->block_sideblock_allowcssclasses];

        return (object) [
            'instance' => $instanceconfigs,
            'plugin' => $pluginconfigs,
        ];
    }
    public function instance_config_save($data,$nolongerused =false) 
    {
        if(get_config('sideblock', 'Allow_HTML') == '1') 
        {
          $data->text = strip_tags($data->text);
        }
       
        // And now forward to the default implementation defined in the parent class
        return parent::instance_config_save($data,$nolongerused);
    }
    /*public function hide_header() 
    {
        return true;
    }*/
    public function html_attributes() {
        $attributes = parent::html_attributes(); // Get default values
        $attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
        return $attributes;
    }
    public function applicable_formats() 
    {
        return array(
                        'course-view' => true, 
                        'course-view-social' => false,
                        'site-index' => true,
                        'mod' => true, 
                        'mod-quiz' => false
                    );
    }
}