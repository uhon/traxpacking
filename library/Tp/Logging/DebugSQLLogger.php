<?php
/**
 * User: uhon
 * Date: 2012/02/20
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Logging_DebugSQLLogger implements  Doctrine\DBAL\Logging\SQLLogger {

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql The SQL to be executed.
     * @param array $params The SQL parameters.
     * @param array $types The SQL parameter types.
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $sql .= PHP_EOL;

        if ($params) {
            $sql .= var_export($params, true) . PHP_EOL;
        }

        if ($types) {
            $sql .= var_export($types, true) . PHP_EOL;
        }
        Tp_Shortcut::debugMessage(nl2br($sql));

    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
    }
}
