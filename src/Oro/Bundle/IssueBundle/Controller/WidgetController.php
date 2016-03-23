<?php

namespace Oro\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\SecurityBundle\Annotation\Acl;

/**
 * @Route("/issue")
 */
class WidgetController extends Controller
{
    /**
     * @Route("/updatedatawidget/{id}", name="update_data_widget")
     * @Acl(
     *      id="issue_view",
     *      type="entity",
     *      class="OroIssueBundle:Issue",
     *      permission="VIEW"
     * )
     * @Template("OroIssueBundle:Issue:dataWidget.html.twig")
     * @param Issue $issue
     * @param Request $request
     * @return array
     */
    public function updateDateWidgetAction(Issue $issue, Request $request)
    {
        return [
            'entity' => $issue
        ];
    }

    /**
     * @Route("/barchart/{widget}", name="issue_barchart", requirements={"widget"="[\w-]+"})
     * @Template("OroIssueBundle:Dashboard:bar_chart.html.twig")
     *
     * @param $widget
     * @return array $widgetAttr
     */
    public function statusBarChartAction($widget)
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
    public function issueShortGridAction($widget)
    {
        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['user'] = $this->getUser();
        return $widgetAttr;
    }
}
