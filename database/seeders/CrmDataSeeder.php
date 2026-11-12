<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use RuntimeException;

class CrmDataSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = User::where('role', 'supervisor')->orderBy('id')->get();
        $agents = User::where('role', 'agent')->orderBy('id')->get();
        $collectors = User::where('role', 'collector')->orderBy('id')->get();

        if ($supervisors->isEmpty() || $agents->isEmpty() || $collectors->isEmpty()) {
            throw new RuntimeException('CRM sample data requires existing supervisors, agents, and collectors.');
        }

        $projects = [
            [
                'project_name' => 'Sunrise Heights',
                'location_address' => '450 Park Avenue, New York, NY 10022',
                'total_floors' => 24,
                'completed_floors' => 20,
                'total_units' => 146,
                'due_date' => '2027-06-30',
            ],
            [
                'project_name' => 'Green Valley Residences',
                'location_address' => '780 Oak Street, Austin, TX 78701',
                'total_floors' => 12,
                'completed_floors' => 9,
                'total_units' => 72,
                'due_date' => '2026-12-15',
            ],
            [
                'project_name' => 'Azure Business Center',
                'location_address' => '210 Commerce Drive, Chicago, IL 60601',
                'total_floors' => 20,
                'completed_floors' => 20,
                'total_units' => 118,
                'due_date' => '2027-05-01',
            ],
            [
                'project_name' => 'Zenith Plaza',
                'location_address' => '55 Hillcrest Road, Denver, CO 80202',
                'total_floors' => 10,
                'completed_floors' => 4,
                'total_units' => 80,
                'due_date' => '2028-03-20',
            ],
        ];

        $projectModels = collect($projects)->map(function ($data, $index) use ($supervisors) {
            $data['project_manager'] = $supervisors[$index % $supervisors->count()]->full_name;

            return Project::updateOrCreate(['project_name' => $data['project_name']], $data);
        });

        $unitTypes = ['apartment', 'apartment', 'studio', 'office_space', 'penthouse', 'duplex', 'commercial'];
        $unitPrices = [
            'apartment' => 4800000,
            'studio' => 3200000,
            'office_space' => 7200000,
            'penthouse' => 12500000,
            'duplex' => 9800000,
            'commercial' => 15000000,
        ];

        $allUnits = collect();

        foreach ($projectModels as $fixture => $project) {
            $floorsToSeed = min($project->total_floors, 8);
            for ($floor = 1; $floor <= $floorsToSeed; $floor++) {
                $type = $unitTypes[($floor + $fixture) % count($unitTypes)];
                $unitsOnFloor = ($floor === $floorsToSeed) ? 2 : 3;

                for ($i = 1; $i <= $unitsOnFloor; $i++) {
                    $suffix = strtoupper(substr(str_replace(' ', '', $project->project_name), 0, 3));
                    $size = rand(52, 210);
                    $allUnits->push(Unit::updateOrCreate(
                        ['project_id' => $project->id, 'unit_number' => sprintf('%s-%02d-%02d', $suffix, $floor, $i)],
                        [
                            'floor' => $floor,
                            'unit_type' => $type,
                            'size' => $size,
                            'price' => round($unitPrices[$type] * ($size / 100)),
                            'status' => 'available',
                        ]
                    ));
                }
            }
        }

        $leadData = [
            ['full_name' => 'James Carter', 'email' => 'james.carter@example.com', 'phone' => '(555) 010-1001', 'budget_range' => '$500K - $800K', 'preferred_location' => 'Downtown', 'lead_source' => 'website', 'lead_type' => 'buyer', 'current_stage' => 'new', 'agent' => 0],
            ['full_name' => 'Emily Johnson', 'email' => null, 'phone' => '(555) 010-1002', 'budget_range' => '$300K - $500K', 'preferred_location' => 'Riverside', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'contacted', 'agent' => 1],
            ['full_name' => 'William Davis', 'email' => 'william.davis@example.com', 'phone' => '(555) 010-1003', 'budget_range' => '$1.5M - $2M', 'preferred_location' => 'Uptown', 'lead_source' => 'social media', 'lead_type' => 'investor', 'current_stage' => 'qualified', 'agent' => 2],
            ['full_name' => 'Olivia Martinez', 'email' => 'olivia.martinez@example.com', 'phone' => '(555) 010-1004', 'budget_range' => '$800K - $1.2M', 'preferred_location' => 'West End', 'lead_source' => 'walk_in', 'lead_type' => 'buyer', 'current_stage' => 'site visit', 'agent' => 3],
            ['full_name' => 'Benjamin Wilson', 'email' => null, 'phone' => '(555) 010-1005', 'budget_range' => '$200K - $400K', 'preferred_location' => 'Lakeview', 'lead_source' => 'website', 'lead_type' => 'seller', 'current_stage' => 'lost', 'agent' => 4],
            ['full_name' => 'Sophia Brown', 'email' => 'sophia.brown@example.com', 'phone' => '(555) 010-1006', 'budget_range' => '$1M - $1.5M', 'preferred_location' => 'Downtown', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'proposal sent', 'agent' => 0],
            ['full_name' => 'Henry Taylor', 'email' => 'henry.taylor@example.com', 'phone' => '(555) 010-1007', 'budget_range' => '$400K - $600K', 'preferred_location' => 'Green Park', 'lead_source' => 'social media', 'lead_type' => 'Tenant', 'current_stage' => 'contacted', 'agent' => 1],
            ['full_name' => 'Charlotte Anderson', 'email' => null, 'phone' => '(555) 010-1008', 'budget_range' => '$2M - $3M', 'preferred_location' => 'Uptown', 'lead_source' => 'walk_in', 'lead_type' => 'investor', 'current_stage' => 'qualified', 'agent' => 2],
            ['full_name' => 'Alexander Thomas', 'email' => 'alexander.thomas@example.com', 'phone' => '(555) 010-1009', 'budget_range' => '$600K - $900K', 'preferred_location' => 'Riverside', 'lead_source' => 'website', 'lead_type' => 'buyer', 'current_stage' => 'initial payment', 'agent' => 3],
            ['full_name' => 'Amelia White', 'email' => null, 'phone' => '(555) 010-1010', 'budget_range' => '$200K - $300K', 'preferred_location' => 'Lakeview', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'new', 'agent' => 4],
            ['full_name' => 'Lucas Harris', 'email' => 'lucas.harris@example.com', 'phone' => '(555) 010-1011', 'budget_range' => '$1.2M - $1.6M', 'preferred_location' => 'Downtown', 'lead_source' => 'social media', 'lead_type' => 'investor', 'current_stage' => 'completed', 'agent' => 0],
            ['full_name' => 'Mia Clark', 'email' => 'mia.clark@example.com', 'phone' => '(555) 010-1012', 'budget_range' => '$500K - $700K', 'preferred_location' => 'West End', 'lead_source' => 'walk_in', 'lead_type' => 'buyer', 'current_stage' => 'site visit', 'agent' => 1],
            ['full_name' => 'Jackson Lewis', 'email' => null, 'phone' => '(555) 010-1013', 'budget_range' => '$800K - $1M', 'preferred_location' => 'Uptown', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'proposal sent', 'agent' => 2],
            ['full_name' => 'Harper Walker', 'email' => 'harper.walker@example.com', 'phone' => '(555) 010-1014', 'budget_range' => '$300K - $500K', 'preferred_location' => 'Riverside', 'lead_source' => 'website', 'lead_type' => 'buyer', 'current_stage' => 'contacted', 'agent' => 3],
            ['full_name' => 'Ethan Hall', 'email' => null, 'phone' => '(555) 010-1015', 'budget_range' => '$100K - $200K', 'preferred_location' => 'Lakeview', 'lead_source' => 'social media', 'lead_type' => 'Tenant', 'current_stage' => 'lost', 'agent' => 4],
            ['full_name' => 'Abigail Young', 'email' => 'abigail.young@example.com', 'phone' => '(555) 010-1016', 'budget_range' => '$1M - $1.4M', 'preferred_location' => 'Downtown', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'qualified', 'agent' => 0],
            ['full_name' => 'Sebastian King', 'email' => null, 'phone' => '(555) 010-1017', 'budget_range' => '$400K - $600K', 'preferred_location' => 'Green Park', 'lead_source' => 'walk_in', 'lead_type' => 'investor', 'current_stage' => 'new', 'agent' => 1],
            ['full_name' => 'Ella Wright', 'email' => 'ella.wright@example.com', 'phone' => '(555) 010-1018', 'budget_range' => '$1.5M - $2M', 'preferred_location' => 'Uptown', 'lead_source' => 'website', 'lead_type' => 'investor', 'current_stage' => 'completed', 'agent' => 2],
            ['full_name' => 'Owen Scott', 'email' => null, 'phone' => '(555) 010-1019', 'budget_range' => '$600K - $800K', 'preferred_location' => 'West End', 'lead_source' => 'social media', 'lead_type' => 'seller', 'current_stage' => 'site visit', 'agent' => 3],
            ['full_name' => 'Grace Green', 'email' => 'grace.green@example.com', 'phone' => '(555) 010-1020', 'budget_range' => '$200K - $400K', 'preferred_location' => 'Lakeview', 'lead_source' => 'referral', 'lead_type' => 'buyer', 'current_stage' => 'contacted', 'agent' => 4],
            ['full_name' => 'Daniel Baker', 'email' => null, 'phone' => '(555) 010-1021', 'budget_range' => '$800K - $1.2M', 'preferred_location' => 'Downtown', 'lead_source' => 'website', 'lead_type' => 'buyer', 'current_stage' => 'initial payment', 'agent' => 0],
            ['full_name' => 'Chloe Adams', 'email' => 'chloe.adams@example.com', 'phone' => '(555) 010-1022', 'budget_range' => '$1M - $1.3M', 'preferred_location' => 'Riverside', 'lead_source' => 'walk_in', 'lead_type' => 'investor', 'current_stage' => 'qualified', 'agent' => 1],
            ['full_name' => 'Matthew Nelson', 'email' => null, 'phone' => '(555) 010-1023', 'budget_range' => '$400K - $600K', 'preferred_location' => 'Uptown', 'lead_source' => 'social media', 'lead_type' => 'buyer', 'current_stage' => 'proposal sent', 'agent' => 2],
            ['full_name' => 'Lily Carter', 'email' => 'lily.carter@example.com', 'phone' => '(555) 010-1024', 'budget_range' => '$300K - $500K', 'preferred_location' => 'West End', 'lead_source' => 'website', 'lead_type' => 'buyer', 'current_stage' => 'new', 'agent' => 3],
            ['full_name' => 'Noah Mitchell', 'email' => null, 'phone' => '(555) 010-1025', 'budget_range' => '$700K - $1M', 'preferred_location' => 'Downtown', 'lead_source' => 'referral', 'lead_type' => 'investor', 'current_stage' => 'site visit', 'agent' => 4],
        ];

        $leads = collect($leadData)->map(function ($data) use ($agents) {
            return Lead::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'budget_range' => $data['budget_range'],
                    'preferred_location' => $data['preferred_location'],
                    'lead_source' => $data['lead_source'],
                    'lead_type' => $data['lead_type'],
                    'current_stage' => $data['current_stage'],
                    'agent_id' => $agents[$data['agent'] % $agents->count()]->id,
                ]
            );
        });

        $dealableStageLeads = $leads->whereIn('current_stage', ['completed', 'initial payment'])->values();

        $dealConfigs = [
            ['cycle' => 'monthly', 'installments' => 24, 'down_payment' => 0.20, 'payment_status' => 'partial_payment'],
            ['cycle' => 'quarterly', 'installments' => 16, 'down_payment' => 0.25, 'payment_status' => 'partial_payment'],
            ['cycle' => 'semi_annually', 'installments' => 8, 'down_payment' => 0.30, 'payment_status' => 'pending'],
            ['cycle' => 'monthly', 'installments' => 36, 'down_payment' => 0.15, 'payment_status' => 'fully_paid'],
            ['cycle' => 'annually', 'installments' => 5, 'down_payment' => 0.30, 'payment_status' => 'partial_payment'],
        ];

        foreach ($dealableStageLeads as $i => $lead) {
            if ($i >= count($dealConfigs)) {
                break;
            }

            $project = $projectModels[$i % $projectModels->count()];
            $availableUnit = $allUnits->firstWhere(fn ($unit) => $unit->project_id === $project->id && $unit->status === 'available');

            if (! $availableUnit) {
                continue;
            }

            $config = $dealConfigs[$i];
            $dealAmount = $availableUnit->price;
            $downPayment = round($dealAmount * $config['down_payment']);
            $installmentAmount = round(($dealAmount - $downPayment) / $config['installments']);

            Deal::updateOrCreate(
                ['unit_id' => $availableUnit->id],
                [
                    'lead_id' => $lead->id,
                    'project_id' => $project->id,
                    'collector_id' => $collectors[$i % $collectors->count()]->id,
                    'deal_amount' => $dealAmount,
                    'down_payment' => $downPayment,
                    'payment_status' => $config['payment_status'],
                    'payment_cycle' => $config['cycle'],
                    'number_of_installments' => $config['installments'],
                    'installment_amount' => $installmentAmount,
                    'start_date' => Carbon::now()->subMonths(rand(1, 10)),
                    'commission_type' => $i % 2 === 0 ? 'percentage' : 'fixed_amount',
                    'commission_value' => $i % 2 === 0 ? 2.5 : 150000,
                    'beneficiary' => $i % 2 === 0 ? 'internal_agent' : 'external_agent',
                    'commission_trigger' => ['immediate', 'each_payment', 'full_payment'][$i % 3],
                ]
            );

            $availableUnit->update(['status' => $i % 2 === 0 ? 'sold' : 'reserved']);
        }

        $activityTypes = ['follow_up_call', 'meeting', 'property_visit', 'email'];
        $actionStatuses = ['done', 'on_progress'];
        $descriptions = [
            'Discussed current available units within the client budget range.',
            'Followed up on the proposal sent last week.',
            'Walked the client through the show unit on the 8th floor.',
            'Sent the updated payment plan breakdown.',
            'Confirmed the handover date and documentation requirements.',
            'Client requested more time to review the costing sheet.',
        ];

        foreach ($leads as $i => $lead) {
            if (in_array($lead->current_stage, ['lost'], true)) {
                continue;
            }

            $numActions = 1 + ($i % 3);
            for ($a = 0; $a < $numActions; $a++) {
                $activityType = $activityTypes[($i + $a) % count($activityTypes)];
                $description = $descriptions[($i + $a) % count($descriptions)];

                Action::updateOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'activity_type' => $activityType,
                        'description' => $description,
                    ],
                    [
                        'assigned_to' => $lead->agent_id,
                        'status' => $actionStatuses[($i + $a) % count($actionStatuses)],
                        'scheduled_time' => Carbon::now()->startOfDay()->addDays(($i + $a) % 15)->addHours(9 + $a),
                    ]
                );
            }
        }
    }
}
