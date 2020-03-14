<?php

namespace App\Form\Type;

use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageType extends AbstractType
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * ImageType constructor.
     */
    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Image::class,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $image = $view->vars['data'];
        if ($image) {
            $view->vars['url'] = '/resize/xs'.$this->uploaderHelper->asset($image);
        }
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
