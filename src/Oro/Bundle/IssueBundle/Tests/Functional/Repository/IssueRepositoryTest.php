<?php

namespace Oro\Bundle\IssueBundle\Tests\Functional\Repository;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\IssueBundle\Entity\Issue;

/**
 * Class ProductRepositoryFunctionalTest
 * @package Oro\TrackerBundle\Tests\Functional\Repository
 * @dbIsolation
 * @dbReindex
 */
class ProductRepositoryFunctionalTest extends WebTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testSearchByStatus()
    {
        $container = $this->getContainer()->get('doctrine.orm.entity_manager');
        $issues = $container
            ->getRepository('OroIssueBundle:Issue')
            ->findGroupedByStatus();

        $this->assertEquals(
            'SELECT count(issue.id)  AS issues, step.label AS step_name FROM OroIssueBundle:Issue issue '.
            'LEFT JOIN OroWorkflowBundle:WorkflowStep workflowStep WITH issue.workflowStep = workflowStep '.
            'GROUP BY workflowStep.id',
            $issues->getDQL()
        );

        $this->assertCount(2, $issues->getQuery()->getResult());
    }
}
