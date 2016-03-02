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
     * @Template()
     * @Acl(
     *      id="issue_view",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="VIEW"
     * )
     */
    public function indexAction()
    {
        return array('gridName' => 'issue-grid');
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
        $result =  $this->update(new Issue(), $request);

        if ($request->query->get('_wid')) {
            $result['formAction'] = $this->generateUrl('issue_create');
        }

        return $result;
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
        /*$form = $this->get('form.factory')->create('issue_form', $issue);
        $handler = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                array(
                    'route' => 'issue_update',
                    'parameters' => array('id' => $issue->getId()),
                ),
                array('route' => 'issue_view',
                    'parameters' => array('id' => $issue->getId())
                ),
                $issue
            );
        }

        return array(
            'entity' => $issue,
            'form' => $form->createView(),
        );
        */
        $saved = false;
        //$form = $this->createForm($this->getFormType(), $issue);
        $form = $this->get('form.factory')->create('issue_form', $issue);
        if ($this->get('issue.form.handler.issue')->process($issue)) {

            $saved = true;
            if (!$request->query->get('_widgetContainer')) {
                return $this->get('oro_ui.router')->redirectAfterSave(
                    ['route' => 'issue_update', 'parameters' => ['id' => $issue->getId()]],
                    ['route' => 'issue_view', 'parameters' => ['id' => $issue->getId()]],
                    $issue
                );
            }
        }

        return array(
            'saved'  => $saved,
            'entity' => $issue,
            'form'   => $form->createView()
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