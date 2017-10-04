<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ExpensesLib;

use DomainException;

/**
 * Service factory for returning a ExpensesLib\TableGatewayMapper instance.
 *
 * Requires the ExpensesLib\TableGateway service be present in the service locator.
 */
class TableGatewayMapperFactory
{
    public function __invoke($services)
    {
        if (! $services->has('ExpensesLib\TableGateway')) {
            throw new DomainException(sprintf(
                'Cannot create %s; missing %s dependency',
                TableGatewayMapper::class,
                TableGateway::class
            ));
        }
        return new TableGatewayMapper($services->get(TableGateway::class));
    }
}
