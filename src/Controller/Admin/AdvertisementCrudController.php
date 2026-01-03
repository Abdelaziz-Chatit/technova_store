<?php

namespace App\Controller\Admin;

use App\Entity\Advertisement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESPONSABLE')]
class AdvertisementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Advertisement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Advertisement')
            ->setEntityLabelInPlural('Advertisements')
            ->setSearchFields(['title', 'description'])
            ->setDefaultSort(['order' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title')
                ->setRequired(true)
                ->setHelp('Advertisement title/name'),
            TextareaField::new('description')
                ->setRequired(false)
                ->hideOnIndex()
                ->setHelp('Optional description or call-to-action text'),
            ImageField::new('image')
                ->setBasePath('/uploads/advertisements/')
                ->setUploadDir('public/uploads/advertisements/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setHelp('Advertisement banner image (recommend 1200x400px)'),
            TextField::new('link')
                ->setRequired(false)
                ->setHelp('URL to navigate to when clicking the ad (optional)'),
            IntegerField::new('order')
                ->setRequired(true)
                ->setHelp('Display order (lower numbers appear first)')
                ->setFormTypeOptions(['data' => 0]),
            BooleanField::new('isActive')
                ->setLabel('Active')
                ->setHelp('Show this advertisement on the homepage'),
            DateTimeField::new('createdAt')
                ->onlyOnIndex()
                ->setLabel('Created'),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex()
                ->setLabel('Updated'),
        ];
    }
}
