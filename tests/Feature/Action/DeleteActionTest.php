<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;


it('allows an admin to delete an action', function () {

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($admin)->delete(
        route('deleteAction', $action)
    );

    $response->assertRedirect();

    $response->assertSessionHas(
        'success',
        'Action deleted successfully.'
    );

    $this->assertDatabaseMissing('actions', [
        'id' => $action->id,
    ]);
});


it('prevents a non-admin from deleting an action', function () {

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($agent)->delete(
        route('deleteAction', $action)
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
    ]);
});


it('prevents an unauthenticated user from deleting an action', function () {

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->delete(
        route('deleteAction', $action)
    );

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
    ]);
});


it('returns not found when deleting a nonexistent action', function () {

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->delete(
        route('deleteAction', 999999)
    );

    $response->assertNotFound();
});