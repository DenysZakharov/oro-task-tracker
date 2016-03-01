<?php

namespace Oro\Bundle\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;

class IssueType extends AbstractType
{
    const BUG = 'bug';
    const TASK = 'task';
    const STORY = 'story';

    protected static $types = [
        self::BUG => 'Bug',
        self::TASK => 'Task',
        self::STORY => 'Story'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'code',
                'text',
                [
                    'required' => true,
                    'label' => 'oro.issue.code.label'
                ]
            )
            ->add(
                'summary',
                'text',
                [
                    'required' => true,
                    'label' => 'oro.issue.summary.label'
                ]
            )
            ->add(
                'type',
                'choice',
                array(
                    'choices' => self::getIssueTypes(),
                    'label' => 'oro.issue.type.label',
                    'required' => true
                )
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => false,
                    'label' => 'oro.issue.description.label'
                ]
            )
            ->add(
                'priority',
                'translatable_entity',
                [
                    'label' => 'oro.issue.priority.label',
                    'class' => 'Oro\Bundle\IssueBundle\Entity\IssuePriority',
                    'required' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->addOrderBy('p.order', 'DESC');
                    }
                ]
            )
            ->add(
                'assignee',
                'translatable_entity',
                [
                    'label' => 'oro.issue.assignee.label',
                    'class' => 'Oro\Bundle\UserBundle\Entity\User',
                    'required' => true
                ]
            )
            ->add(
                'related',
                'translatable_entity',
                [
                    'label' => 'oro.issue.related.label',
                    'class' => 'Oro\Bundle\IssueBundle\Entity\Issue',
                    'multiple' => true,
                    'required' => false
                ]
            )->add(
                'tags',
                'oro_tag_select',
                [
                    'label' => 'oro.tag.entity_plural_label'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\Bundle\IssueBundle\Entity\Issue',
        ));
    }

    public function getName()
    {
        return 'issue_form';
    }

    /**
     * @return array
     */
    public static function getIssueTypes()
    {
        return self::$types;
    }
}
