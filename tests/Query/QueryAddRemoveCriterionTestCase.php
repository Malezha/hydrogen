<?php

namespace RDS\Hydrogen\Tests\Query;

use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Query;

class QueryAddRemoveCriterionTestCase extends QueryTestCase
{
    public function testCriterion()
    {
        $query = Query::new();
        $criterion = new Where($query, 'field', '=', 'value');
        $query->add($criterion);

        $this->assertTrue($query->has($criterion));

        $query->remove($criterion);
        $this->assertFalse($query->has($criterion));
    }
}