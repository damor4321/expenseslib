<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ExpensesLib;

use DomainException;

/**
 * Service factory for the ExpensesLib TableGateway
 *
 * If the "expenseslib" key is present, and either the "db" or "table" subkeys
 * are present and valid, uses those; otherwise, uses defaults of "Db\ExpensesLib"
 * and "expenses", respectively.
 *
 * If the DB service does not exist, raises an error.
 *
 * Otherwise, creates a TableGateway instance with the DB service and table.
 */
class TableGatewayFactory
{
    public function __invoke($services)
    {
        $db    = 'Db\ExpensesLib';
        $table = 'expenses';
        if ($services->has('config')) {
            $config = $services->get('config');
            switch (isset($config['expenseslib'])) {
                case true:
                    $config = $config['expenseslib'];
                    $db     = isset($config['db']) ? $config['db'] : $db;
                    $table  = isset($config['table']) ? $config['table'] : $table;
                    break;
                case false:
                default:
                    break;
            }
        }

        if (! $services->has($db)) {
            throw new DomainException(sprintf(
                'Unable to create %s due to missing "%s" service',
                TableGateway::class,
                $db
            ));
        }

        return new TableGateway($table, $services->get($db));
    }
}
