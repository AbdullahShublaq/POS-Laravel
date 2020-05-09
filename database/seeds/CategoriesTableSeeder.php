<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categoris = ['category one', 'category two'];
        foreach ($categoris as $category){
            \App\Category::create([
                'ar' => ['name' => $category . ' ar'],
                'en' => ['name' => $category . ' en'],
            ]);
        }

    }
}
