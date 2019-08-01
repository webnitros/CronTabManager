<?php

/**
 * Remove an Items
 */
class CronTabManagerTaskLogRemoveProcessor extends modObjectProcessor {
	public $objectType = 'CronTabManagerTaskLog';
	public $classKey = 'CronTabManagerTaskLog';
	public $languageTopics = array('crontabmanager:manager');
	public $permission = 'crontabmanager_remove';

	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}
		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
			return $this->failure($this->modx->lexicon('crontabmanager_task_log_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var CronTabManagerTaskLog $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('crontabmanager_task_log_err_nf'));
			}
			$object->remove();
		}
		return $this->success();
	}

}

return 'CronTabManagerTaskLogRemoveProcessor';