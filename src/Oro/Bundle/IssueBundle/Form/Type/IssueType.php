<?php

namespace Oro\Bundle\IssueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Oro\Bundle\IssueBundle\Entity\Issue;

class IssueType extends AbstractType
{
    const BUG = 'bug';
    const TASK = 'task';
    const SUBTASK = 'subTask';
    const STORY = 'story';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'code',
                'text',
                [
                    'required' => true,
                    'label' => 'issue.code'
                ]
            )
            ->add(
                'summary',
                'text',
                [
                    'required' => true,
                    'label' => 'issue.summary'
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'choices' => $this->getIssueTypes(),
                    'label' => 'issue.type',
                    'required' => true
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => false,
                    'label' => 'issue.description'
                ]
            )
            ->add(
                'priority',
                'translatable_entity',
                [
                    'label' => 'issue.priority',
                    'class' => 'Oro\Bundle\IssueBundle\Entity\IssuePriority',
                    'required' => true,
                ]
            )
            ->add(
                'assignee',
                'translatable_entity',
                [
                    'label' => 'issue.assignee',
                    'class' => 'Oro\Bundle\UserBundle\Entity\User',
                    'required' => true
                ]
            )
            ->add(
                'relatedIssues',
                'translatable_entity',
                [
                    'label' => 'issue.related',
                    'class' => 'Oro\Bundle\IssueBundle\Entity\Issue',
                    'multiple' => true,
                    'required' => false
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Oro\Bundle\IssueBundle\Entity\Issue'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_issue';
    }

    /**
     * @return array
     */
    public function getIssueTypes()
    {
        return [
            self::BUG => 'Bug',
            self::TASK => 'Task',
            self::STORY => 'Story'
        ];
    }
}
