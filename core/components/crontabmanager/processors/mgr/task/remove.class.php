<?php

/**
 * Remove an Task
 */
class CronTabManagerTaskRemoveProcessor extends modObjectRemoveProcessor {
	public $objectType = 'CronTabManagerTask';
	public $classKey = 'CronTabManagerTask';
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

return 'CronTabManagerTaskRemoveProcessor';