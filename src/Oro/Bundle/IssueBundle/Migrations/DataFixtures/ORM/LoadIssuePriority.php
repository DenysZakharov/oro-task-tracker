<?php

namespace Oro\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\IssueBundle\Entity\IssuePriority;

class LoadIssuePriority extends AbstractFixture
{
    /**
     * @var array
     */
    protected static $data = [
        [
            'code' => 'major',
            'label' => 'Major',
            'priority' => 3
        ],
        [
            'code' => 'critical',
            'label' => 'Critical',
            'priority' => 4
        ],
        [
            'code' => 'minor',
            'label' => 'Minor',
            'priority' => 2
        ],
        [
            'code' => 'trivial',
            'label' => 'Trivial',
            'priority' => 1
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$data as $item) {
            $entity = new IssuePriority();
            $entity->setCode($item['code']);
            $entity->setLabel($item['label']);
            $entity->setPriority($item['priority']);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
