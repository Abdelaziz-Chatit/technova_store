<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Order')
            ->setEntityLabelInPlural('Orders')
            ->setSearchFields(['customerName', 'customerEmail', 'id'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(25);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setLabel('Order #')
                ->onlyOnIndex(),
            AssociationField::new('user')
                ->setRequired(false)
                ->autocomplete(),
            TextField::new('customerName')
                ->setLabel('Customer Name')
                ->setRequired(true),
            TextField::new('customerEmail')
                ->setLabel('Email')
                ->setRequired(true),
            TextField::new('customerPhone')
                ->setLabel('Phone')
                ->hideOnIndex(),
            TextField::new('shippingAddress')
                ->setLabel('Shipping Address')
                ->hideOnIndex(),
            MoneyField::new('totalAmount')
                ->setCurrency('USD')
                ->setStoredAsCents(false)
                ->setLabel('Total'),
            ChoiceField::new('status')
                ->setChoices([
                    'Pending' => 'pending',
                    'Paid' => 'paid',
                    'Shipped' => 'shipped',
                    'Delivered' => 'delivered',
                    'Cancelled' => 'cancelled',
                ])
                ->setRequired(true),
            DateTimeField::new('createdAt')
                ->setLabel('Order Date')
                ->setFormat('yyyy-MM-dd HH:mm')
                ->onlyOnIndex(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')
                ->setChoices([
                    'Pending' => 'pending',
                    'Paid' => 'paid',
                    'Shipped' => 'shipped',
                    'Delivered' => 'delivered',
                    'Cancelled' => 'cancelled',
                ]))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(EntityFilter::new('user'));
    }
}

