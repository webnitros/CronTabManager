<?php

/**
 * Remove an Items
 */
class CronTabManagerCategoryRemoveProcessor extends modObjectProcessor {
	public $objectType = 'CronTabManagerCategory';
	public $classKey = 'CronTabManagerCategory';
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
			return $this->failure($this->modx->lexicon('crontabmanager_category_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var CronTabManagerCategory $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('crontabmanager_category_err_nf'));
			}
			$object->remove();
		}
		return $this->success();
	}

}

return 'CronTabManagerCategoryRemoveProcessor';