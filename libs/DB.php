<?php

class DBErrorException extends Exception {
}

class DB {
    /**
     * @var int
     */
    public static $query_count = 0;

    /**
     * @var mixed
     */
    protected static $connection;

    /**
     * @var mixed
     */
    protected static $query;

    /**
     * @var bool
     */
    protected static $query_closed = TRUE;

    /**
     * @var bool
     */
    protected static $show_errors = TRUE;

    /**
     * @var mixed
     */
    private static $_instance = null;

    /**
     * DB class for MySQL (mysqli-driver)
     * @return void
     */
    function __construct() {}
    /**
     * Return affected rows
     * @return int
     */
    public static function affectedRows() {
        return self::$query->affected_rows;
    }

    /**
     * Closing cconnection
     * @return bool
     */
    public static function close() {
        return self::$connection->close();
    }

    /**
     * Make connetion
     * @param  string $dbhost
     * @param  int    $dbport
     * @param  string $dbuser
     * @param  string $dbpass
     * @param  string $dbname
     * @param  string $charset
     * @return void
     */
    public static function connect( $dbhost = 'localhost', $dbport = 3306, $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8' ) {
        self::$connection = new mysqli( $dbhost, $dbuser, $dbpass, $dbname, $dbport );
        if ( self::$connection->connect_error ) {
            self::error( 'Failed to connect to MySQL - ' . self::$connection->connect_error );
        }
        self::$connection->set_charset( $charset );
    }

    /**
     * Fetching and restructure result
     * @param  string|bool $id_field    primary field name, 'true' for return single row, 'false' for always multy-row
     * @param  string|bool $id_subfield secondary field name, 'true' for return single row, 'false' for always multy-row
     * @return array
     */
    public static function fetchAll( $id_field = false, $id_subfield = false ) {
        $params = array();
        $row    = array();
        $meta   = self::$query->result_metadata();
        while ( $field = $meta->fetch_field() ) {
            $row[$field->name] = null;
            $params[]          = &$row[$field->name];
        }
        call_user_func_array( array( self::$query, 'bind_result' ), $params );
        $result = array();
        while ( self::$query->fetch() ) {
            $r = array();
            foreach ( $row as $key => $val ) {
                $r[$key] = $val;
            }
            if ( $id_field && $id_field !== true ) {
                if ( $id_subfield && $id_subfield !== true ) {
                    if ( !key_exists( (string) $r[$id_field], $result ) ) {
                        $result[(string) $r[$id_field]] = [];
                    }
                    $result[(string) $r[$id_field]][(string) $r[$id_subfield]] = $r;
                } else {
                    $result[(string) $r[$id_field]] = $r;
                }
            } else {
                if ( $id_field === false ) {
                    $result[] = $r;
                } else {
                    $result = $r;
                }
            }
        }
        self::$query->close();
        self::$query_closed = TRUE;
        return $result;
    }

    /**
     * Return instance for chain style
     * @return DB
     */
    public static function getInstance() {
        if ( self::$_instance === null ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Return last insert ID
     * @return string|int|bool
     */
    public static function lastInsertID() {
        return self::$connection->insert_id;
    }

    /**
     * Return number of result rows
     * @return int
     */
    public static function numRows() {
        self::$query->store_result();
        return self::$query->num_rows;
    }

    /**
     * Run SQL-query
     * @param  string $query    SQL-query with or without params-placeholder
     * @param  mixed  $args,... many params for query or array of params
     * @return DB
     */
    public static function query( $query ) {
        if ( !self::$query_closed ) {
            self::$query->close();
        }
        if ( self::$query = self::$connection->prepare( $query ) ) {
            if ( func_num_args() > 1 ) {
                $x        = func_get_args();
                $args     = array_slice( $x, 1 );
                $types    = '';
                $args_ref = array();
                foreach ( $args as $k => &$arg ) {
                    if ( is_array( $args[$k] ) ) {
                        foreach ( $args[$k] as $j => &$a ) {
                            $types .= self::_gettype( $args[$k][$j] );
                            $args_ref[] = &$a;
                        }
                    } else {
                        $types .= self::_gettype( $args[$k] );
                        $args_ref[] = &$arg;
                    }
                }
                array_unshift( $args_ref, $types );
                call_user_func_array( array( self::$query, 'bind_param' ), $args_ref );
            }
            self::$query->execute();
            if ( self::$query->errno ) {
                self::error( 'Unable to process MySQL query (check your params) - ' . self::$query->error );
            }
            self::$query_closed = FALSE;
            self::$query_count++;
        } else {
            self::error( 'Unable to prepare MySQL statement (check your syntax) - ' . self::$connection->error );
        }
        return self::$_instance;
    }

    /**
     * Setting showing error flag
     * @param  bool   $flag
     * @return void
     */
    public static function show_errors( $flag = true ) {
        self::$show_errors = $flag;
    }

    /**
     * Check variable type
     * @param  mixed    $var
     * @return string
     */
    private static function _gettype( $var ) {
        if ( is_string( $var ) ) {
            return 's';
        }

        if ( is_float( $var ) ) {
            return 'd';
        }

        if ( is_int( $var ) ) {
            return 'i';
        }

        return 'b';
    }

    /**
     * Throwing error
     * @param  string             $error
     * @throws DBErrorException
     * @return void
     */
    private static function error( $error ) {
        if ( self::$show_errors ) {
            throw new DBErrorException( $error );
        }
    }
}
