<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = Position::select('id')->get();
        $createAt = Carbon::now()->subDays(rand(1, 30))
            ->setTimeFrom(Carbon::createFromTime(rand(0, 23), rand(0, 59), rand(0, 59)))
            ->format('Y-m-d  H:i:s');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->e164PhoneNumber(),
            'position_id' => $positions->random()->id,
            'photo' => $this->generateUserPhoto(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => $createAt
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    private function generateUserPhoto(): string
    {
        if (!Storage::disk('public')->exists('users')) {
            Storage::disk('public')->makeDirectory('users');
        }

        $path = database_path('imageExample/userLogo.png');
        $imageName = Str::random(20) . '.png';
        $destinationPath = 'users' . '/' . $imageName;

        Storage::disk('public')->put($destinationPath, File::get($path));

        return $imageName;
    }
}
