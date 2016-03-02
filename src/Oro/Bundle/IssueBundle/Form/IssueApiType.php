<?php

namespace Oro\Bundle\IssueBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueApiType extends IssueType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Oro\Bundle\IssueBundle\Entity\Issue',
                'intention' => 'issue',
                'cascade_validation' => true,
                'csrf_protection' => false
            ]
        );
    }

    public function getName()
    {
        return 'issue_api';
    }
}
