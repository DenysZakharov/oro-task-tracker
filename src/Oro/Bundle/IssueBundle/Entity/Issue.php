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
 *      routeName="issue_index",
 *      routeView="issue_view",
 *      defaultValues={
 *           "workflow"={
 *              "active_workflow"="issue_flow"
 *          },
 *          "grouping"={
 *              "groups"={"activity"}
 *          },
 *          "security"={
 *              "type"="ACL"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "tag"={
 *              "enabled"=true
 *          }
 *      }
 * )
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Issue extends ExtendIssue
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Id",
     *            "order"="10"
     *        }
     *    }
     * )
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Summary",
     *            "order"="30"
     *        }
     *    }
     * )
     */
    protected $summary;

    /**
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Code",
     *            "order"="20"
     *        }
     *    }
     * )
     */
    protected $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Description",
     *            "order"="40"
     *        }
     *    }
     * )
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Type",
     *            "order"="50"
     *        }
     *    }
     * )
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Priority",
     *            "order"="60"
     *        }
     *    }
     * )
     **/
    protected $priority;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Resolution",
     *            "order"="70"
     *        }
     *    }
     * )
     **/
    protected $resolution;

    /**
     * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     */
    protected $workflowItem;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     */
    protected $workflowStep;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Reporter",
     *            "order"="80",
     *            "short"=true
     *        }
     *    }
     * )
     **/
    protected $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Assignee",
     *            "order"="90",
     *            "short"=true
     *        }
     *    }
     * )
     **/
    protected $assignee;

    /**
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\IssueBundle\Entity\Issue", inversedBy="relatedIssues")
     * @ORM\JoinTable(name="issue_related")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="RelatedIssues",
     *            "order"="100"
     *        }
     *    }
     * )
     **/
    protected $relatedIssues;

    /**
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="issue_collaborator")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     */
    protected $collaborators;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IssueBundle\Entity\Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     **/
    protected $children;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     **/
    protected $createdAt;

    /**
     * @ORM\Column(name="updatedAt", type="datetime")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "excluded"=true
     *        }
     *    }
     * )
     **/
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Owner",
     *            "order"="150"
     *        }
     *    }
     * )
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * @ConfigField(
     *    defaultValues={
     *        "importexport"={
     *            "header"="Organization",
     *            "order"="160"
     *        }
     *    }
     * )
     */
    protected $organization;

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
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Issue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param \Oro\Bundle\IssueBundle\Entity\IssuePriority $priority
     *
     * @return Issue
     */
    public function setPriority(IssuePriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \Oro\Bundle\IssueBundle\Entity\IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param \Oro\Bundle\IssueBundle\Entity\IssueResolution $resolution
     *
     * @return Issue
     */
    public function setResolution(IssueResolution $resolution = null)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return \Oro\Bundle\IssueBundle\Entity\IssueResolution
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
    public function setReporter(User $reporter = null)
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
    public function setAssignee(User $assignee = null)
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
    public function setRelatedIssue(Issue $relatedIssue)
    {
        $this->relatedIssues[] = $relatedIssue;

        return $this;
    }

    /**
     * Remove relatedIssue
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $relatedIssue
     */
    public function removeRelatedIssue(Issue $relatedIssue)
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
     * @return $this
     */
    public function addCollaborator(User $collaborator)
    {
        if (!$this->hasCollaborator($collaborator)) {
            $this->collaborators[] = $collaborator;
        }
        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasCollaborator($user)
    {
        $collaborators = $this->getCollaborators();
        if ($collaborators->count()) {
            foreach ($collaborators as $collaborator) {
                if ($collaborator->getId() === $user->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Remove collaborator
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $collaborator
     */
    public function removeCollaborator(User $collaborator)
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
    public function setParent(Issue $parent = null)
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
    public function addChild(Issue $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Oro\Bundle\IssueBundle\Entity\Issue $child
     */
    public function removeChild(Issue $child)
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
    public function setOwner(User $owner = null)
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
    public function setOrganization(Organization $organization = null)
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getSummary();
    }
}
