<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'title' => 'Home',
                'route_name' => 'home',
                'order' => 1,
                'location' => 'main',
            ],
            [
                'title' => 'Doctors',
                'route_name' => 'doctors.search',
                'order' => 2,
                'location' => 'main',
                'children' => [
                    ['title' => 'Find Doctors', 'route_name' => 'doctors.search', 'order' => 1],
                    ['title' => 'Doctor Register', 'route_name' => 'doctor.register', 'order' => 2],
                    ['title' => 'Doctor Login', 'route_name' => 'doctor.login', 'order' => 3],
                ],
            ],
            [
                'title' => 'Products',
                'route_name' => 'ecommerce.products',
                'order' => 3,
                'location' => 'main',
            ],
            [
                'title' => 'Courses',
                'route_name' => 'courses.index',
                'order' => 4,
                'location' => 'main',
            ],
            [
                'title' => 'Pages',
                'route_name' => null,
                'order' => 5,
                'location' => 'main',
                'children' => [
                    ['title' => 'Privacy Policy', 'route_name' => 'privacy', 'order' => 1],
                    ['title' => 'Terms & Conditions', 'route_name' => 'terms', 'order' => 2],
                    ['title' => 'Contact/Components', 'route_name' => 'components', 'order' => 3],
                ],
            ],
        ];

        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $menu = Menu::updateOrCreate(
                [
                    'title' => $menuData['title'],
                    'location' => $menuData['location'],
                    'parent_id' => null,
                ],
                [
                    'url' => $menuData['url'] ?? null,
                    'route_name' => $menuData['route_name'] ?? null,
                    'icon' => $menuData['icon'] ?? null,
                    'order' => $menuData['order'] ?? 0,
                    'is_active' => true,
                    'open_in_new_tab' => $menuData['open_in_new_tab'] ?? false,
                ]
            );

            foreach ($children as $childData) {
                Menu::updateOrCreate(
                    [
                        'title' => $childData['title'],
                        'location' => 'main',
                        'parent_id' => $menu->id,
                    ],
                    [
                        'url' => $childData['url'] ?? null,
                        'route_name' => $childData['route_name'] ?? null,
                        'icon' => $childData['icon'] ?? null,
                        'order' => $childData['order'] ?? 0,
                        'is_active' => true,
                        'open_in_new_tab' => $childData['open_in_new_tab'] ?? false,
                    ]
                );
            }
        }
    }
}
