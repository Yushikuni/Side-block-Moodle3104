<?php
defined('MOODLE_INTERNAL') || die();

class block_sideblock extends block_base 
{
    public function init() 
    {
        $this->title = get_string('sideblock', 'block_sideblock');
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
        /*$userstring = "";
        $users = $DB->get_records('user');
        foreach($users as $user)
        {
            $userstring .= $user->firstname.' '.$user->lastname.'<br/>';
        }*/

        //$USER->id;
        //$COURSE->id; //optional_param('course',null,PARAM_INT);//$PAGE->course->id;
                                        //Potřebuju z tabulky completition id kurzu a id uživtele
        $userscomplete = $DB->get_record('course_completions', ['userid' => $USER->id, 'course' => $COURSE->id] );
        //,['userid'=>'2']);//'course');//'userid');    

        //řádek 53 neber v potaz, vše půjde přes get_string() metodu
        $content = "Zobrazení ID uživatele: ".$USER->id.'<br/>'."Zobrazení ID kurzu: ".$COURSE->id.'<br/>'."Kurz: ";

        if($userscomplete->timecompleted != null)
        {
            $content .= "dokončen: ".date("Y/m/d h:m:s", $userscomplete->timecompleted).'<br/>';
            //bude jeden identifikátor
        }
        else
        {
            //bude druhej identifikátor
            $content .= "nesplněn".'<br/>';
        }
        //$content = get_string('Identifikátor řetězce','jméno pluginu', Datum(common));
        /*foreach($userscomplete as $user)
        {
            if($user->timecompleted != null)
            {
                $content .= "Dokončen: ".date("Y/m/d h:m:s", $user->timecompleted).'<br/>';//vrátí už i datum
            }
            else
            {
                $content .= "Nesplněn".'<br/>';
            } 
        }*/
        $this->content         =  new stdClass;
        //$textik = "text";
        /*$dbh = new PDO('mysql:host=localhost;dbname=moodle','root', '');//vyřešit jinak
        $index = 0; 
        
        foreach($dbh->query('SELECT `timecompleted` FROM `mdl_course_completions`WHERE `timecompleted` IS NOT NULL')as $timeStamp)
        {
        //1623232156 > 39600
            /*if(date("Y/m/d",$timeStamp[$index]) != NULL) // date("Y/m/d",'39600')) // 1.1 1970 12:00
            {
                $textik = "\nTime Stamp: ".date("Y/m/d",$timeStamp[$index]);
            }
            else
            {
                $textik = "Nesplněn";
            }
                
            $textik = "\nTime Stamp: ".date("d.m Y",$timeStamp[$index]);//Y/m/d",$timeStamp[$index]);
            //$index++;
        }*/
        
        $this->content->text = $content;//$textik;
        $this->content->footer = 'Author: Květa';
     
        return $this->content;
    }
    /*
    public function specialization() 
    {
        if (isset($this->config)) 
        {
            if (empty($this->config->title)) 
            {                                              //$this->title
                                                        //$this->config->title
                                                        //'defaulttitle'
                $this->title  = $this->title = get_string('Side Block', 'block_sideblock');            
            } 
            else 
            {
                $this->title = $this->config->title;
            }
     
            if (empty($this->config->text)) 
            {
                $this->config->text = get_string('-', 'block_sideblock');
            }    
        }
    }
    */

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