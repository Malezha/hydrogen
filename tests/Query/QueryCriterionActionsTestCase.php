<?php

namespace RDS\Hydrogen\Tests\Query;

use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Join;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Query;
use RDS\Hydrogen\Tests\TestCase;

class QueryCriterionActionsTestCase extends TestCase
{
    public function testAddRemoveCriterion()
    {
        $query = Query::new();
        $criterion = new Where($query, 'field', '=', 'value');
        $query->add($criterion);

        $this->assertTrue($query->has($criterion));

        $query->remove($criterion);
        $this->assertFalse($query->has($criterion));
    }

    public function testOnlyCriterion()
    {
        $query = Query::new();

        $where = new Where($query, 'field', '=', 'value');
        $where2 = new Where($query, 'field2', '=', 'value2');
        $relation = new Join($query, 'user', Join::TYPE_LEFT_JOIN);

        $query->add($where)
            ->add($where2)
            ->add($relation);

        // Assert is all okey
        $this->assertAttributeEquals([
            $where,
            $where2,
            $relation
        ], 'criteria', $query);

        $onlyWhere = $query->only(Where::class);

        // Assert filter by string instance
        $this->assertAttributeEquals([
            (clone $where)->attach($onlyWhere),
            (clone $where2)->attach($onlyWhere)
        ], 'criteria', $onlyWhere);

        $onlyWhere2 = $query->only(function (CriterionInterface $criterion) {
            return $criterion instanceof Where && $criterion->getValue() === 'value';
        });

        $this->assertAttributeEquals([
            (clone $where)->attach($onlyWhere2)
        ], 'criteria', $onlyWhere2);
    }

    public function testExceptCriterion()
    {
        $query = Query::new();

        $where = new Where($query, 'field', '=', 'value');
        $where2 = new Where($query, 'field2', '=', 'value2');
        $relation = new Join($query, 'user', Join::TYPE_LEFT_JOIN);

        $query->add($where)
            ->add($where2)
            ->add($relation);

        // Assert is all okey
        $this->assertAttributeEquals([
            $where,
            $where2,
            $relation
        ], 'criteria', $query);

        $exceptRelation = $query->except(Join::class);

        // Assert filter by string instance
        $this->assertAttributeEquals([
            (clone $where)->attach($exceptRelation),
            (clone $where2)->attach($exceptRelation),
        ], 'criteria', $exceptRelation);

        $exceptRelation2 = $query->except(function (CriterionInterface $criterion) {
            return $criterion instanceof Where && $criterion->getValue() === 'value';
        });

        // Assert filter by string instance
        $this->assertAttributeEquals([
            (clone $where2)->attach($exceptRelation2),
            (clone $relation)->attach($exceptRelation2),
        ], 'criteria', $exceptRelation2);
    }
}