<?php

namespace Oro\Bundle\IssueBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\IssueBundle\Migrations\Schema\v1_0\OroIssueBundle;
use Oro\Bundle\IssueBundle\Migrations\Schema\v1_1\NoteIssue;
use Oro\Bundle\IssueBundle\Migrations\Schema\v1_3\ActivityIssue;

class OroCRMTaskBundleInstaller implements
    Installation,
    NoteExtensionAwareInterface,
    ActivityExtensionAwareInterface
{
    /** @var NoteExtension */
    protected $noteExtension;

    /**
     * @var ActivityExtension
     */
    protected $activityExtension;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_3';
    }

    /**
     * {@inheritdoc}
     */
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        OroIssueBundle::addIssueTable($schema);
        NoteIssue::addNoteAssociations($schema, $this->noteExtension);
        ActivityIssue::addActivityAssociations($schema, $this->activityExtension);
    }
}
