<?php

namespace Oro\Bundle\IssueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Oro\Bundle\IssueBundle\Model\ExtendIssue;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;
use Oro\Bundle\TagBundle\Entity\Taggable;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\IssueBundle\Entity\IssuePriority;
use Oro\Bundle\IssueBundle\Entity\IssueResolution;

/**
 * @ORM\Entity(repositoryClass="Oro\Bundle\IssueBundle\Entity\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *      name="tracker_issue",
 * )
 * @Config(
 *      defaultValues={
 *           "workflow"={
 *              "active_workflow"="issue_flow"
 *          },
 *          "security"={
 *              "type"="ACL"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *      }
 * )
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Issue extends  ExtendIssue implements Taggable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $summary;

    /**
     * @ORM\Column(name="`code`", type="string", length=50, unique=true)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     **/
    protected $priority;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
     **/
    protected $resolution;

    /**
     * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowItem;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowStep;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     **/
    protected $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     **/
    protected $assignee;

    /**
     * @ORM\ManyToMany(targetEntity="Issue", mappedBy="relatedIssues", cascade="PERSIST")
     * @ORM\JoinTable(name="issue_related")
     **/
    protected $relatedIssues;

    /**
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="issue_collaborator")
     */
    protected $collaborators;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     **/
    protected $children;

    /**
     * @ORM\Column(name="created", type="datetime")
     **/
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     **/
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    protected $organization;

    protected $tags;

    public function __construct()
    {
        parent::__construct();

        $this->collaborators = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->relatedIssues = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function doOnPrePersist()
    {
        $this->created = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @param WorkflowItem $workflowItem
     * @return Opportunity
     */
    public function setWorkflowItem($workflowItem)
    {
        $this->workflowItem = $workflowItem;

        return $this;
    }

    /**
     * @return WorkflowItem
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * @param WorkflowItem $workflowStep
     * @return Opportunity
     */
    public function setWorkflowStep($workflowStep)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    /**
     * @return WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * Returns the unique taggable resource identifier
     *
     * @return string
     */
    public function getTaggableId()
    {
        return $this->getId();
    }


    /**
     * Returns the collection of tags for this Taggable entity
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        if (!$this->tags) {
            $this->tags = new ArrayCollection();
        }

        return $this->tags;
    }

    /**
     * Set tag collection
     *
     * @param $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Issue
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Issue
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set type
     *
     * @param \Oro\Bundle\OrganizationBundle\Entity\Organization $type
     *
     * @return Issue
     */
    public function setType(\Oro\Bundle\OrganizationBundle\Entity\Organization $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Oro\Bundle\OrganizationBundle\Entity\Organization
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Priority $priority
     *
     * @return Issue
     */
    public function setPriority(\Oro\Bundle\IssueBundle\Entity\Priority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \Oro\Bundle\IssueBundle\Entity\Priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Resolution $resolution
     *
     * @return Issue
     */
    public function setResolution(\Oro\Bundle\IssueBundle\Entity\Resolution $resolution = null)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return \Oro\Bundle\IssueBundle\Entity\Resolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set reporter
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $reporter
     *
     * @return Issue
     */
    public function setReporter(\Oro\Bundle\UserBundle\Entity\User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \Oro\Bundle\UserBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $assignee
     *
     * @return Issue
     */
    public function setAssignee(\Oro\Bundle\UserBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Oro\Bundle\UserBundle\Entity\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Add relatedIssue
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $relatedIssue
     *
     * @return Issue
     */
    public function addRelatedIssue(\Oro\Bundle\IssueBundle\Entity\Issue $relatedIssue)
    {
        $this->relatedIssues[] = $relatedIssue;

        return $this;
    }

    /**
     * Remove relatedIssue
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $relatedIssue
     */
    public function removeRelatedIssue(\Oro\Bundle\IssueBundle\Entity\Issue $relatedIssue)
    {
        $this->relatedIssues->removeElement($relatedIssue);
    }

    /**
     * Get relatedIssues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedIssues()
    {
        return $this->relatedIssues;
    }

    /**
     * Add collaborator
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $collaborator
     *
     * @return Issue
     */
    public function addCollaborator(\Oro\Bundle\UserBundle\Entity\User $collaborator)
    {
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $collaborator
     */
    public function removeCollaborator(\Oro\Bundle\UserBundle\Entity\User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set parent
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $parent
     *
     * @return Issue
     */
    public function setParent(\Oro\Bundle\IssueBundle\Entity\Issue $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Oro\Bundle\IssueBundle\Entity\Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $child
     *
     * @return Issue
     */
    public function addChild(\Oro\Bundle\IssueBundle\Entity\Issue $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $child
     */
    public function removeChild(\Oro\Bundle\IssueBundle\Entity\Issue $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set owner
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $owner
     *
     * @return Issue
     */
    public function setOwner(\Oro\Bundle\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Oro\Bundle\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set organization
     *
     * @param \Oro\Bundle\OrganizationBundle\Entity\Organization $organization
     *
     * @return Issue
     */
    public function setOrganization(\Oro\Bundle\OrganizationBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Oro\Bundle\OrganizationBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
