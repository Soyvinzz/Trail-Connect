<?php
declare(strict_types=1);

final class EventModel
{
    public function findById(int $eventId): ?array
    {
        return tc_find_event($eventId);
    }

    public function save(array $event): int
    {
        return tc_save_event($event);
    }

    public function delete(int $eventId): void
    {
        tc_delete_event($eventId);
    }

    public function allJoinRequests(): array
    {
        return tc_join_requests();
    }

    public function saveJoinRequest(array $request): void
    {
        tc_save_join_request($request);
    }

    public function deleteJoinRequest(int $requestId): void
    {
        tc_delete_join_request($requestId);
    }

    public function allUpdates(): array
    {
        return tc_updates();
    }

    public function saveUpdate(array $update): void
    {
        tc_save_update($update);
    }

    public function deleteUpdate(int $updateId): void
    {
        tc_delete_update($updateId);
    }
}
