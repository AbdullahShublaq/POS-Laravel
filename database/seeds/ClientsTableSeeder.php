<?php

use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $clients = ['abdullah', 'ash'];
        foreach ($clients as $client){
            \App\Client::create([
                'name' => $client,
                'phone' => ["123456"],
                'address' => 'address',
            ]);
        }
    }
}
