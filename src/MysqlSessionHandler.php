<?php

namespace Pematon\Session;

use Nette;
use Nette\Database\Explorer;

/**
 * Storing session to database.
 * Inspired by: https://github.com/JedenWeb/SessionStorage/
 */
class MysqlSessionHandler implements \SessionHandlerInterface
{
    private Explorer $database;

    private string $tableName;
    private ?string $lockId = null;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    protected function hash(string $id): string
    {
        return md5($id);
    }

    private function lock(): void
    {
        if ($this->lockId === null) {
            $this->lockId = md5(session_id());

            /** @noinspection PhpStatementHasEmptyBodyInspection */
            while (!$this->database->query("SELECT GET_LOCK(?, 1) AS `lock`", $this->lockId)->fetch()->lock) ;
        }
    }

    private function unlock(): void
    {
        if ($this->lockId === null) {
            return;
        }

        $this->database->query("SELECT RELEASE_LOCK(?)", $this->lockId);
        $this->lockId = null;
    }

    public function open(string $path, string $name): bool
    {
        $this->lock();

        return true;
    }

    public function close(): bool
    {
        $this->unlock();

        return true;
    }

    public function destroy($id): bool
    {
        $hashedSessionId = $this->hash($id);

        $this->database->table($this->tableName)->where("id", $hashedSessionId)->delete();

        $this->unlock();

        return true;
    }

    public function read($id): string|false
    {
        $this->lock();

        $hashedSessionId = $this->hash($id);

        $row = $this->database->table($this->tableName)->get($hashedSessionId);

        return $row ? (string)($row["data"]) : "";
    }

    public function write(string $id, string $data): bool
    {
        $this->lock();

        $hashedId = $this->hash($id);
        $time = time();

        if ($row = $this->database->table($this->tableName)->get($hashedId)) {
            if ($row["data"] !== $data) {
                $row->update([
                    "timestamp" => $time,
                    "data" => $data,
                ]);
            } else if ($time - $row["timestamp"] > 300) {
                // Optimization: When data has not been changed, only update
                // the timestamp after 5 minutes.
                $row->update([
                    "timestamp" => $time,
                ]);
            }
        } else {
            $this->database->table($this->tableName)->insert([
                "id" => $hashedId,
                "timestamp" => $time,
                "data" => $data,
            ]);
        }

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $maxTimestamp = time() - $max_lifetime;

        // Try to avoid a conflict when running garbage collection simultaneously on two
        // MySQL servers at a very busy site in a master-master replication setup by
        // subtracting one tenth of $maxLifeTime (but at least one day) from $maxTimestamp
        // for each server with reasonably small ID except for the server with ID 1.
        //
        // In a typical master-master replication setup, the server IDs are 1 and 2.
        // There is no subtraction on server 1 and one day (or one tenth of $maxLifeTime)
        // subtraction on server 2.
        $serverId = $this->database->query("SELECT @@server_id AS `server_id`")->fetch()->server_id;
        if ($serverId > 1 && $serverId < 10) {
            $maxTimestamp -= ($serverId - 1) * max(86400, $max_lifetime / 10);
        }

        return $this->database->table($this->tableName)
            ->where("timestamp < ?", (int)$maxTimestamp)
            ->delete();
    }
}
