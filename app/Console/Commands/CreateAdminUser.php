<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * @var string
     */
    protected $signature = 'cityfix:create-admin {--name= : Nama admin} {--email= : Email admin}';

    /**
     * @var string
     */
    protected $description = 'Buat akun admin CityFix (production) dengan password kuat, wajib diganti saat login pertama';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama admin', 'CityFix Administrator');
        $email = $this->option('email') ?: $this->ask('Email admin');
        $password = $this->secret('Password sementara (min. 12 karakter)');

        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(12)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'must_change_password' => true,
        ]);

        $this->info("Admin {$email} berhasil dibuat. Password wajib diganti saat login pertama.");

        return self::SUCCESS;
    }
}
