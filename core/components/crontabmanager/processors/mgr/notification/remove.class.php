<?php

/**
 * Remove an Task
 */
class CronTabManagerNotificationRemoveProcessor extends modObjectRemoveProcessor {
	public $objectType = 'CronTabManagerNotification';
	public $classKey = 'CronTabManagerNotification';
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

return 'CronTabManagerNotificationRemoveProcessor';