<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/9/16
 * Time: 8:08 PM
 */

namespace Shop\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    const TYPE_NAME = 'user';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('first_name', TextType::class, [
                'required' => false
            ])
            ->add('last_name', TextType::class, [
                'required' => false
            ])
            ->add('phone')
            ->add('address', TextType::class, [
                'required' => false
            ])
            ->add('postcode', TextType::class, [
                'required' => false
            ])
            ->add('city', TextType::class,[
                'required' => false
            ])
            ->add('gender', TextType::class, [
                'required' => false
            ])
            ->add('birthday', BirthdayType::class, [
                'required' => false
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'formCustomer',
            'csrf_protection' => true,
            'allow_extra_fields' => true,
        ));
    }

    public function getName()
    {
        return self::TYPE_NAME;
    }
}