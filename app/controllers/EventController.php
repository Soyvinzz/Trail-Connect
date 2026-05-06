<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/EventModel.php';

final class EventController
{
    private EventModel $events;

    public function __construct()
    {
        $this->events = new EventModel();
    }

    public function handle(string $action): bool
    {
        return match ($action) {
            'publish_event' => $this->publishEvent(),
            'delete_event' => $this->deleteEvent(),
            'join_event' => $this->joinEvent(),
            'request_status' => $this->requestStatus(),
            'delete_request' => $this->deleteRequest(),
            'save_update' => $this->saveUpdate(),
            'delete_update' => $this->deleteUpdate(),
            default => false,
        };
    }

    private function publishEvent(): bool
    {
        if (tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $trail = trim((string) ($_POST['trail'] ?? ''));
        if ($title === '' || $trail === '') {
            header('Location: index.php?page=create_event&error=event_required');
            exit;
        }
        $existing = $eventId > 0 ? $this->events->findById($eventId) : null;
        $defaultOrganizer = is_array($existing) && ($existing['organizer'] ?? '') !== ''
            ? (string) $existing['organizer']
            : (tc_current_user_id() > 0 ? tc_display_name() : 'TrailConnect Organizer');
        $organizerUserId = null;
        if ($eventId > 0 && is_array($existing)) {
            $existingUid = $existing['organizer_user_id'] ?? null;
            $organizerUserId = $existingUid !== null && $existingUid !== ''
                ? max(1, (int) $existingUid) : (tc_current_user_id() > 0 ? tc_current_user_id() : null);
        } else {
            $organizerUserId = tc_current_user_id() > 0 ? tc_current_user_id() : null;
        }
        $savedId = $this->events->save([
            'id' => $eventId > 0 ? $eventId : null,
            'title' => $title,
            'trail' => $trail,
            'date' => (string) ($_POST['date'] ?? ''),
            'time' => (string) ($_POST['time'] ?? '08:00'),
            'difficulty' => (string) ($_POST['difficulty'] ?? 'mod'),
            'min_hiking_level' => (string) ($_POST['min_hiking_level'] ?? ''),
            'min_minor_hikes' => max(0, (int) ($_POST['min_minor_hikes'] ?? 0)),
            'min_major_hikes' => max(0, (int) ($_POST['min_major_hikes'] ?? 0)),
            'meet' => trim((string) ($_POST['meet'] ?? '')),
            'max' => (int) ($_POST['max'] ?? 12),
            'desc' => trim((string) ($_POST['desc'] ?? '')),
            'approval' => (string) ($_POST['approval'] ?? 'manual'),
            'organizer' => $defaultOrganizer,
            'organizer_user_id' => $organizerUserId,
            'status' => 'published',
        ]);
        $msg = $eventId > 0 ? 'event_updated' : 'event_created';
        header('Location: index.php?page=my_event&msg=' . $msg . '&event_id=' . $savedId);
        exit;
    }

    private function deleteEvent(): bool
    {
        if (tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $event = $eventId > 0 ? $this->events->findById($eventId) : null;
        if ($eventId > 0 && !tc_event_manageable_by_current_organizer($event)) {
            header('Location: index.php?page=dashboard');
            exit;
        }
        if ($eventId > 0) {
            $this->events->delete($eventId);
        }
        header('Location: index.php?page=my_event&msg=event_deleted');
        exit;
    }

    private function joinEvent(): bool
    {
        if (tc_role() !== 'hiker') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $returnTo = (string) ($_POST['return_to'] ?? 'index.php?page=my_event');
        if ($returnTo === '' || strpos($returnTo, 'index.php') !== 0) {
            $returnTo = 'index.php?page=my_event';
        }
        if ($eventId > 0) {
            $name = tc_display_name();
            $uid = tc_current_user_id();
            $event = $this->events->findById($eventId);
            if (is_array($event)) {
                if (($event['status'] ?? 'published') !== 'published') {
                    $separator = strpos($returnTo, '?') !== false ? '&' : '?';
                    header('Location: ' . $returnTo . $separator . 'msg=join_requirement_failed&reason=not_published');
                    exit;
                }
                $profile = tc_profile();
                $eligibility = tc_hiker_meets_event_requirements($event, $profile);
                if (empty($eligibility['ok'])) {
                    $separator = strpos($returnTo, '?') !== false ? '&' : '?';
                    header('Location: ' . $returnTo . $separator . 'msg=join_requirement_failed&reason=' . urlencode((string) ($eligibility['reason'] ?? 'requirements')));
                    exit;
                }
            }
            $existingRequest = null;
            foreach ($this->events->allJoinRequests() as $request) {
                $sameEvent = (int) $request['event_id'] === $eventId;
                $sameByName = $sameEvent && (string) $request['hiker_name'] === $name;
                $sameByUser = $sameEvent && $uid > 0 && isset($request['user_id']) && (int) $request['user_id'] === $uid;
                if ($sameByName || $sameByUser) {
                    $existingRequest = $request;
                    break;
                }
            }
            if ($existingRequest !== null) {
                $existingRequest['status'] = 'pending';
                $existingRequest['requested_at'] = date('Y-m-d H:i:s');
                $this->events->saveJoinRequest($existingRequest);
            } else {
                $this->events->saveJoinRequest([
                    'event_id' => $eventId,
                    'hiker_name' => $name,
                    'status' => 'pending',
                    'requested_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if (is_array($event)) {
                $ownerId = (int) ($event['organizer_user_id'] ?? 0);
                if ($ownerId > 0) {
                    $et = (string) ($event['title'] ?? 'your hike');
                    tc_push_notice_for_user($ownerId, $name . ' requested to join "' . $et . '".', 'info');
                }
            }
        }
        $separator = strpos($returnTo, '?') !== false ? '&' : '?';
        header('Location: ' . $returnTo . $separator . 'msg=join_request_submitted');
        exit;
    }

    private function requestStatus(): bool
    {
        if (tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $requestId = (int) ($_POST['request_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? 'pending');
        $requests = $this->events->allJoinRequests();
        if ($requestId > 0 && isset($requests[$requestId])) {
            $item = $requests[$requestId];
            $event = $this->events->findById((int) ($item['event_id'] ?? 0));
            if (!tc_event_manageable_by_current_organizer($event)) {
                header('Location: index.php?page=my_event');
                exit;
            }
            $item['status'] = in_array($status, ['approved', 'declined', 'pending'], true) ? $status : 'pending';
            $this->events->saveJoinRequest($item);
            $eventTitle = (string) ($event['title'] ?? 'your event');
            $hikerName = (string) ($item['hiker_name'] ?? 'A joiner');
            $actorId = tc_current_user_id();
            $hikerUserId = (int) ($item['user_id'] ?? 0);
            if ($item['status'] === 'approved') {
                if ($actorId > 0) {
                    tc_push_notice_for_user($actorId, $hikerName . ' was approved for "' . $eventTitle . '".', 'success');
                }
                if ($hikerUserId > 0) {
                    tc_push_notice_for_user($hikerUserId, 'Your join request for "' . $eventTitle . '" was approved.', 'success');
                }
            } elseif ($item['status'] === 'declined') {
                if ($actorId > 0) {
                    tc_push_notice_for_user($actorId, $hikerName . ' was declined for "' . $eventTitle . '".', 'warning');
                }
                if ($hikerUserId > 0) {
                    tc_push_notice_for_user($hikerUserId, 'Your join request for "' . $eventTitle . '" was declined.', 'warning');
                }
            }
        }
        header('Location: index.php?page=my_event&msg=request_status_updated');
        exit;
    }

    private function deleteRequest(): bool
    {
        $requestId = (int) ($_POST['request_id'] ?? 0);
        if ($requestId > 0 && tc_role() === 'organizer') {
            $request = $this->events->allJoinRequests()[$requestId] ?? null;
            $eventRef = is_array($request) ? $this->events->findById((int) ($request['event_id'] ?? 0)) : null;
            if (!tc_event_manageable_by_current_organizer($eventRef)) {
                header('Location: index.php?page=my_event');
                exit;
            }
            $this->events->deleteJoinRequest($requestId);
            if (is_array($request)) {
                $event = $this->events->findById((int) ($request['event_id'] ?? 0));
                $eventTitle = (string) ($event['title'] ?? 'your event');
                $hikerName = (string) ($request['hiker_name'] ?? 'A joiner');
                $actorId = tc_current_user_id();
                if ($actorId > 0) {
                    tc_push_notice_for_user($actorId, $hikerName . ' request was removed from "' . $eventTitle . '".', 'info');
                }
                $hikerUserId = (int) ($request['user_id'] ?? 0);
                if ($hikerUserId > 0) {
                    tc_push_notice_for_user($hikerUserId, 'Your request for "' . $eventTitle . '" was removed by the organizer.', 'warning');
                }
            }
        }
        if ($requestId > 0 && tc_role() === 'hiker') {
            $request = $this->events->allJoinRequests()[$requestId] ?? null;
            if (is_array($request) && (string) ($request['hiker_name'] ?? '') === tc_display_name()) {
                $this->events->deleteJoinRequest($requestId);
            }
        }
        header('Location: index.php?page=my_event&msg=join_request_deleted');
        exit;
    }

    private function saveUpdate(): bool
    {
        if (tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $updateId = (int) ($_POST['update_id'] ?? 0);
        $message = trim((string) ($_POST['message'] ?? ''));
        $eventId = (int) ($_POST['event_id'] ?? 0);
        if ($message === '' || $eventId <= 0) {
            header('Location: index.php?page=updates&error=update_required');
            exit;
        }
        $event = $this->events->findById($eventId);
        if (!tc_event_manageable_by_current_organizer($event)) {
            header('Location: index.php?page=updates');
            exit;
        }
        $this->events->saveUpdate([
            'id' => $updateId > 0 ? $updateId : null,
            'event_id' => $eventId,
            'type' => trim((string) ($_POST['type'] ?? 'General')),
            'message' => $message,
            'posted_at' => date('Y-m-d H:i:s'),
        ]);
        $msg = $updateId > 0 ? 'update_saved' : 'update_published';
        header('Location: index.php?page=updates&msg=' . $msg);
        exit;
    }

    private function deleteUpdate(): bool
    {
        if (tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $updateId = (int) ($_POST['update_id'] ?? 0);
        if ($updateId > 0) {
            $update = $this->events->allUpdates()[$updateId] ?? null;
            $event = is_array($update) ? $this->events->findById((int) ($update['event_id'] ?? 0)) : null;
            if (!tc_event_manageable_by_current_organizer($event)) {
                header('Location: index.php?page=updates');
                exit;
            }
            $this->events->deleteUpdate($updateId);
        }
        header('Location: index.php?page=updates&msg=update_deleted');
        exit;
    }
}
