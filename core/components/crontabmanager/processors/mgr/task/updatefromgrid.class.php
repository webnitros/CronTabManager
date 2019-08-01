<?php
require_once (dirname(__FILE__).'/update.class.php');
class CronTabManagerTaskUpdateFromGridProcessor extends CronTabManagerTaskUpdateProcessor {
    public $classKey = 'CronTabManagerTask';
    public $objectType = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:manager');

    public function initialize() {
        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $properties = $this->modx->fromJSON($data);
        $this->setProperties($properties);
        $this->unsetProperty('data');
        return parent::initialize();
    }
}

return 'CronTabManagerTaskUpdateFromGridProcessor';
