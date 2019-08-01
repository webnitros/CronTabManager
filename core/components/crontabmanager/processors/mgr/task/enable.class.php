<?php
require_once(dirname(__FILE__) . '/update.class.php');
/**
 * Enable an Task
 */
class CronTabManagerTaskEnableProcessor extends CronTabManagerTaskUpdateProcessor
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

return 'CronTabManagerTaskEnableProcessor';
