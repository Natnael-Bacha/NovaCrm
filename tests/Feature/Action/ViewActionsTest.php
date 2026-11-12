<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

function createActionForAgent(User $agent, string $phone): Action
{
    $lead = Lead::create([
        'full_name' => 'Lead '.$phone,
        'phone' => $phone,
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
        'agent_id' => $agent->id,
    ]);

    return Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $agent->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
    ]);
}

it('shows supervisors only actions assigned to their own agents', function () {
    $supervisor = User::factory()->create(['role' => 'supervisor']);
    $agent = User::factory()->create([
        'role' => 'agent',
        'supervisor_id' => $supervisor->id,
    ]);
    $otherAgent = User::factory()->create([
        'role' => 'agent',
        'supervisor_id' => User::factory()->create(['role' => 'supervisor'])->id,
    ]);

    $teamAction = createActionForAgent($agent, '0912345001');
    $otherTeamAction = createActionForAgent($otherAgent, '0912345002');

    $response = $this->actingAs($supervisor)->get(route('supervisor.actions'));

    $response->assertOk();
    $response->assertViewIs('supervisor.actions');
    $response->assertViewHas('actions', fn ($actions) => $actions->getCollection()->modelKeys() === [$teamAction->id]);
    $response->assertSee(route('supervisor.actions'));
    $response->assertSee('Actions');
    $response->assertSee($teamAction->lead->full_name);
    $response->assertDontSee($otherTeamAction->lead->full_name);
});

it('keeps all actions visible to admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $firstAgent = User::factory()->create(['role' => 'agent']);
    $secondAgent = User::factory()->create(['role' => 'agent']);

    createActionForAgent($firstAgent, '0912345003');
    createActionForAgent($secondAgent, '0912345004');

    $response = $this->actingAs($admin)->get(route('admin.actions'));

    $response->assertOk();
    $response->assertViewHas('actions', fn ($actions) => $actions->total() === 2);
});
