<?php

namespace Pematon\Session;

use Nette;

/**
 * Storing session to database.
 * Inspired by: https://github.com/JedenWeb/SessionStorage/
 */
class MysqlSessionHandler extends Nette\Object implements \SessionHandlerInterface
{
	private $tableName;

	/** @var Nette\Database\Context */
	private $context;

	private $lockId;

	public function __construct(Nette\Database\Context $context)
	{
		$this->context = $context;
	}

	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}

	/**
	 * Returns hash in binary(16) format.
	 */
	protected function hash($id)
	{
		return md5($id, true);
	}

	private function lock() {
		if ($this->lockId === null) {
			$this->lockId = md5(session_id());
			while (!$this->context->query("SELECT GET_LOCK(?, 1) as `lock`", $this->lockId)->fetch()->lock);
		}
	}

	private function unlock() {
		if ($this->lockId === null) {
			return;
		}

		$this->context->query("SELECT RELEASE_LOCK(?)", $this->lockId);
		$this->lockId = null;
	}

	public function open($savePath, $name)
	{
		$this->lock();

		return TRUE;
	}

	public function close()
	{
		$this->unlock();

		return TRUE;
	}

	public function destroy($sessionId)
	{
		$hashedSessionId = $this->hash($sessionId);

		$this->context->table($this->tableName)->where('id', $hashedSessionId)->delete();

		$this->unlock();

		return TRUE;
	}

	public function read($sessionId)
	{
		$this->lock();

		$hashedSessionId = $this->hash($sessionId);

		$row = $this->context->table($this->tableName)->get($hashedSessionId);

		if ($row) {
			return $row->data;
		}

		return '';
	}

	public function write($sessionId, $sessionData)
	{
		$this->lock();

		$hashedSessionId = $this->hash($sessionId);
		$time = time();

		if ($row = $this->context->table($this->tableName)->get($hashedSessionId)) {
			if ($row->data !== $sessionData) {
				$row->update(array(
					'timestamp' => $time,
					'data' => $sessionData,
				));
			} else if ($time - $row->timestamp > 300) {
				// Optimization: When data has not been changed, only update
				// the timestamp after 5 minutes.
				$row->update(array(
					'timestamp' => $time,
				));
			}
		} else {
			$this->context->table($this->tableName)->insert(array(
				'id' => $hashedSessionId,
				'timestamp' => $time,
				'data' => $sessionData,
			));
		}

		return TRUE;
	}

	public function gc($maxLifeTime)
	{
		$this->context->table($this->tableName)
			->where('timestamp < ?', (time() - $maxLifeTime))
			->delete();

		return TRUE;
	}
}
