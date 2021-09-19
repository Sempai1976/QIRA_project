<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['maxlength' => 255, 'required' => true],
                'label'  => 'Email',
            ])
            ->add('subject', null, [
                'attr' => ['maxlength' => 100, 'required' => true],
                'label'  => 'Subject',
            ])
            ->add('message', null, [
                'attr' => ['rows' => 7, 'maxlength' => 3000, 'required' => true],
                'label' => 'Text Body'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
                'label' => 'Send message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'form_name' => 'add_message'
        ]);
    }
}
