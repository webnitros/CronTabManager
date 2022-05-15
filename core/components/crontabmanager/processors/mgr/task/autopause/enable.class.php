<?php
require_once(dirname(__FILE__) . '/update.class.php');
/**
 * Enable an Task
 */
class CronTabManagerAutoPauseEnableProcessor extends CronTabManagerAutoPauseUpdateProcessor
{

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = array(
            'active' => true,
        );
        return true;
    }

}

return 'CronTabManagerAutoPauseEnableProcessor';
