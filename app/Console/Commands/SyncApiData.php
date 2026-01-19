<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Vehicle;
use App\Models\Party;
use App\Models\Cases;
use App\Models\Document;
use App\Models\Inspection;
use App\Models\SystemUser;

class SyncApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulling and syncing data from external API';

    
   public function handle()
    {
        $this->info(' Starting API sync...');

        try {
            
            $response = Http::timeout(30)->get('https://deskplan.lv/muita/app.json');
            
            if (!$response->successful()) {
                $this->error('Failed to fetch API data: ' . $response->status());
                return Command::FAILURE;
            }

            $data = $response->json();
            
            
            $this->syncVehicles($data['vehicles'] ?? []);
            $this->syncParties($data['parties'] ?? []);
            $this->syncCases($data['cases'] ?? []);
            $this->syncUsers($data['users'] ?? []);
            $this->syncDocuments($data['documents'] ?? []);
            $this->syncInspections($data['inspections'] ?? []);
            
            
            $this->newLine();
            $this->info(' Sync completed successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error(' Sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function syncVehicles(array $vehicles): void
    {
        $count = 0;
        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(
                ['id' => $vehicle['id']],
                [
                    'plate_no' => $vehicle['plate_no'] ?? null,
                    'country'  => $vehicle['country'] ?? null,
                    'make'     => $vehicle['make'] ?? null,
                    'model'    => $vehicle['model'] ?? null,
                    'vin'      => $vehicle['vin'] ?? null,
                ]
            );
            $count++;
        }
        $this->info(" Vehicles: {$count} synced");
    }

    private function syncParties(array $parties): void
    {
        $count = 0;
        foreach ($parties as $party) {
            Party::updateOrCreate(
                ['id' => $party['id']],
                [
                    'name' => $party['name'] ?? null,
                    'type' => $party['type'] ?? 'unknown',
                    'reg_code' => $party['reg_code'] ?? null,  
                    'country' => $party['country'] ?? null,
                    'email' => $party['email'] ?? null,      
                    'phone' => $party['phone'] ?? null,       
                    'vat' => $party['vat'] ?? null,           
]
            );
            $count++;
        }
        $this->info(" Parties: {$count} synced");
    }

    private function syncCases(array $cases): void
    {
        $count = 0;
        foreach ($cases as $case) {
            Cases::updateOrCreate(
                ['id' => $case['id']],
                [
                    'external_ref' => $case['external_ref'] ?? null,
                    'status' => in_array($case['status'] ?? '', ['new','screening','in_inspection','on_hold','released','closed']) ? $case['status'] : 'new',
                    'priority' => $case['priority'] ?? 'normal',
                    'arrival_ts' => $case['arrival_ts'] ?? null,
                    'checkpoint_id' => $case['checkpoint_id'] ?? null,
                    'origin_country' => $case['origin_country'] ?? null,
                    'destination_country' => $case['destination_country'] ?? null,
                    'risk_flags' => $case['risk_flags'] ?? [],
                    'vehicle_id' => $case['vehicle_id'] ?? null,
                    'declarant_id' => $case['declarant_id'] ?? null,
                    'consignee_id' => $case['consignee_id'] ?? null,
                ]
            );
            $count++;
        }
        $this->info(" Cases: {$count} synced");
    }

   private function syncUsers(array $users): void
{
    $count = 0;
    foreach ($users as $user) {
        \App\Models\SystemUser::updateOrCreate(
            ['id' => $user['id']], 
            [
                'username' => $user['username'] ?? null,
                'full_name' => $user['full_name'] ?? null,
                'role' => $user['role'] ?? 'broker',
                'active' => $user['active'] ?? true,
            ]
        );
        $count++;
    }
    $this->info(" Users: {$count} synced");
}

    private function syncDocuments(array $documents): void
    {
        $count = 0;
        foreach ($documents as $document) {
            Document::updateOrCreate(
                ['id' => $document['id']],
                [
                    'case_id' => $document['case_id'] ?? null,
                    'filename' => $document['filename'] ?? null,
                    'mime_type' => $document['mime_type'] ?? null,
                    'category' => $document['category'] ?? null,
                    'pages' => $document['pages'] ?? null,
                    'uploaded_by' => $document['uploaded_by'] ?? null,
                ]   
            );
            $count++;
        }
        $this->info(" Documents: {$count} synced");
    }

    private function syncInspections(array $inspections): void
    {
        $count = 0;
        foreach ($inspections as $inspection) {
        
            $result = null;
            if (!empty($inspection['checks']) && is_array($inspection['checks'])) {
                
                $result = $inspection['checks'][0]['result'] ?? null;
                
                $checksData = json_encode($inspection['checks']);
            } else {
                $checksData = null;
            }
            
            
            $inspectionId = $inspection['id'] ?? null;
            if (empty($inspectionId)) {
                $inspectionId = 'insp-' . ($inspection['case_id'] ?? 'unknown') . '-' . time() . '-' . $count;
            }
            
            Inspection::updateOrCreate(
                ['id' => $inspectionId],
                [
                    'case_id' => $inspection['case_id'] ?? null,
                    'type' => $inspection['type'] ?? null,
                    'result' => $result,
                    'checks' => $checksData, 
                    'started_at' => $inspection['start_ts'] ?? null,
                    'assigned_to' => $inspection['assigned_to'] ?? null,
                    'location' => $inspection['location'] ?? null,
                ]
            );
            $count++;
        }
        $this->info(" Inspections: {$count} synced");
    }
}
