<?php
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_sideblock'),
            get_string('descconfig', 'block_sideblock')
        ));
 
$settings->add(new admin_setting_configcheckbox(
            'sideblock/Allow_HTML',
            get_string('labelallowhtml', 'block_sideblock'),
            get_string('descallowhtml', 'block_sideblock'),
            '0'
        ));