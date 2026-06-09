<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        $byCategory = [
            'Languages' => ['PHP', 'SQL', 'HTML5', 'JavaScript', 'TypeScript', 'Java', 'C#', 'Ruby', 'Dart'],
            'Frameworks' => ['Laravel', 'Filament', 'Ruby on Rails', 'React Native', 'Flutter', '.NET Framework', 'Xamarin', 'Spring'],
            'Databases' => ['MySQL', 'PostgreSQL', 'SQL Server', 'SingleStore', 'SQLite', 'Redis'],
            'Tools' => ['Git', 'GitLab', 'Jira', 'ClickUp', 'SAP B1 Service Layer', 'Android Studio', 'Composer', 'npm'],
            'Cloud' => ['AWS', 'EC2', 'S3', 'Vapor', 'Forge'],
            'Methodology' => ['Scrum', 'Kanban', 'Extreme Programming'],
        ];

        $sort = 0;
        foreach ($byCategory as $category => $names) {
            foreach ($names as $name) {
                $sort++;
                Skill::updateOrCreate(
                    ['owner_id' => $owner->id, 'name' => $name],
                    ['category' => $category, 'sort_order' => $sort],
                );
            }
        }
    }
}
