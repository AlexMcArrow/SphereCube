<?php

namespace SphereCube;

use InvalidArgumentException;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * PostgresDb Class
 **/
class DBInstance
{
    /**
     * PDO connection
     *
     * @var PDO
     */
    protected $connection;
    /**
     * The SQL query to be prepared and executed
     *
     * @var string
     */
    protected $query;
    /**
     * The previously executed SQL query
     *
     * @var string
     */
    protected $lastQuery;
    /**
     * Dynamic array that holds a combination of where condition/table data value types and parameter references
     *
     * @var array|null
     */
    protected $bindParams;
    /**
     * Variable which holds last statement error
     *
     * @var string
     */
    protected $stmtError;
    /**
     * Allows the use of the tableNameToClassName method
     *
     * @var bool
     */
    protected $autoClassEnabled = true;
    /**
     * Name of table we're performing the action on
     *
     * @var string|null
     */
    protected $tableName;
    /**
     * Type of fetch to perform
     *
     * @var int
     */
    protected $fetchType = PDO::FETCH_ASSOC;
    /**
     * Fetch argument
     *
     * @var string
     */
    protected $fetchArg;
    /**
     * Error mode for the connection
     * Defaults to
     *
     * @var int
     */
    protected $errorMode = PDO::ERRMODE_WARNING;
    /**
     * List of keywords used for escaping column names, automatically populated on connection
     *
     * @var string[]
     */
    protected $sqlKeywords = [];
    /**
     * List of columns to be returned after insert/delete
     *
     * @var string[]|null
     */
    protected $returning;

    /**
     * List of columns to be returned after insert/delete
     *
     * @var array
     */
    protected $resultset = [];


    /**
     * Variable which holds an amount of returned rows during queries
     *
     * @var int
     */
    public $count = 0;


    /**
     * Used for connecting to the database
     *
     * @var string
     */
    private $connectionString;

    /**
     * Used for define Db scheme
     *
     * @var string
     */
    private $connectionScheme;

    const ORDERBY_RAND = 'rand()';

    /**
     * PostgresDb constructor
     *
     * @param string $db
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param int    $port
     * @param string $scheme
     */
    public function __construct($db = '', $host = '', $user = '', $pass = '', $port = 5432, $scheme = 'public')
    {
        $this->connectionString = <<<CONNSTR
        pgsql:host=$host port=$port user=$user password=$pass dbname=$db options='--client_encoding=UTF8'
        CONNSTR;
        $this->connectionScheme = $scheme;
    }

