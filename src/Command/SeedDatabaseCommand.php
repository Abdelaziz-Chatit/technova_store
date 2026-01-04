<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-database',
    description: 'Seed the database with tech products and user accounts',
)]
class SeedDatabaseCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info('Starting database seeding...');

            // Clear existing data
            $io->writeln('Clearing existing data...');
            $conn = $this->em->getConnection();
            $conn->executeStatement('DELETE FROM product');
            $conn->executeStatement('DELETE FROM category');
            $conn->executeStatement('DELETE FROM `user`');
            $conn->executeStatement('ALTER TABLE product AUTO_INCREMENT = 1');
            $conn->executeStatement('ALTER TABLE category AUTO_INCREMENT = 1');
            $conn->executeStatement('ALTER TABLE `user` AUTO_INCREMENT = 1');

            // Create Categories
            $io->writeln('Creating categories...');
            $categories = [
                ['name' => 'Laptops', 'slug' => 'laptops'],
                ['name' => 'Smartphones', 'slug' => 'smartphones'],
                ['name' => 'Tablets', 'slug' => 'tablets'],
                ['name' => 'Accessories', 'slug' => 'accessories'],
                ['name' => 'Gaming Gear', 'slug' => 'gaming-gear'],
            ];

            $categoryObjects = [];
            foreach ($categories as $cat) {
                $category = new Category();
                $category->setName($cat['name']);
                $category->setSlug($cat['slug']);
                $this->em->persist($category);
                $categoryObjects[$cat['slug']] = $category;
            }
            $this->em->flush();

            // Create Products
            $io->writeln('Creating tech products...');
            $products = [
                // Laptops
                [
                    'name' => 'MacBook Pro 16" M3 Max',
                    'description' => 'Premium laptop with Apple M3 Max chip, 16GB unified memory, 512GB SSD, Liquid Retina XDR display. Perfect for professionals and developers.',
                    'price' => 2499.99,
                    'stock' => 15,
                    'category' => 'laptops',
                ],
                [
                    'name' => 'Dell XPS 15',
                    'description' => 'Powerful ultrabook with Intel Core i7, 16GB RAM, RTX 4060, 512GB SSD. Great for content creation and programming.',
                    'price' => 1899.99,
                    'stock' => 20,
                    'category' => 'laptops',
                ],
                [
                    'name' => 'Lenovo ThinkPad X1 Carbon',
                    'description' => 'Business-class ultrabook with 14th Gen Intel Core i7, 16GB RAM, 512GB SSD. Lightweight and durable.',
                    'price' => 1599.99,
                    'stock' => 25,
                    'category' => 'laptops',
                ],
                [
                    'name' => 'ASUS ROG Gaming Laptop',
                    'description' => 'High-performance gaming laptop with RTX 4080, Intel i9, 32GB RAM, 1TB SSD, 144Hz display.',
                    'price' => 2799.99,
                    'stock' => 10,
                    'category' => 'laptops',
                ],
                // Smartphones
                [
                    'name' => 'iPhone 15 Pro Max',
                    'description' => 'Latest Apple flagship with A17 Pro chip, titanium design, advanced camera system, 12MP ultra-wide lens.',
                    'price' => 1299.99,
                    'stock' => 30,
                    'category' => 'smartphones',
                ],
                [
                    'name' => 'Samsung Galaxy S24 Ultra',
                    'description' => 'Premium Android phone with Snapdragon 8 Gen 3, 200MP main camera, 6.8" AMOLED display, S Pen included.',
                    'price' => 1399.99,
                    'stock' => 25,
                    'category' => 'smartphones',
                ],
                [
                    'name' => 'Google Pixel 8 Pro',
                    'description' => 'AI-powered phone with Google Tensor G3, advanced computational photography, 50MP camera, 6.7" OLED display.',
                    'price' => 999.99,
                    'stock' => 28,
                    'category' => 'smartphones',
                ],
                [
                    'name' => 'OnePlus 12',
                    'description' => 'Fast flagship killer with Snapdragon 8 Gen 3, 120Hz AMOLED display, OxygenOS 14, 5000mAh battery.',
                    'price' => 799.99,
                    'stock' => 35,
                    'category' => 'smartphones',
                ],
                // Tablets
                [
                    'name' => 'iPad Pro 12.9" M2',
                    'description' => 'Powerful tablet with Apple M2 chip, Liquid Retina XDR display, ProMotion 120Hz, 8GB RAM, 256GB storage.',
                    'price' => 1199.99,
                    'stock' => 18,
                    'category' => 'tablets',
                ],
                [
                    'name' => 'Samsung Galaxy Tab S9 Ultra',
                    'description' => 'Premium Android tablet with Snapdragon 8 Gen 2, 14.6" AMOLED 120Hz display, 12GB RAM, IP68 water resistance.',
                    'price' => 1099.99,
                    'stock' => 16,
                    'category' => 'tablets',
                ],
                [
                    'name' => 'iPad Air 6th Gen',
                    'description' => 'Mid-range tablet with M2 chip, 11" Liquid Retina display, 8GB RAM, perfect for creativity and productivity.',
                    'price' => 699.99,
                    'stock' => 22,
                    'category' => 'tablets',
                ],
                // Accessories
                [
                    'name' => 'AirPods Pro Max',
                    'description' => 'Premium wireless headphones with spatial audio, adaptive audio, 20-hour battery, premium aluminum design.',
                    'price' => 549.99,
                    'stock' => 40,
                    'category' => 'accessories',
                ],
                [
                    'name' => 'Sony WH-1000XM5 Headphones',
                    'description' => 'Industry-leading noise cancellation, 30-hour battery, multipoint connection, premium sound quality.',
                    'price' => 399.99,
                    'stock' => 45,
                    'category' => 'accessories',
                ],
                [
                    'name' => 'Samsung Galaxy Watch 6 Classic',
                    'description' => 'Smartwatch with rotating bezel, AMOLED display, health tracking, 5ATM water resistance.',
                    'price' => 399.99,
                    'stock' => 32,
                    'category' => 'accessories',
                ],
                [
                    'name' => 'Apple Watch Series 9',
                    'description' => 'Advanced smartwatch with S9 chip, double tap gesture, health tracking, fitness features.',
                    'price' => 429.99,
                    'stock' => 38,
                    'category' => 'accessories',
                ],
                [
                    'name' => 'USB-C Fast Charger 65W',
                    'description' => 'Universal fast charger compatible with laptops, tablets, and phones. GaN technology, compact design.',
                    'price' => 49.99,
                    'stock' => 100,
                    'category' => 'accessories',
                ],
                // Gaming Gear
                [
                    'name' => 'NVIDIA GeForce RTX 4090',
                    'description' => 'Flagship graphics card with 24GB GDDR6X memory, AI acceleration, ray tracing capabilities.',
                    'price' => 1599.99,
                    'stock' => 8,
                    'category' => 'gaming-gear',
                ],
                [
                    'name' => 'Corsair K95 Platinum XT Keyboard',
                    'description' => 'Mechanical gaming keyboard with Cherry MX switches, RGB lighting, media control, aluminum frame.',
                    'price' => 199.99,
                    'stock' => 22,
                    'category' => 'gaming-gear',
                ],
                [
                    'name' => 'SteelSeries Rival 600 Mouse',
                    'description' => 'High-precision gaming mouse with dual sensors, 18,000 CPI, 12,000 Hz polling rate, ergonomic design.',
                    'price' => 79.99,
                    'stock' => 50,
                    'category' => 'gaming-gear',
                ],
                [
                    'name' => 'HyperX Cloud Orbit S Gaming Headset',
                    'description' => 'Premium gaming headset with 3D spatial audio, active noise cancellation, memory foam ear cushions.',
                    'price' => 299.99,
                    'stock' => 18,
                    'category' => 'gaming-gear',
                ],
            ];

            foreach ($products as $prod) {
                $product = new Product();
                $product->setName($prod['name']);
                $product->setDescription($prod['description']);
                $product->setPrice($prod['price']);
                $product->setStock($prod['stock']);
                $product->setCategory($categoryObjects[$prod['category']]);
                $this->em->persist($product);
            }
            $this->em->flush();

            // Create Admin User
            $io->writeln('Creating admin user...');
            $admin = new User();
            $admin->setName('Admin User');
            $admin->setEmail('admin@technova.com');
            $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
            $admin->setPassword($hashedPassword);
            $this->em->persist($admin);
            $this->em->flush();

            // Create Manager User
            $io->writeln('Creating manager user...');
            $manager = new User();
            $manager->setName('Manager User');
            $manager->setEmail('manager@technova.com');
            $manager->setRoles(['ROLE_MANAGER', 'ROLE_USER']);
            $hashedPassword = $this->passwordHasher->hashPassword($manager, 'manager123');
            $manager->setPassword($hashedPassword);
            $this->em->persist($manager);
            $this->em->flush();

            $io->success('Database seeding completed successfully!');
            $io->section('Admin Credentials');
            $io->writeln('Email: <info>admin@technova.com</info>');
            $io->writeln('Password: <info>admin123</info>');
            $io->writeln('');
            $io->section('Manager Credentials');
            $io->writeln('Email: <info>manager@technova.com</info>');
            $io->writeln('Password: <info>manager123</info>');
            $io->writeln('');
            $io->writeln('✅ 20 tech products created across 5 categories!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error during seeding: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
