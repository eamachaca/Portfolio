<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Translatable string columns by table. Spatie HasTranslations stores
     * values as {"en": "...", "es": "..."} in the same column. Plain
     * strings are wrapped as {"en": "<value>"} so the front keeps
     * rendering until a translation is supplied.
     */
    private const STRING_FIELDS = [
        'users' => ['headline', 'bio'],
        'experiences' => ['summary'],
        'projects' => ['title', 'excerpt', 'description'],
        'studies' => ['title', 'description'],
        'services' => ['title', 'description'],
        'faqs' => ['question', 'answer'],
        'posts' => ['title', 'excerpt', 'content'],
    ];

    /**
     * MySQL VARCHAR(255) columns that need bumping to TEXT — once a row
     * has two or three locales the JSON overhead overflows 255 chars
     * easily. SQLite stores all text as TEXT so it is a no-op there.
     */
    private const STRING_COLUMNS_TO_BUMP = [
        'users' => ['headline'],
        'projects' => ['title', 'excerpt'],
        'studies' => ['title'],
        'services' => ['title'],
        'faqs' => ['question'],
        'posts' => ['title', 'excerpt'],
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (self::STRING_COLUMNS_TO_BUMP as $table => $cols) {
                foreach ($cols as $col) {
                    DB::statement("ALTER TABLE {$table} MODIFY COLUMN {$col} TEXT NULL");
                }
            }
        }

        foreach (self::STRING_FIELDS as $table => $fields) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $fields) {
                $update = [];
                foreach ($fields as $field) {
                    $update[$field] = self::wrap($row->{$field} ?? null);
                }
                DB::table($table)->where('id', $row->id)->update($update);
            });
        }

        DB::table('experiences')->orderBy('id')->each(function ($row) {
            $levels = json_decode($row->levels ?? 'null', true);
            if (! is_array($levels)) {
                return;
            }
            $updated = array_map(function ($level) {
                if (isset($level['description']) && is_string($level['description'])) {
                    $level['description'] = ['en' => $level['description']];
                }
                if (isset($level['highlights']) && is_array($level['highlights'])) {
                    $level['highlights'] = array_map(
                        fn ($h) => is_string($h) ? ['en' => $h] : $h,
                        $level['highlights'],
                    );
                }

                return $level;
            }, $levels);

            DB::table('experiences')->where('id', $row->id)->update([
                'levels' => json_encode($updated, JSON_UNESCAPED_UNICODE),
            ]);
        });

        DB::table('projects')->orderBy('id')->each(function ($row) {
            $apps = json_decode($row->apps ?? 'null', true);
            if (! is_array($apps)) {
                return;
            }
            $updated = array_map(function ($app) {
                if (isset($app['description']) && is_string($app['description'])) {
                    $app['description'] = ['en' => $app['description']];
                }

                return $app;
            }, $apps);

            DB::table('projects')->where('id', $row->id)->update([
                'apps' => json_encode($updated, JSON_UNESCAPED_UNICODE),
            ]);
        });
    }

    public function down(): void
    {
        foreach (self::STRING_FIELDS as $table => $fields) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $fields) {
                $update = [];
                foreach ($fields as $field) {
                    $update[$field] = self::unwrap($row->{$field} ?? null);
                }
                DB::table($table)->where('id', $row->id)->update($update);
            });
        }

        DB::table('experiences')->orderBy('id')->each(function ($row) {
            $levels = json_decode($row->levels ?? 'null', true);
            if (! is_array($levels)) {
                return;
            }
            $updated = array_map(function ($level) {
                if (isset($level['description']) && is_array($level['description'])) {
                    $level['description'] = $level['description']['en'] ?? reset($level['description']) ?: '';
                }
                if (isset($level['highlights']) && is_array($level['highlights'])) {
                    $level['highlights'] = array_map(
                        fn ($h) => is_array($h) ? ($h['en'] ?? reset($h) ?: '') : $h,
                        $level['highlights'],
                    );
                }

                return $level;
            }, $levels);

            DB::table('experiences')->where('id', $row->id)->update([
                'levels' => json_encode($updated, JSON_UNESCAPED_UNICODE),
            ]);
        });

        DB::table('projects')->orderBy('id')->each(function ($row) {
            $apps = json_decode($row->apps ?? 'null', true);
            if (! is_array($apps)) {
                return;
            }
            $updated = array_map(function ($app) {
                if (isset($app['description']) && is_array($app['description'])) {
                    $app['description'] = $app['description']['en'] ?? reset($app['description']) ?: '';
                }

                return $app;
            }, $apps);

            DB::table('projects')->where('id', $row->id)->update([
                'apps' => json_encode($updated, JSON_UNESCAPED_UNICODE),
            ]);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (self::STRING_COLUMNS_TO_BUMP as $table => $cols) {
                foreach ($cols as $col) {
                    DB::statement("ALTER TABLE {$table} MODIFY COLUMN {$col} VARCHAR(255) NULL");
                }
            }
        }
    }

    /**
     * Wrap a plain string as {"en": "<value>"}. Idempotent.
     */
    private static function wrap(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        $decoded = json_decode($value, true);
        if (is_array($decoded) && $decoded !== [] && self::isAssoc($decoded)) {
            return $value;
        }

        return json_encode(['en' => $value], JSON_UNESCAPED_UNICODE);
    }

    private static function unwrap(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        $decoded = json_decode($value, true);
        if (! is_array($decoded)) {
            return $value;
        }

        return $decoded['en'] ?? (reset($decoded) ?: null);
    }

    private static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
};
