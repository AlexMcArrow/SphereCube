<?php

if ( php_sapi_name() != 'cli' ) {
    die( 'CLI only' );
}

require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Minimal extends CLI {
    function __construct() {
        $this->bin = __FILE__;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function main( Options $options ) {
        switch ( $options->getCmd() ) {
            case 'cacheclear':
                array_map( function ( string $cache ) {
                    unlink( $cache );
                }, glob( buildpath( COREPATH, 'cache' . DIRECTORY_SEPARATOR . '*.php' ) ) );
                $this->success( 'Cache cleared' );
                break;
            case 'index:drop':
                $this->man_drop();
                $this->success( 'Index droped' );
                break;
            case 'index:create':
                $this->man_drop();
                $this->man_create();
                $this->success( 'Index createted' );
                break;
            case 'index:rebuild':
                $this->man_rebuild();
                $this->success( 'Index data rebuilded' );
                break;
            default:
                echo $options->help();
                break;
        }
    }

    /**
     * @return void
     */
    protected function man_create() {
        Manticore::getInstance()->getIndex( 'card' )->create( [
            'name'   => ['type' => 'text'],
            'guid'   => ['type' => 'string'],
            'active' => ['type' => 'integer']
        ], [
            'rt_mem_limit'  => '256M',
            'min_infix_len' => '1'
        ] );
        Manticore::getInstance()->getIndex( 'cardfield' )->create( [
            'name' => ['type' => 'text'],
            'guid' => ['type' => 'string'],
            'type' => ['type' => 'string']
        ], [
            'rt_mem_limit'  => '256M',
            'min_infix_len' => '1'
        ] );
        Manticore::getInstance()->getIndex( 'cardfieldvalue' )->create( [
            'value'  => ['type' => 'text'],
            'guid'   => ['type' => 'string'],
            'fguid'  => ['type' => 'string'],
            'active' => ['type' => 'integer']
        ], [
            'rt_mem_limit'  => '256M',
            'min_infix_len' => '1'
        ] );
    }

    /**
     * @return void
     */
    protected function man_drop() {
        Manticore::getInstance()->getIndex( 'card' )->drop( true );
        Manticore::getInstance()->getIndex( 'cardfield' )->drop( true );
        Manticore::getInstance()->getIndex( 'cardfieldvalue' )->drop( true );
    }

    /**
     * @return void
     */
    protected function man_rebuild() {
        $this->man_truncate();

        $cardIndex = Manticore::getInstance()->getIndex( 'card' );
        $cardCount = 0;
        foreach ( DB::getInstance()
            ->query( "SELECT
                            c.`card_id` AS `guid`,
                            c.`card_name` AS `name`,
                            c.`active` AS `active`
                        FROM
                            card AS c;" )
            ->fetchAll( 'guid' ) as $value ) {
            $cardIndex->addDocument( $value );
            $cardCount++;
        }
        $this->success( 'Card rebuild [' . $cardCount . ']' );

        $cardIndex = Manticore::getInstance()->getIndex( 'cardfield' );
        $cardCount = 0;
        foreach ( DB::getInstance()
            ->query( "SELECT
                            cf.`cardfield_id` AS `guid`,
                            cf.`cardfield_name` AS `name`,
                            cf.`cardfield_type` AS `type`
                        FROM
                        cardfield AS cf;" )
            ->fetchAll( 'guid' ) as $value ) {
            $cardIndex->addDocument( $value );
            $cardCount++;
        }
        $this->success( 'CardField rebuild [' . $cardCount . ']' );

        $cardIndex = Manticore::getInstance()->getIndex( 'cardfieldvalue' );
        $cardCount = 0;
        foreach ( DB::getInstance()
            ->query( "SELECT
                            cfv.`cardfieldvalue_id` AS `guid`,
                            cfv.`cardfield_id` AS `fguid`,
                            cfv.`value` AS `value`,
                            cfv.`active` AS `active`
                        FROM
                        cardfieldvalue AS cfv;" )
            ->fetchAll( 'guid' ) as $value ) {
            $cardIndex->addDocument( $value );
            $cardCount++;
        }
        $this->success( 'CardFieldValue rebuild [' . $cardCount . ']' );
    }

    /**
     * @return void
     */
    protected function man_truncate() {
        Manticore::getInstance()->getIndex( 'card' )->truncate();
        $this->success( 'Card truncate' );
        Manticore::getInstance()->getIndex( 'cardfield' )->truncate();
        $this->success( 'CardField truncate' );
        Manticore::getInstance()->getIndex( 'cardfieldvalue' )->truncate();
        $this->success( 'CardFieldValue truncate' );
    }

    /**
     * @return void
     */
    protected function setup( Options $options ) {
        $options->setHelp( 'SphereCube CLI' );
        $options->registerCommand( 'cacheclear', 'Clear cache' );
        $options->registerCommand( 'index:drop', 'index drop structure' );
        $options->registerCommand( 'index:create', 'index create structure' );
        $options->registerCommand( 'index:rebuild', 'index rebuild data' );
    }
}
// execute it
$cli = new Minimal();
$cli->run();
