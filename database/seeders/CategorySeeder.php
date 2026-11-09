<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomeCategories = [
            ['name' => 'Uang Saku',    'icon' => 'wallet',         'color' => '#10b981'],
            ['name' => 'Gaji',         'icon' => 'currency-dollar', 'color' => '#059669'],
            ['name' => 'Beasiswa',     'icon' => 'academic-cap',   'color' => '#3b82f6'],
            ['name' => 'Hadiah',       'icon' => 'gift',           'color' => '#ec4899'],
            ['name' => 'Bonus',        'icon' => 'star',           'color' => '#f59e0b'],
            ['name' => 'Hasil Jualan', 'icon' => 'shopping-bag',   'color' => '#8b5cf6'],
            ['name' => 'Freelance',    'icon' => 'briefcase',      'color' => '#6366f1'],
            ['name' => 'Lainnya',      'icon' => 'dots-horizontal', 'color' => '#64748b'],
        ];

        foreach ($incomeCategories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name'], 'type' => 'income', 'user_id' => null],
                [
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }

        $expenseCategories = [
            ['name' => 'Makan',              'icon' => 'cake',            'color' => '#ef4444'],
            ['name' => 'Minuman',            'icon' => 'beaker',          'color' => '#f97316'],
            ['name' => 'Transportasi',       'icon' => 'truck',           'color' => '#f59e0b'],
            ['name' => 'Pendidikan',         'icon' => 'book-open',       'color' => '#06b6d4'],
            ['name' => 'Jajan',              'icon' => 'emoji-happy',     'color' => '#f43f5e'],
            ['name' => 'Internet',           'icon' => 'wifi',            'color' => '#3b82f6'],
            ['name' => 'Pulsa',              'icon' => 'device-mobile',   'color' => '#0ea5e9'],
            ['name' => 'Kesehatan',          'icon' => 'heart',           'color' => '#10b981'],
            ['name' => 'Hiburan',            'icon' => 'film',            'color' => '#a855f7'],
            ['name' => 'Belanja',            'icon' => 'shopping-cart',   'color' => '#ec4899'],
            ['name' => 'Kebutuhan Sekolah',  'icon' => 'pencil',          'color' => '#14b8a6'],
            ['name' => 'Sosial',             'icon' => 'users',           'color' => '#6366f1'],
            ['name' => 'Tagihan',            'icon' => 'document-text',   'color' => '#78716c'],
            ['name' => 'Lainnya',            'icon' => 'dots-horizontal', 'color' => '#64748b'],
        ];

        foreach ($expenseCategories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name'], 'type' => 'expense', 'user_id' => null],
                [
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
