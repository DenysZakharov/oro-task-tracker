<?php

namespace Oro\Bundle\IssueBundle\Migrations\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Oro\Bundle\IssueBundle\Entity\IssueResolution;

class LoadIssueResolution extends AbstractFixture implements VersionedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.2';
    }

    /**
     * @var array
     */
    protected static $data = [
        [
            'code' => 'fixed',
            'label' => 'Fixed',
            'priority' => '10'
        ],
        [
            'code' => 'unresolved',
            'label' => 'Unresolved',
            'priority' => '20'
        ],
        [
            'code' => 'duplicate',
            'label' => 'Duplicate',
            'priority' => '30'
        ],
        [
            'code' => 'done',
            'label' => 'Done',
            'priority' => '40'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$data as $item) {
            $entity = new IssueResolution();
            $entity->setCode($item['code']);
            $entity->setLabel($item['label']);
            $entity->setPriority($item['priority']);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
