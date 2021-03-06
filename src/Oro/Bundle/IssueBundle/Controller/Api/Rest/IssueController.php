<?php

namespace Oro\Bundle\IssueBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\HttpDateTimeParameterFilter;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\IdentifierToReferenceFilter;

/**
 * @RouteResource("issue")
 * @NamePrefix("tracker_api_")
 */
class IssueController extends RestController
{
    /**
     * REST GET list
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. defaults to 10."
     * )
     * @ApiDoc(
     *      description="Get all issue items",
     *      resource=true
     * )
     * @AclAncestor("issue_view")
     * @return Response
     */
    public function cgetAction()
    {
        $page  = (int)$this->getRequest()->get('page', 1);
        $limit = (int)$this->getRequest()->get('limit', self::ITEMS_PER_PAGE);
        $criteria = $this->getFilterCriteria($this->getSupportedQueryParameters(__FUNCTION__));
        return $this->handleGetListRequest($page, $limit, $criteria);
    }

    /**
     * REST GET item
     *
     * @param string $id
     *
     * @ApiDoc(
     *      description="Get issue item",
     *      resource=true
     * )
     * @AclAncestor("issue_view")
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT
     *
     * @param int $id Issue item id
     *
     * @ApiDoc(
     *      description="Update issue",
     *      resource=true
     * )
     * @AclAncestor("issue_update")
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new issue
     *
     * @ApiDoc(
     *      description="Create new issue",
     *      resource=true
     * )
     * @AclAncestor("issue_create")
     * @return Response
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     * @return Response
     *
     * @ApiDoc(
     *      description="Delete Issue",
     *      resource=true
     * )
     * @Acl(
     *      id="issue_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="OroIssueBundle:Issue"
     * )
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('issue.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->get('issue.form.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->get('issue.form.handler.issue.api');
    }
}
