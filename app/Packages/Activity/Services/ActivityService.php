<?php

use Base\Services\Service;

/**
 *
 * Activity Logs Service for Webby.
 *
 * @package        Activity Logs
 * @version        1.0.0
 * @license        MIT
 */
class ActivityService extends Service
{

	private $fields = [
		'id'         => 'id',
		'type'       => 'type',
		'type_id'    => 'type_id',
		'action'      => 'action',
		'comment'    => 'comment',
		'created_at' => 'created_at',
		'created_by' => 'created_by',
	];

	private $logId        = 0; // LogId to Retrive
	private $type         = false; // Type String
	private $typeId       = false; // Type ID
	private $action        = false; // Action
	private $comment      = ''; // Comment adding
	private $createdBy    = ''; // User ID
	private $fromDate; // From Date
	private $toDate; // To Date

	/**
	 * Intilize Codeigniter
	 */
	public function __construct() 
	{
		$this->use->model('Activity/ActivityModel');
	}

	/**
	 * Set UserID
	 * @param int $userid
	 * @return $this
	 */
	public function user($userid) 
	{
		$this->createdBy = $userid;
		return $this;
	}

	/**
	 * Set TypeID
	 * @param string $type
	 * @return $this
	 */
	public function type($type) 
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * Set  TypeID
	 * @param int $id
	 * @return $this
	 */
	public function id($typeId) 
	{
		$this->typeId = $typeId;
		return $this;
	}

	/**
	 * Set  LogId
	 * @param int $logId
	 * @return $this
	 */
	public function logId($logId) 
	{
		$this->logId = $logId;
		return $this;
	}

	/**
	 * Set Action
	 * @param String $action
	 * @return $this
	 */
	public function action($action) 
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * Set Comment
	 * @param string $comment
	 * @return $this
	 */
	public function comment($comment) 
	{
		$this->comment = $comment;
		return $this;
	}

	/**
	 * 
	 * @param type $from
	 * @param type $to
	 * @return $this
	 */
	public function dateRange($from, $to) 
	{
		$this->fromDate = $from;
		$this->toDate   = $to;
		return $this;
	}

	/**
	 * Add Log, as Database Entry
	 * @param void
	 * @return void
	 */
	public function log() 
	{
		$data = [
			$this->fields['type']        => $this->type,
			$this->fields['type_id']     => $this->typeId,
			$this->fields['action']      => $this->action,
			$this->fields['comment']     => $this->comment,
			$this->fields['created_at']  => datetime(),
			$this->fields['created_by']  => $this->createdBy,
		];

		$this->ActivityModel->save($data);
		$this->logId = $this->ActivityModel->lastInsertId();
		$this->flushParameter();
	}

	/**
	 * Get last Log
	 * @return array
	 */
	public function lastLog() 
	{

		if (empty($this->logId)) {
			return $this->ActivityModel->selectLast('*');
		}

		return $this->ActivityModel->where('id', $this->logId)->selectLast('*');
	}

	protected function getQueryMaker() 
	{
		if ($this->createdBy) {
			$this->ActivityModel->where($this->fields['created_by'], $this->createdBy);
		}

		if ($this->type) {
			$this->ActivityModel->where($this->fields['type'], $this->type);
		}

		if ($this->typeId) {
			$this->ActivityModel->where($this->fields['type_id'], $this->typeId);
		}

		if ($this->action) {
			$this->ActivityModel->where($this->fields['action'], $this->action);
		}

		if ($this->logId) {
			$this->ActivityModel->where($this->fields['id'], $this->logId);
		}

		if ($this->fromDate) {
			$this->ActivityModel->where("{$this->fields['created_at']} >", $this->fromDate);
		}

		if ($this->toDate) {
			$this->ActivityModel->where("{$this->fields['created_at']} <=", $this->toDate);
		}

	}

	public function getTotal() 
	{
		$this->getQueryMaker();

		return $this->ActivityModel->countAll();
	}

	/**
	 * Get all activities
	 *
	 * @return mixed
	 */
	public function get() 
	{
		$this->getQueryMaker();
		$result = $this->ActivityModel;
		return $this->dbCleanResult($result);
	}

	/**
	 * List all activities
	 *
	 * @return mixed
	 */
	public function list() 
	{
		$this->getQueryMaker();
		$result = $this->ActivityModel;
		return $this->dbCleanResult($result);
	}

	public function removeLog($id = null)
	{
		$this->getQueryMaker();
		return $this->ActivityModel->delete('id', $id);
	}

	public function removeAll()
	{
		$this->getQueryMaker();
		$table = $this->ActivityModel->table();
		return $this->ActivityModel->truncate($table);
	}

	public function getIds() 
	{
		$this->getQueryMaker();
		$ids = $this->ActivityModel->select('type_id')->asArray()->get();
		return array_column($ids, 'type_id');
	}

	protected function dbCleanResult($result) 
	{

		if ($result->countAll() > 1) {
			return $result->all();
		}

		if ($result->countAll() == 1) {
			return $result->first();
		} else {
			return false;
		}

	}

	/**
	 * Reset Parameter
	 */
	public function flushParameter()
	{
		$this->comment = '';
		$this->action   = 0;
		$this->type    = 0;
		$this->typeId  = 0;
	}

}
