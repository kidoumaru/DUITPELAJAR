<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:promote {email}', function () {
    $email = $this->argument('email');

    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error("Pengguna dengan email '{$email}' tidak ditemukan. Tidak ada perubahan yang dilakukan.");
        return;
    }

    $user->role = Role::Admin;
    $user->save();

    $this->info("Berhasil! Pengguna '{$user->name}' ({$user->email}) kini memiliki peran admin.");
})->purpose('Promote an existing user to the admin role');
