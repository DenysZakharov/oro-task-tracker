<?php

namespace Oro\Bundle\TrackerBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use Oro\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\IssueBundle\Entity\IssuePriority;
use Oro\Bundle\IssueBundle\Entity\IssueResolution;

class IssueFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Oro\Bundle\IssueBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('Issue Export Import Example');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * @param string $key
     * @param Issue $entity
     */
    public function fillEntityData($key, $entity)
    {
        $user = $this->templateManager
            ->getEntityRepository('Oro\Bundle\UserBundle\Entity\User')
            ->getEntity('den den');

        $entityRepository = 'Oro\Bundle\OrganizationBundle\Entity\Organization';
        $organization = $this->templateManager
            ->getEntityRepository($entityRepository)
            ->getEntity('oro');

        $priority = new IssuePriority();
        $priority->setCode('major');
        $priority->setLabel('Major');
        $priority->setPriority(1);

        $resolution = new IssueResolution();
        $resolution->setCode('fixed');
        $resolution->setLabel('Fixed');

        switch ($key) {
            case 'Issue Export Import Example':
                $entity->setCode('new code')
                    ->setSummary('Summary example')
                    ->setDescription('Description example')
                    ->setOwner($user)
                    ->setAssignee($user)
                    ->setReporter($user)
                    ->setOrganization($organization)
                    ->setPriority($priority)
                    ->setResolution($resolution)
                    ->setType('task');
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