    /**
     * Initiate a database connection using the data passed in the constructor
     *
     * @throws PDOException
     * @throws RuntimeException
     */
    protected function connect()
    {
        $this->setConnection(new PDO($this->connectionString));
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, $this->errorMode);
        $this->connection->exec('SET search_path TO ' . $this->connectionScheme);
    }

    /**
     * @return PDO
     * @throws RuntimeException
     * @throws PDOException
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * Allows passing any PDO object to the class, e.g. one initiated by a different library
     *
     * @param PDO $PDO
     *
     * @throws PDOException
     * @throws RuntimeException
     */
    public function setConnection(PDO $PDO)
    {
        $this->connection = $PDO;
        // $keywords = $this->query('SELECT word FROM pg_get_keywords();');
        // foreach ($keywords as $key) {
        //     $this->sqlKeywords[strtolower($key['word'])] = true;
        // }
    }

    /**
     * Set the error mode of the PDO instance
     * Expects a PDO::ERRMODE_* constant
     *
     * @param int
     *
     * @return self
     */
    public function setPDOErrmode($errmode)
    {
        $this->errorMode = $errmode;
        if ($this->connection) {
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, $this->errorMode);
        }

        return $this;
    }

    /**
     * Returns the error mode of the PDO instance
     *
     * @return int
     */
    public function getPDOErrmode()
    {
        return $this->errorMode;
    }

    /**
     * Method attempts to prepare the SQL query
     * and throws an error if there was a problem.
     *
     * @return PDOStatement
     * @throws RuntimeException
     */
    protected function prepareQuery()
    {
        try {
            $stmt = $this->getConnection()->prepare($this->query);
        } catch (PDOException $e) {
            throw new RuntimeException(
                "Problem preparing query ($this->query): " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        if (is_bool($stmt)) {
            throw new RuntimeException("Problem preparing query ($this->query). Check logs/stderr for any warnings.");
        }

        return $stmt;
    }

    /**
     * Function to replace query placeholders with bound variables
     *
     * @param string $query
     * @param array  $bindParams
     *
     * @return string
     */
    public static function replacePlaceHolders($query, $bindParams)
    {
        $namedParams = [];
        foreach ($bindParams as $key => $value) {
            if (!is_int($key)) {
                unset($bindParams[$key]);
                $namedParams[ltrim($key, ':')] = $value;
                continue;
            }
        }
        ksort($bindParams);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $query = preg_replace_callback(
            '/:([a-z]+)/',
            function ($matches) use ($namedParams) {
                return array_key_exists(
                    $matches[1],
                    $namedParams
                ) ? self::bindValue($namedParams[$matches[1]]) : $matches[1];
            },
            $query
        );

        foreach ($bindParams as $param) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $query = preg_replace('/\?/', self::bindValue($param), $query, 1);
        }

        return $query;
    }

    /**
     * Convert a bound value to a readable string
     *
     * @param mixed $val
     *
     * @return string
     */
    private static function bindValue($val)
    {
        switch (gettype($val)) {
            case 'NULL':
                $val = 'NULL';
                break;
            case 'string':
                $val = "'" . preg_replace('/(^|[^\'])\'/', "''", $val) . "'";
                break;
            case 'boolean':
                $val = $val ? 'true' : 'false';
                break;
            default:
                $val = (string)$val;
        }

        return $val;
    }

    /**
     * Helper function to add variables into bind parameters array
     *
     * @param mixed $value Variable value
     * @param string|null $key Variable key
     */
    protected function bindParam($value, $key = null)
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if ($key === null || is_numeric($key)) {
            $this->bindParams[] = $value;
        } else {
            $this->bindParams[$key] = $value;
        }
    }

    /**
     * Helper function to add variables into bind parameters array in bulk
     *
     * @param array $values Variable with values
     * @param bool $ignoreKey Whether array keys should be ignored when binding
     */
    protected function bindParams($values, $ignoreKey = false)
    {
        foreach ($values as $key => $value) {
            $this->bindParam($value, $ignoreKey ? null : $key);
        }
    }

    /**
     * Helper function to add variables into bind parameters array and will return
     * its SQL part of the query according to operator in ' $operator ?'
     *
     * @param string $operator
     * @param        $value
     *
     * @return string
     */
    protected function buildPair($operator, $value)
    {
        $this->bindParam($value);

        return " $operator ? ";
    }

    protected static function escapeApostrophe($str)
    {
        return preg_replace('~(^|[^\'])\'~', '$1\'\'', $str);
    }

    /**
     * Abstraction method that will build the RETURNING clause
     *
     * @param string|string[]|null $returning What column(s) to return
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function buildReturning($returning)
    {
        if ($returning === null) {
            return;
        }

        if (!is_array($returning)) {
            $returning = array_map('trim', explode(',', $returning));
        }
        $this->returning = $returning;
        $columns = [];
        foreach ($returning as $column) {
            $columns[] = $this->quoteColumnName($column, true);
        }
        $this->query .= ' RETURNING ' . implode(', ', $columns);
    }

    /**
     * @param mixed[] $tableData
     * @param string[] $tableColumns
     * @param bool $isInsert
     *
     * @throws RuntimeException
     */
    public function buildDataPairs($tableData, $tableColumns, $isInsert)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];
            if (!$isInsert) {
                $this->query .= "\"$column\" = ";
            }

            // Simple value
            if (!is_array($value)) {
                $this->bindParam($value);
                $this->query .= '?, ';
                continue;
            }

            if ($isInsert) {
                throw new RuntimeException("Array passed as insert value for column $column");
            }

            $this->query .= '';
            $in = [];
            foreach ($value as $k => $v) {
                if (is_int($k)) {
                    $this->bindParam($value);
                    $in[] = '?';
                } else {
                    $this->bindParams[$k] = $value;
                    $in[] = ":$k";
                }
            }
            $this->query = 'IN (' . implode(', ', $in) . ')';
        }
        $this->query = rtrim($this->query, ', ');
    }

    /**
     * Abstraction method that will build an INSERT or UPDATE part of the query
     *
     * @param array $tableData
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function buildInsertQuery($tableData)
    {
        if (!is_array($tableData)) {
            return;
        }

        $isInsert = stripos($this->query, 'INSERT') === 0;
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            $this->query .= ' (' . implode(', ', $this->quoteColumnNames($dataColumns)) . ') VALUES (';
        } else {
            $this->query .= ' SET ';
        }

        $this->buildDataPairs($tableData, $dataColumns, $isInsert);

        if ($isInsert) {
            $this->query .= ')';
        }
    }

    /**
     * Execute raw SQL query.
     *
     * @param string $query User-provided query to execute.
     * @param array $bindParams Variables array to bind to the SQL statement.
     *
     * @return DBInstance
     * @throws RuntimeException
     * @throws PDOException
     */
    public function query($query, $bindParams = null)
    {
        $this->query = $query;
        $this->alterQuery();

        $stmt = $this->prepareQuery();
        if (empty($bindParams)) {
            $this->bindParams = null;
        } elseif (!is_array($bindParams)) {
            throw new RuntimeException('$bindParams must be an array');
        } else {
            $this->bindParams($bindParams);
        }

        $this->resultset = $this->execStatement($stmt) ?: [];
        return $this;
    }

    /**
     * Sets a class to be used as the PDO::fetchAll argument
     *
     * @param string $class
     * @param int $type
     *
     * @return self
     */
    public function setClass($class, $type = PDO::FETCH_CLASS)
    {
        $this->fetchType = $type;
        $this->fetchArg = $class;

        return $this;
    }

    /**
     * Disabled the tableNameToClassName method
     *
     * @return self
     */
    public function disableAutoClass()
    {
        $this->autoClassEnabled = false;

        return $this;
    }

    /**
     * @param PDOStatement $stmt Statement to execute
     * @param boolean $reset Whether the object should be reset (must be done manually if set to false)
     *
     * @return array|false
     * @throws PDOException
     */
    protected function execStatement($stmt, $reset = true)
    {
        $this->lastQuery = $this->bindParams !== null
            ? self::replacePlaceHolders($this->query, $this->bindParams)
            : $this->query;

        try {
            $success = $stmt->execute($this->bindParams);
        } catch (PDOException $e) {
            $this->stmtError = $e->getMessage();
            $this->reset();
            throw $e;
        }

        if ($success !== true) {
            $this->count = 0;
            $errInfo = $stmt->errorInfo();
            $this->stmtError = "PDO Error #{$errInfo[1]}: {$errInfo[2]}";
            $result = false;
        } else {
            $this->count = $stmt->rowCount();
            $this->stmtError = null;
            $result = $this->fetchArg !== null
                ? $stmt->fetchAll($this->fetchType, $this->fetchArg)
                : $stmt->fetchAll($this->fetchType);
        }

        if ($reset) {
            $this->reset();
        }

        return $result;
    }

    public function reset()
    {
        $this->autoClassEnabled = true;
        $this->tableName = null;
        $this->fetchType = PDO::FETCH_ASSOC;
        $this->fetchArg = null;
        $this->bindParams = [];
        $this->query = null;
        $this->returning = null;
    }

    /**
     * Returns a boolean value if no data needs to be returned, otherwise returns the requested data
     *
     * @param mixed $res Result of an executed statement
     * @return bool|mixed
     */
    protected function returnWithReturning($res)
    {
        if ($res === false || $this->count < 1) {
            return false;
        }

        if ($this->returning !== null) {
            if (!is_array($res)) {
                return false;
            }

            // If we got a single column to return then just return it
            if (count($this->returning) === 1) {
                return array_values($res[0])[0];
            }

            // If we got multiple, return the entire array
            return $res[0];
        }

        return true;
    }

    /**
     * Adds quotes around table name for use in queries
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function quoteTableName($tableName)
    {
        return preg_replace('~^"?([a-zA-Z\d_\-]+)"?(?:\s*(\s[a-zA-Z\d]+))?$~', '"$1"$2', trim($tableName));
    }

    /**
     * Adds quotes around column name for use in queries
     *
     * @param string $columnName
     * @param bool $allowAs Controls whether "column as alias" can be used
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function quoteColumnName($columnName, $allowAs = false)
    {
        $columnAlias = '';
        $columnName = trim($columnName);
        $hasAs = preg_match('~\S\s+AS\s+\S~i', $columnName);
        if ($allowAs && $hasAs) {
            $values = $this->quoteColumnNames(preg_split('~\s+AS\s+~i', $columnName));
            $columnName = $values[0];
            $columnAlias = " AS $values[1]";
        } elseif (!$allowAs && $hasAs) {
            throw new InvalidArgumentException(
                __METHOD__ . ": Column name ($columnName) contains disallowed AS keyword"
            );
        }

        // JSON(B) access
        if (strpos($columnName, '->>') !== false && preg_match(
            '~^"?([a-z_\-\d]+)"?->>\'?([\w\-]+)\'?"?$~',
            $columnName,
            $match
        )) {
            $col = "\"$match[1]\"";
            return $col . (!empty($match[2]) ? "->>'" . self::escapeApostrophe($match[2]) . "'" : '') . $columnAlias;
        }
        // Let's not mess with TOO complex column names (containing || or ')
        if (strpos($columnName, '||') !== false || preg_match('~\'(?<!\\\\\')~', $columnName)) {
            return $columnName . $columnAlias;
        }

        if (strpos($columnName, '.') !== false && preg_match($dotTest = '~\.(?<!\\\\\.)~', $columnName)) {
            $split = preg_split($dotTest, $columnName);
            if (count($split) > 2) {
                throw new RuntimeException("Column $columnName contains more than one table separation dot");
            }

            return $this->quoteTableName($split[0]) . '.' . $this->quoteColumnName($split[1]) . $columnAlias;
        }
        $functionCallOrAsterisk = preg_match('~(^\w+\(|^\s*\*\s*$)~', $columnName);
        $validColumnName = preg_match('~^(?=[a-z_])([a-z\d_]+)$~', $columnName);
        $isSqlKeyword = isset($this->sqlKeywords[strtolower($columnName)]);
        if (!$functionCallOrAsterisk && (!$validColumnName || $isSqlKeyword)) {
            return '"' . trim($columnName, '"') . '"' . $columnAlias;
        }

        return $columnName . $columnAlias;
    }

    /**
     * Adds quotes around column name for use in queries
     *
     * @param string[] $columnNames
     * @param bool $allowAs
     *
     * @return string[]
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function quoteColumnNames($columnNames, $allowAs = false)
    {
        foreach ($columnNames as $i => $columnName) {
            $columnNames[$i] = $this->quoteColumnName($columnName, $allowAs);
        }

        return $columnNames;
    }

    /**
     * Replaces some custom shortcuts to make the query valid
     */
    protected function alterQuery()
    {
        $this->query = preg_replace('~(\s+)&&(\s+)~', '$1AND$2', $this->query);
    }

    /**
     * Method returns last executed query
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    /**
     * Return last error message
     *
     * @return string
     */
    public function getLastError()
    {
        if (!$this->connection) {
            return 'No connection has been made yet';
        }

        return trim($this->stmtError);
    }

    /**
     * Returns the class name expected for the table name
     * This is a utility function for use in case you want to make your own
     * automatic table<->class bindings using a wrapper class
     *
     * @param bool $hasNamespace
     *
     * @return string|null
     */
    public function tableNameToClassName($hasNamespace = false)
    {
        $className = $this->tableName;

        if (is_string($className)) {
            $className = preg_replace(
                '/s(_|$)/',
                '$1',
                preg_replace('/ies([-_]|$)/', 'y$1', preg_replace_callback('/(?:^|-)([a-z])/', function ($match) {
                    return strtoupper($match[1]);
                }, $className))
            );
            $append = $hasNamespace ? '\\' : '';
            $className = preg_replace_callback('/__?([a-z])/', function ($match) use ($append) {
                return $append . strtoupper($match[1]);
            }, $className);
        }

        return $className;
    }

    public function fetchAll(string|bool $baseField = false, string|bool $subField = false): array
    {
        if ($baseField === true) {
            return $this->resultset[0];
        }
        if (is_string($baseField)) {
            $out = [];
            foreach ($this->resultset as $value) {
                if (is_string($subField)) {
                    foreach ($value as $svalue) {
                        if (!array_key_exists($baseField, $out)) {
                            $out[$value[$baseField]] = [];
                        }
                        $out[$value[$baseField]][$svalue[$subField]] = $value;
                    }
                } else {
                    $out[$value[$baseField]] = $value;
                }
            }
            return $out;
        }
        return $this->resultset;
    }

    /**
     * Close connection
     */
    public function __destruct()
    {
        if ($this->connection) {
            $this->connection = null;
        }
    }
}
