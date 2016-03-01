<?php

namespace Oro\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

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
        return array('entity_class' => 'Oro\Bundle\IssueBundle\Entity\Issue');
    }

    /**
     * @Route("/create", name="issue_create")
     * @Acl(
     *      id="issue_create",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("OroIssueBundle:Issue:update.html.twig")
     * @return array
     */
    public function createAction(Request $request)
    {
        return $this->update(new Issue(), $request);
    }

    /**
     * @Route("/update/{id}", name="issue_update", requirements={"id"="\d+"})
     * @Template()
     * @Acl(
     *      id="issue_update",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="EDIT"
     * )
     * @param Issue $issue
     * @return array
     */
    public function updateAction(Issue $issue, Request $request)
    {
        return $this->update($issue, $request);
    }

    private function update(Issue $issue, Request $request)
    {
        $form = $this->get('form.factory')->create('issue_form', $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                array(
                    'route' => 'issue_update',
                    'parameters' => array('id' => $issue->getId()),
                ),
                array('route' => 'issue_view'),
                $issue
            );
        }

        return array(
            'entity' => $issue,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/view/{id}", name="issue_view", requirements={"id"="\d+"})
     * @AclAncestor("issue_view")
     * @Template()
     * @param Issue $issue
     * @return array
     */
    public function viewAction(Issue $issue)
    {
        return array('entity' => $issue);
    }
}