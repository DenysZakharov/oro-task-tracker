<?php

namespace Oro\Bundle\IssueBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{
    /**
     * Find issues grouped by status
     *
     * @return  array
     */
    public function findGroupedByStatus()
    {
        $query = $this
            ->createQueryBuilder('issue')
            ->select('COUNT(issue.id) AS issues, step.label AS step_name')
            ->leftJoin('issue.workflowStep', 'step')
            ->groupBy('step.id')
            ->getQuery();
        return $query->getResult();
    }
}