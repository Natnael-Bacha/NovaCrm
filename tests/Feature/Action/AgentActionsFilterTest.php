<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

it('filters an agents actions by scheduled date, status, and customer', function () {
    $agent = User::factory()->create(['role' => 'agent']);
    $otherAgent = User::factory()->create(['role' => 'agent']);

    $customer = Lead::create([
        'full_name' => 'Matching Customer',
        'phone' => '0912345201',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);
    $anotherCustomer = Lead::create([
        'full_name' => 'Another Customer',
        'phone' => '0912345202',
        'lead_source' => 'referral',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);
    $otherAgentCustomer = Lead::create([
        'full_name' => 'Other Agent Customer',
        'phone' => '0912345203',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $matchingAction = Action::create([
        'lead_id' => $customer->id,
        'activity_type' => 'meeting',
        'assigned_to' => $agent->id,
        'status' => 'done',
        'scheduled_time' => '2026-11-12 10:00:00',
    ]);
    Action::create([
        'lead_id' => $customer->id,
        'activity_type' => 'meeting',
        'assigned_to' => $agent->id,
        'status' => 'on_progress',
        'scheduled_time' => '2026-11-12 11:00:00',
    ]);
    Action::create([
        'lead_id' => $anotherCustomer->id,
        'activity_type' => 'meeting',
        'assigned_to' => $agent->id,
        'status' => 'done',
        'scheduled_time' => '2026-11-12 12:00:00',
    ]);
    Action::create([
        'lead_id' => $otherAgentCustomer->id,
        'activity_type' => 'meeting',
        'assigned_to' => $otherAgent->id,
        'status' => 'done',
        'scheduled_time' => '2026-11-12 10:00:00',
    ]);

    $response = $this->actingAs($agent)->get(route('agent.actions', [
        'date' => '2026-11-12',
        'status' => 'done',
        'customer' => $customer->id,
    ]));

    $response->assertOk();
    $response->assertViewHas('actions', fn ($actions) => $actions->getCollection()->modelKeys() === [$matchingAction->id]);
    $response->assertViewHas('customers', fn ($customers) => $customers->pluck('id')->sort()->values()->all() === collect([$customer->id, $anotherCustomer->id])->sort()->values()->all());
    $response->assertSee('Matching Customer');
    $response->assertDontSee('Other Agent Customer');
    $response->assertSee('onchange="this.form.requestSubmit()"', false);
    $response->assertDontSee('>Filter</button>', false);
});

it('preserves filters when paginating agent actions', function () {
    $agent = User::factory()->create(['role' => 'agent']);
    $customer = Lead::create([
        'full_name' => 'Pagination Customer',
        'phone' => '0912345204',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    for ($index = 0; $index < 11; $index++) {
        Action::create([
            'lead_id' => $customer->id,
            'activity_type' => 'follow_up_call',
            'assigned_to' => $agent->id,
            'status' => 'on_progress',
            'scheduled_time' => '2026-11-12 '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).':00:00',
        ]);
    }

    $response = $this->actingAs($agent)->get(route('agent.actions', [
        'date' => '2026-11-12',
        'status' => 'on_progress',
        'customer' => $customer->id,
    ]));

    $response->assertOk();
    $response->assertViewHas('actions', fn ($actions) => $actions->total() === 11);
    $response->assertSee('date=2026-11-12', false);
    $response->assertSee('status=on_progress', false);
    $response->assertSee('customer='.$customer->id, false);
    $response->assertSee('page=2', false);
});
