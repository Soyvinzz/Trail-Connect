<?php
declare(strict_types=1);
$pageTitle = 'Create event — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Create event</h1>
        <p class="page-lede">Organizers only · List hikes around Bacolod &amp; Negros Occ. in <strong>three steps</strong> — basics, details, safety &amp; approvals.</p>
        <ol class="ce-steps" aria-label="Progress">
            <li class="ce-steps__item is-done">Basic info</li>
            <li class="ce-steps__item is-current" aria-current="step">Details</li>
            <li class="ce-steps__item">Management</li>
        </ol>
    </header>

    <form class="ce-form card card--stack glass-stack" method="post" action="index.php?page=create_event">
        <input type="hidden" name="action" value="publish_event">

        <fieldset class="ce-panel">
            <legend class="ce-panel__title">Step 1 — Basic info</legend>
            <label class="field-label" for="evt-title">Event title</label>
            <input id="evt-title" class="input" name="title" placeholder="Patag sunrise · Mambukal group hike" required>

            <label class="field-label" for="evt-trail">Trail (curated)</label>
            <select id="evt-trail" class="input input--select" name="trail" required>
                <option value="">Select trail…</option>
                <option>Patag plateau loop (Silay)</option>
                <option>Mambukal falls trail (Murcia)</option>
                <option>DSB pine ridge (Don Salvador Benedicto)</option>
                <option>Gawahon eco loop (Victorias)</option>
            </select>

            <div class="date-pair">
                <div>
                    <label class="field-label" for="evt-date">Date</label>
                    <input id="evt-date" class="input" type="date" name="date" required>
                </div>
                <div>
                    <label class="field-label" for="evt-time">Start time</label>
                    <input id="evt-time" class="input" type="time" name="time" value="08:00" required>
                </div>
            </div>
        </fieldset>

        <fieldset class="ce-panel">
            <legend class="ce-panel__title">Step 2 — Details</legend>
            <span class="field-label">Difficulty</span>
            <div class="chip-group chip-group--radio">
                <label class="chip"><input type="radio" name="difficulty" value="easy"> Easy</label>
                <label class="chip"><input type="radio" name="difficulty" value="mod" checked> Moderate</label>
                <label class="chip"><input type="radio" name="difficulty" value="hard"> Hard</label>
            </div>

            <label class="field-label" for="evt-meet">Meeting point</label>
            <input id="evt-meet" class="input" name="meet" placeholder="e.g. Mambukal resort entrance / Patag jump-off" required>

            <label class="field-label" for="evt-max">Participant maximum</label>
            <input id="evt-max" class="input" type="number" name="max" min="2" max="50" value="12" required>

            <label class="field-label" for="evt-desc">Description</label>
            <textarea id="evt-desc" class="input" name="desc" placeholder="Pace, regroup points, and what makes this Negros trail special." required></textarea>
        </fieldset>

        <fieldset class="ce-panel">
            <legend class="ce-panel__title">Step 3 — Management</legend>
            <span class="field-label">Approval type</span>
            <div class="chip-group chip-group--radio">
                <label class="chip" title="Join immediately if capacity allows."><input type="radio" name="approval" value="auto"> Auto-approve</label>
                <label class="chip" title="You approve each request."><input type="radio" name="approval" value="manual" checked> Manual review</label>
            </div>

            <span class="field-label">Required gear</span>
            <ul class="check-list check-list--form">
                <li><label><input type="checkbox" name="gear[]" value="boots" checked> Sturdy boots</label></li>
                <li><label><input type="checkbox" name="gear[]" value="water"> 2L water minimum</label></li>
                <li><label><input type="checkbox" name="gear[]" value="shell"> Rain shell</label></li>
                <li><label><input type="checkbox" name="gear[]" value="headlamp"> Headlamp</label></li>
            </ul>
            <label class="field-label" for="evt-gear-other">Other gear notes</label>
            <input id="evt-gear-other" class="input" name="gear_other" placeholder="Cap / sun protection for exposed Patag sections…">

            <label class="field-label" for="evt-safety">Safety notes</label>
            <textarea id="evt-safety" class="input" name="safety" placeholder="Fog, wind, red-clay slips after rain, turnaround rules…" required></textarea>

            <label class="field-label" for="evt-emergency">Emergency contact (organizer)</label>
            <input id="evt-emergency" class="input" name="emergency" placeholder="Name &amp; phone" required>
        </fieldset>

        <section class="publish-summary card card--inset">
            <h2 class="section-title">Pre-publish summary</h2>
            <p class="field-hint">Sample fields above — adjust for a real Mambukal, DSB, or Patag publish.</p>
            <ul class="detail-list detail-list--compact">
                <li>Trail + schedule in step 1</li>
                <li>Difficulty, meet point, cap, story in step 2</li>
                <li>Approval, gear, safety, emergency in step 3</li>
            </ul>
        </section>

        <div class="ce-bar">
            <button type="button" class="btn-secondary" onclick="history.back()">Previous</button>
            <button type="button" class="btn-secondary" onclick="window.scrollTo({top:0,behavior:'smooth'})">Next</button>
            <button class="btn-primary" type="submit">Publish event</button>
        </div>
    </form>
</div>
<?php include 'partials/footer.php'; ?>
