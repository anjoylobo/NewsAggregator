<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * Factory class for generating dummy `User` model data.
 *
 * This class defines how to generate random data for the `User` model 
 * attributes, which can be used for seeding or testing purposes. 
 * It includes methods for generating users with personal teams, unverified emails, and soft deletion.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     *
     * This password is used for generating users with the same default password.
     * It's set only once to avoid re-hashing on every factory invocation.
     *
     * @var string|null
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * This method returns an array with the default attributes for creating 
     * a new `User` model, including name, email, password, etc.
     *
     * @return array<string, mixed> The default attributes for the `User` model.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Default password for all users
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * This method modifies the state of the user model so that the email 
     * verification field (`email_verified_at`) is set to null.
     *
     * @return static The current instance of the factory with unverified email state.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * This method adds a personal team to the user if the application has 
     * team features enabled. It also allows for customization of the 
     * team through a callback.
     *
     * @param callable|null $callback Optional callback to customize the team's attributes.
     * @return static The current instance of the factory with a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }

    /**
     * Indicate that the user is soft deleted.
     *
     * This method modifies the state of the user model to indicate it is 
     * deleted, which is useful for testing soft deletes in the application.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory The current instance of the factory with the deleted state.
     */
    public function deleted()
    {
        return $this->state(fn (array $attributes) => [
            'deleted' => true,
        ]);
    }
}
