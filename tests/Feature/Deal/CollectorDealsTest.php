<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;

function createCollectorAssignedDeal(User $collector, string $suffix, string $paymentStatus = 'partial_payment'): Deal
{
    $lead = Lead::create([
        'full_name' => 'Collector Customer '.$suffix,
        'phone' => '0912345'.$suffix,
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $project = Project::create([
        'project_name' => 'Collector Project '.$suffix,
        'project_manager' => 'Project Manager',
        'location_address' => 'Test Address',
        'total_floors' => 10,
        'completed_floors' => 2,
        'total_units' => 20,
        'due_date' => now()->addYear()->toDateString(),
    ]);

    $unit = Unit::create([
        'project_id' => $project->id,
        'unit_number' => 'UNIT-'.$suffix,
        'floor' => 1,
        'unit_type' => 'apartment',
        'size' => 80,
        'price' => 1000000,
        'status' => 'reserved',
    ]);

    return Deal::create([
        'lead_id' => $lead->id,
        'project_id' => $project->id,
        'unit_id' => $unit->id,
        'collector_id' => $collector->id,
        'deal_amount' => 1000000,
        'down_payment' => 100000,
        'payment_status' => $paymentStatus,
        'payment_cycle' => 'monthly',
        'number_of_installments' => 12,
        'installment_amount' => 75000,
        'start_date' => now()->toDateString(),
        'commission_type' => 'percentage',
        'commission_value' => 2.5,
        'beneficiary' => 'internal_agent',
        'commission_trigger' => 'each_payment',
    ]);
}

it('shows only deals assigned to the signed-in collector', function () {
    $collector = User::factory()->create(['role' => 'collector']);
    $otherCollector = User::factory()->create(['role' => 'collector']);

    $assignedDeal = createCollectorAssignedDeal($collector, '101');
    $otherDeal = createCollectorAssignedDeal($otherCollector, '102');

    $response = $this->actingAs($collector)->get(route('collector.deals'));

    $response->assertOk();
    $response->assertViewIs('collector.deals');
    $response->assertViewHas('deals', fn ($deals) => $deals->getCollection()->modelKeys() === [$assignedDeal->id]);
    $response->assertSee('Collector Customer 101');
    $response->assertSee('Collector Project 101');
    $response->assertSee('Partial payment');
    $response->assertDontSee('Collector Customer 102');
});

it('does not allow non-collectors to open the collector deals page', function () {
    $agent = User::factory()->create(['role' => 'agent']);

    $this->actingAs($agent)
        ->get(route('collector.deals'))
        ->assertForbidden();
});

it('filters assigned deals by payment status and preserves the filter in pagination', function () {
    $collector = User::factory()->create(['role' => 'collector']);

    for ($index = 0; $index < 11; $index++) {
        createCollectorAssignedDeal($collector, '20'.$index, 'pending');
    }

    createCollectorAssignedDeal($collector, '299', 'fully_paid');

    $response = $this->actingAs($collector)->get(route('collector.deals', [
        'payment_status' => 'pending',
    ]));

    $response->assertOk();
    $response->assertViewHas('deals', fn ($deals) => $deals->total() === 11);
    $response->assertSee('Collector Customer 200');
    $response->assertDontSee('Collector Customer 299');
    $response->assertSee('onchange="this.form.requestSubmit()"', false);
    $response->assertSee('payment_status=pending', false);
});

it('redirects collectors to their deals page after login', function () {
    $collector = User::factory()->create([
        'role' => 'collector',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'email' => $collector->email,
        'password' => 'password',
    ])->assertRedirect(route('collector.deals'));
});
