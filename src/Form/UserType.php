<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("frequency", ChoiceType::class, [
				"label" => "Sync frequency",
				"choices" => [
					"Every day" => "1 day",
					"Every hour" => "1 hour",
					"Every 15 minutes" => "15 minutes",
				]
			])
            ->add("paddleVendorId", null, ["label" => "Paddle Vendor ID"])
            ->add("paddleApiKey", null, ["label" => "Paddle API key"])
            ->add("chartMogulApiKey", null, ["label" => "Chart Mogul API key"])
			->add("submit", SubmitType::class, ["label" => "Save changes  💾"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
        ]);
    }
}
