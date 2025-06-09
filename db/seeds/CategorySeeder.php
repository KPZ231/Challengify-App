<?php

use Phinx\Seed\AbstractSeed;
use Ramsey\Uuid\Uuid;

class CategorySeeder extends AbstractSeed
{
    public function run(): void
    {
        $categories = [
            [
                'id' => 'ec602ddd-44a8-11f0-aafb-74563c6dd840', // Creative Writing
                'name' => 'Creative Writing',
                'description' => 'Express your creativity through written narratives, poetry, and prose.',
                'slug' => 'creative-writing',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '7c8f9a10-44a8-11f0-aafb-74563c6dd841', // Graphic Design
                'name' => 'Graphic Design',
                'description' => 'Create visual content to communicate messages through typography, images, and colors.',
                'slug' => 'graphic-design',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'b5e6d2c3-44a8-11f0-aafb-74563c6dd842', // Programming
                'name' => 'Programming',
                'description' => 'Solve problems through code, develop applications, and create algorithms.',
                'slug' => 'programming',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'f1a2b3c4-44a8-11f0-aafb-74563c6dd843', // Photography
                'name' => 'Photography',
                'description' => 'Capture moments, express ideas, and tell stories through the lens.',
                'slug' => 'photography',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '1d2e3f4a-44a8-11f0-aafb-74563c6dd844', // Music & Audio
                'name' => 'Music & Audio',
                'description' => 'Create harmonies, compose melodies, and produce audio content.',
                'slug' => 'music-audio',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Get existing category names to avoid duplicates
        $existingCategories = $this->fetchAll('SELECT name FROM categories');
        $existingNames = array_column($existingCategories, 'name');
        
        $data = [];
        foreach ($categories as $category) {
            if (!in_array($category['name'], $existingNames)) {
                $data[] = $category;
            }
        }
        
        if (!empty($data)) {
            $this->table('categories')->insert($data)->saveData();
            $this->output->writeln('Added ' . count($data) . ' new categories');
        } else {
            $this->output->writeln('No new categories to add');
        }
    }
} 