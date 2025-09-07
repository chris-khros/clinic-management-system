<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Require at least one user to satisfy created_by FK
        $userId = User::query()->value('id');
        if (!$userId) {
            return; // skip if no accounts present
        }

        $items = [
            [
                'title' => 'Flu Vaccination Drive',
                'content' => 'Get vaccinated this season. Walk-ins welcome every Friday 9am-4pm.',
                'visibility' => 'public',
                'is_active' => true,
            ],
            [
                'title' => 'New Cardiology Wing',
                'content' => 'Our new cardiology facilities are now open with state-of-the-art equipment.',
                'visibility' => 'public',
                'is_active' => true,
            ],
            [
                'title' => 'Holiday Schedule',
                'content' => 'Clinic will be closed on public holidays. Please plan your visits accordingly.',
                'visibility' => 'internal',
                'is_active' => false,
            ],
        ];

        foreach ($items as $data) {
            Announcement::create(array_merge($data, [
                'created_by' => $userId,
                'attachment' => null,
                'expires_at' => null,
            ]));
        }
    }
}

