<?php

class DBErrorException extends Exception {
}

class DB {
    public static $query_count = 0;

    protected static $connection;

    protected static $query;

    protected static $query_closed = TRUE;

    protected static $show_errors = TRUE;

    private static $_instance = null;

    public static function affectedRows() {
        return self::$query->affected_rows;
    }

    public static function close() {
        return self::$connection->close();
    }

    public static function connect( $dbhost = 'localhost', $dbport = 3306, $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8' ) {
        self::$connection = new mysqli( $dbhost, $dbuser, $dbpass, $dbname, $dbport );
        if ( self::$connection->connect_error ) {
            self::error( 'Failed to connect to MySQL - ' . self::$connection->connect_error );
        }
        self::$connection->set_charset( $charset );
    }

    public static function fetchAll( $callback = null ) {
        $params = array();
        $row    = array();
        $meta   = self::$query->result_metadata();
        while ( $field = $meta->fetch_field() ) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array( array( self::$query, 'bind_result' ), $params );
        $result = array();
        while ( self::$query->fetch() ) {
            $r = array();
            foreach ( $row as $key => $val ) {
                $r[$key] = $val;
            }
            if ( $callback != null && is_callable( $callback ) ) {
                $value = call_user_func( $callback, $r );
                if ( $value == 'break' ) {
                    break;
                }
            } else {
                $result[] = $r;
            }
        }
        self::$query->close();
        self::$query_closed = TRUE;
        return $result;
    }

    public static function fetchArray() {
        $params = array();
        $row    = array();
        $meta   = self::$query->result_metadata();
        while ( $field = $meta->fetch_field() ) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array( array( self::$query, 'bind_result' ), $params );
        $result = array();
        while ( self::$query->fetch() ) {
            foreach ( $row as $key => $val ) {
                $result[$key] = $val;
            }
        }
        self::$query->close();
        self::$query_closed = TRUE;
        return $result;
    }

    public static function getInstance() {
        if ( self::$_instance === null ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public static function lastInsertID() {
        return self::$connection->insert_id;
    }

    public static function numRows() {
        self::$query->store_result();
        return self::$query->num_rows;
    }

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

    public static function show_errors( $flag = true ) {
        self::$show_errors = $flag;
    }

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

    private static function error( $error ) {
        if ( self::$show_errors ) {
            throw new DBErrorException( $error );
        }
    }
}
