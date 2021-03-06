<?php

namespace Oro\Bundle\IssueBundle\Controller;

use Oro\Bundle\IssueBundle\Form\Type\IssueType;
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
        return [
            'gridName' => 'issue-grid',
            'entity_class' => $this->container->getParameter('issue.entity')
        ];
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
     * @param Request $request
     * @return array
     */
    public function createAction(Request $request)
    {
        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('issue_create', $request);

        return $this->update(new Issue(), $formAction, $request);
    }

    /**
     * @Route("/createwidget", name="issue_create_widget")
     * @Acl(
     *      id="issue_create",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("OroIssueBundle:Issue:updateWidget.html.twig")
     * @param Request $request
     * @return array
     */
    public function createWidgetAction(Request $request)
    {
        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('issue_create_widget', $request);

        return $this->update(new Issue(), $formAction, $request);
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
     * @param Request $request
     * @return array
     */
    public function updateAction(Issue $issue, Request $request)
    {
        $formAction = $this->get('router')->generate('issue_update', ['id' => $issue->getId()]);
        return $this->update($issue, $formAction, $request);
    }

    /**
     * @param Issue $issue
     * @param string $formAction
     * @param Request $request
     * @return array
     */
    private function update(Issue $issue, $formAction, Request $request)
    {
        $saved = false;
        if ($this->get('issue.form.handler.issue')->process($issue, $this->getUser())) {
            $saved = true;
            if (!$request->get('_widgetContainer')) {
                return $this->get('oro_ui.router')->redirectAfterSave(
                    ['route' => 'issue_update', 'parameters' => ['id' => $issue->getId()]],
                    ['route' => 'issue_view', 'parameters' => ['id' => $issue->getId()]]
                );
            }
        }

        return [
            'entity' => $issue,
            'saved'  => $saved,
            'form'   => $this->get('issue.form.handler.issue')->getForm()->createView(),
            'formAction' => $formAction
        ];

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
     * @Route("/create/subtask/{issueCode}", name="subissue_create")
     * @Acl(
     *      id="issue_create",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("OroIssueBundle:Issue:update.html.twig")
     * @param $issueCode
     * @param Request $request
     * @return array
     */
    public function createSubissueAction($issueCode, Request $request)
    {
        $subIssue = new Issue();
        $parent = $this->getDoctrine()->getRepository('OroIssueBundle:Issue')->findOneByCode($issueCode);
        $subIssue
            ->setReporter($this->getUser())
            ->setParent($parent)
            ->setType(IssueType::SUBTASK);
        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest(
                'subissue_create',
                $request,
                ['issueCode' => $issueCode]
            );
        return $this->update($subIssue, $formAction, $request);
    }
}
