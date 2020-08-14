<?php
namespace App\Form;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// This class file is used to create the form with specified input fields

class ProductType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('brochure', FileType::class, ['label' => array('style' => 'width:300px;'), 'label' => 'Image (Image file)', 'attr' => array('style' => 'width:30%', 'placeholder' => 'Product Image'), 'mapped' => false, 'required' => true, 'constraints' => [new File(['maxSize' => '1024k', 'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'], 'mimeTypesMessage' => 'Please upload only images of type [.png,.jpeg,jpg]', ]) ], ])
				->add('name', TextType::class, ['label' => 'Product Name', 'required' => true, 'attr' => array('style' => 'width:30%;', 'placeholder' => 'Product name') ])
				->add('Description', TextType::class, ['label' => 'Product Description', 'required' => true, 'attr' => array('style' => 'width:30%', 'placeholder' => 'Product Description') ])
				->add('Price', TextType::class, ['label' => 'Product Price', 'required' => true, 'attr' => array('style' => 'width:30%', 'placeholder' => 'Product Price') ])
				->add('save', SubmitType::class, ['attr' => ['class' => 'btn add-to-cart', 'name' => 'Add Product']]);
    }
	
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['data_class' => Product::class, ]);
    }
}
?>