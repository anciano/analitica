<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingGrdEgreso;
use App\Models\GrdEgresoFact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GrdUpsertFinalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'upserting']);
        ini_set('memory_limit', '1024M');

        DB::transaction(function () {
            StagingGrdEgreso::where('import_run_id', $this->importRun->id)
                ->where('is_valid', true)
                ->chunk(1000, function ($chunk) {
                    $toInsert = [];
                    foreach ($chunk as $row) {
                    $item = $row->payload_parsed;
                    // insert() doesn't auto-cast arrays to JSON strings, so we do it manually
                    $item['dx_secundarios'] = json_encode($item['dx_secundarios'] ?? []);
                    $item['proc_secundarios'] = json_encode($item['proc_secundarios'] ?? []);

                    $toInsert[] = array_merge($item, [
                        'import_run_id' => $this->importRun->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                    GrdEgresoFact::insert($toInsert);
                });

            $this->importRun->update([
                'status' => 'completed',
            ]);
        });
    }
}
