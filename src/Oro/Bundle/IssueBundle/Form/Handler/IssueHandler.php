<?php

namespace Oro\Bundle\IssueBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\IssueBundle\Entity\Issue;

class IssueHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param ObjectManager $manager
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $manager
    ) {
        $this->form = $form;
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param Issue $entity
     * @param \Oro\Bundle\UserBundle\Entity\User $currentUser
     * @return bool  True on successful processing, false otherwise
     */
    public function process(Issue $entity, $currentUser)
    {
        $this->checkForm($entity, $currentUser);
        $this->form->setData($entity);
        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->form->submit($this->request);
            if ($this->form->isValid()) {
                $this->onSuccess($entity);
                return true;
            }
        }
        return false;
    }

    /**
     * Get form, that build into handler, via handler service
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Issue $entity
     * @param $currentUser
     */
    protected function checkForm(Issue $entity, $currentUser)
    {
        if ($entity->getId() || $entity->getParent()) {
            $this->form->remove('type');
        } else {
            $entity->setReporter($currentUser);
        }
    }

    /**
     * "Success" form handler
     *
     * @param Issue $entity
     */
    protected function onSuccess(Issue $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
