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

    private function update(Issue $issue, $formAction, $parentIssue = false)
    {
        $saved = false;
        if ($this->get('issue.form.handler.issue')->process($issue, $this->getUser(), $parentIssue)) {
            $saved = true;
            if (!$this->getRequest()->get('_widgetContainer')) {
                return $this->get('oro_ui.router')->redirectAfterSave(
                    ['route' => 'issue_update', 'parameters' => ['id' => $issue->getId()]],
                    ['route' => 'issue_view', 'parameters' => ['id' => $issue->getId()]],
                    $issue
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
     * @Route("/create/subtask/{issue}", name="subissue_create", requirements={"id"="\d+"})
     * @Acl(
     *      id="issue_create",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("OroIssueBundle:Issue:update.html.twig")
     * @param Issue $issue
     * @return array
     */
    public function createSubissueAction(Issue $issue, Request $request)
    {
        $subIssue = new Issue();
        $subIssue
            ->setReporter($this->getUser())
            ->setParent($issue)
            ->setType(IssueType::SUBTASK);

        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest(
                'subissue_create',
                $request,
                ['issue' => $issue]
            );
        return $this->update($subIssue, $formAction, $issue);
    }

    /**
     * @Route("/barchart/{widget}", name="issue_barchart", requirements={"widget"="[\w-]+"})
     * @Template("OroIssueBundle:Dashboard:bar_chart.html.twig")
     *
     * @param $widget
     * @return array $widgetAttr
     */
    public function statusbarchartAction($widget)
    {
        $data = $this->getDoctrine()->getRepository('OroIssueBundle:Issue')->findGroupedByStatus();

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($data)
            ->setOptions(
                [
                    'name' => 'bar_chart',
                    'data_schema' => [
                        'label' => ['field_name' => 'step_name'],
                        'value' => ['field_name' => 'issues']
                    ]
                ]
            )
            ->getView();

        return $widgetAttr;
    }

    /**
     * @Route("/issueshortgrid/{widget}", name="issue_shortgrid", requirements={"widget"="[\w-]+"})
     * @Template("OroIssueBundle:Dashboard:issue_short_grid.html.twig")
     *
     * @param $widget
     * @return array $widgetAttr
     */
    public function issueshortgridAction($widget)
    {
        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['user'] = $this->getUser();

        return $widgetAttr;
    }
}