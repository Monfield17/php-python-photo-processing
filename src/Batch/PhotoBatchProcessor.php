<?php

namespace App\Batch;

use Dibi;
use App\Ai\PhotoAiRunner;

class PhotoBatchProcessor
{
    public static bool $FILTER_BY_RIDES = false;
    public static array $RIDE_IDS = [];

    public const BATCH_LIMIT = 20;

    public static function run(): void
    {
        $photos = self::loadPhotos();

        foreach ($photos as $photo) {
            try {
                $result = PhotoAiRunner::analyze($photo['file_path']);
                self::saveResults($photo, $result);

                Dibi::update('photos', [
                    'processed_ai' => true,
                ])
                    ->where('id = %i', $photo['id'])
                    ->execute();

                self::log("OK: Photo {$photo['id']} processed.");

            } catch (\Throwable $e) {
                self::log("ERROR: Photo {$photo['id']} – " . $e->getMessage());
            }
        }
    }

    private static function loadPhotos(): array
    {
        $query = Dibi::select('*')
            ->from('photos')
            ->where('processed_ai = %b', false);

        if (self::$FILTER_BY_RIDES && !empty(self::$RIDE_IDS)) {
            $rides = self::loadRides();

            foreach ($rides as $ride) {
                $start = date('Y-m-d', strtotime($ride['date'] . ' -1 month'));
                $end   = date('Y-m-d', strtotime($ride['date'] . ' +1 month'));

                $query->where(
                    '(section_id = %i AND photo_date BETWEEN %d AND %d)',
                    $ride['section_id'],
                    $start,
                    $end
                );
            }
        }

        return $query->limit(self::BATCH_LIMIT)->fetchAll();
    }

    private static function loadRides(): array
    {
        return Dibi::select('section_id, date')
            ->from('measurement_rides')
            ->where('id IN %in', self::$RIDE_IDS)
            ->fetchAll();
    }

    private static function saveResults(array $photo, array $result): void
    {
        foreach ($result['objects'] ?? [] as $obj) {
            Dibi::insert('photo_boxes', [
                'object_id' => $obj['id'] ?? null,
                'photo_id' => $photo['id'],
                'assigned_at' => new \DateTime(),
            ])->execute();
        }

        Dibi::insert('system_log', [
            'type' => 'ai_processing',
            'message' => "Photo {$photo['id']} processed by AI.",
            'created_at' => new \DateTime(),
        ])->execute();
    }

    private static function log(string $msg): void
    {
        echo $msg . PHP_EOL;
    }
}
