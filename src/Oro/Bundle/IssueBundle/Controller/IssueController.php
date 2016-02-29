<?php

namespace Oro\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/issue")
 */
class IssueController extends Controller
{
    /**
     * @Route("/", name="issue_index")
     * @Template
     * @Acl(
     *     id="issue_view",
     *     type="entity",
     *     class="IssueBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction()
    {
        return array('gridName' => 'issue-grid');
    }
}