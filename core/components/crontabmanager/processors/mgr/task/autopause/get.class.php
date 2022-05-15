<?php

/**
 * Get an Task
 */
class CronTabManagerAutoPauseGetProcessor extends modObjectGetProcessor {
	public $objectType = 'CronTabManagerAutoPause';
	public $classKey = 'CronTabManagerAutoPause';
	public $languageTopics = array('crontabmanager:default');
	public $permission = 'crontabmanager_view';

	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return mixed
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		return parent::process();
	}

}

return 'CronTabManagerAutoPauseGetProcessor';
