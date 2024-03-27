<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\User;
use Carbon\Carbon;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = fake()->dateTimeBetween('now', '+3 month');
        $checkOutDate = fake()->dateTimeBetween($checkInDate->format('Y-m-d'), $checkInDate->format('Y-m-d'). '+7days');
        $total = fake()->numberBetween(1, 10);
        $men = fake()->numberBetween(1, $total);
        $female = $total - $men;

        return [
            'user_id' => User::factory(),
            'checkin_date' => $checkInDate,
            'checkin_time' => \Carbon\Carbon::createFromFormat('H:i', fake()->time('15:00', '23:59'))->format('H:00'),
            'checkout_date' => $checkOutDate,
            'total' => $total,
            'number_of_room' => fake()->numberBetween(1,5),
            'number_of_men' => $men,
            'number_of_women' => $female,
            'dinner' => fake()->boolean(),
            'breakfast' => fake()->boolean(),
            'payment_info' => '現地で現金払い',
            'reservation_fee' => fake()->numberBetween(10000,200000),
            'remarks_column' => fake()->realText(100),
            'payment_status' => '0',
        ];
    }
}
