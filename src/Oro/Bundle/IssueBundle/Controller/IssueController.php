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
        return ['gridName' => 'issue-grid'];
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
        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('issue_create', $request);

        $result =  $this->update(new Issue(), $formAction);
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
    public function updateAction(Issue $issue)
    {
        $formAction = $this->get('router')->generate('issue_update', ['id' => $issue->getId()]);
        return $this->update($issue, $formAction);
    }

    private function update(Issue $issue, $formAction)
    {
        /*
        $form = $this->get('form.factory')->create('issue_form', $issue);
        //$handler = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route' => 'issue_update',
                    'parameters' => array('id' => $issue->getId()),
                ],
                [
                    'route' => 'issue_view',
                    'parameters' => array('id' => $issue->getId())
                ],
                $issue
            );
        }

        return array(
            'entity' => $issue,
            'form' => $form->createView(),
            //'formAction' => $formAction
        );
        */
        $saved = false;
        //$form = $this->createForm($this->getFormType(), $issue);
        //$form = $this->get('form.factory')->create('issue_form', $issue);
       //var_dump($this->get('issue.form.handler.issue'));die();
        if ($this->get('issue.form.handler.issue')->process($issue, $this->getUser())) {
            if (!$this->getRequest()->query->get('_widgetContainer')) {
                return $this->get('oro_ui.router')->redirectAfterSave(
                    ['route' => 'issue_update', 'parameters' => ['id' => $issue->getId()]],
                    ['route' => 'issue_view', 'parameters' => ['id' => $issue->getId()]],
                    $issue
                );
            }
            $saved = true;
        }
        $form = $this->get('issue.form.handler.issue')->getForm()->createView();
        return array(
            'saved'  => $saved,
            'entity' => $issue,
            'form'   => $form,
            'formAction' => $formAction
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
        return ['entity' => $issue];
    }

    /**
     * @Route("/create/subtask/{parentIssue}", name="subissue_create")
     * @Acl(
     *      id="issue_create",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("OroIssueBundle:Issue:update.html.twig")
     * @param string $parentIssue
     * @return array
     */
    public function createSubissueAction($parentIssue, Request $request)
    {
        $issue = new Issue();

        $issue->setParent($issue);
        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest(
                'subissue_create',
                $request,
                ['parentIssue' => $parentIssue]
            );
//var_dump($formAction);
        return $this->update($issue, $formAction);
    }
}