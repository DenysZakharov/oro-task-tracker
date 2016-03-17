<?php
namespace Oro\Bundle\IssueBundle\Migrations\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Oro\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\IssueBundle\Form\Type\IssueType;

class LoadIssueData extends AbstractFixture implements VersionedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * @var array $issues
     */
    protected static $issues = [
        [
            'summary' => 'My new story',
            'code' => 'new story',
            'type' => IssueType::STORY
        ],
        [
            'summary' => 'Create database',
            'code' => 'database',
            'type' => IssueType::TASK
        ],
        [
            'summary' => 'Issue CRUD',
            'code' => 'crud',
            'type' => IssueType::BUG
        ],
        [
            'summary' => 'My new subtask',
            'code' => 'subtask',
            'type' => IssueType::SUBTASK
        ],
        [
            'summary' => 'Estimates',
            'code' => 'estimates',
            'type' => IssueType::SUBTASK
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $users = $manager->getRepository('OroUserBundle:User')->findAll();
        $priorities = $manager->getRepository('OroIssueBundle:IssuePriority')->findAll();

        $usersCount = count($users);
        $priorityCount = count($priorities);

        $story = null;

        foreach (self::$issues as $data) {
            $issue = new Issue();
            $issue->setOrganization($organization);
            $issue->setSummary($data['summary']);
            $issue->setCode($data['code']);
            $issue->setDescription($data['summary']);
            $issue->setAssignee($users[rand(0, $usersCount - 1)]);
            $issue->setOwner($users[rand(0, $usersCount - 1)]);
            $issue->setReporter($users[rand(0, $usersCount - 1)]);
            $issue->setType($data['type']);
            $issue->setPriority($priorities[rand(0, $priorityCount - 1)]);
            if ($data['type'] == IssueType::SUBTASK) {
                if (!$issue) {
                    continue;
                }
                $issue->setParent($story);
            }
            if ($data['type'] === IssueType::STORY) {
                $story = $issue;
            }
            $manager->persist($issue);
        }

        $manager->flush();
    }
}
