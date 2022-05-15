<?php

/**
 * Remove an Task
 */
class CronTabManagerAutoPauseRemoveProcessor extends modObjectRemoveProcessor {
	public $objectType = 'CronTabManagerAutoPause';
	public $classKey = 'CronTabManagerAutoPause';
	public $languageTopics = array('crontabmanager:manager');
	public $permission = 'crontabmanager_remove';

	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}
}

return 'CronTabManagerAutoPauseRemoveProcessor';
