<?php

namespace Oro\Bundle\IssueBundle\Filter;

use Doctrine\ORM\EntityManager;

class CommandFilter
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getUserChoices()
    {
        $result = [];
        $objects  = $this->em->getRepository('OroUserBundle:User')->findAll();

        foreach ($objects as $obj) {
            $result[$obj->getId()] = $obj->getFirstName();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getPriorityChoices()
    {
        $result = [];
        $objects  = $this->em->getRepository('OroIssueBundle:IssuePriority')->findAll();
        foreach ($objects as $obj) {
            $result[$obj->getCode()] = $obj->getLabel();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getResolutionChoices()
    {
        $result = [];
        $objects  = $this->em->getRepository('OroIssueBundle:IssueResolution')->findAll();
        foreach ($objects as $obj) {
            $result[$obj->getCode()] = $obj->getLabel();
        }

        return $result;
    }
}
