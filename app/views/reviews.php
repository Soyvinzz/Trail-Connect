<?php
declare(strict_types=1);
$pageTitle = 'Reviews — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Reviews</h1>
        <p class="page-lede">Thank organizers and co-hikers after <strong>Negros Occidental</strong> outings — ratings build trust for Patag, DSB, and coastal groups.</p>
    </header>

    <section class="card card--stack glass-stack review-form">
        <h2 class="section-title">Write a review</h2>
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="reviews">

            <label class="field-label" for="rev-who">Review recipient</label>
            <select id="rev-who" class="input input--select" name="recipient">
                <option value="">Select person or group…</option>
                <option>Mara Villanueva (organizer)</option>
                <option>Ian Concepcion (organizer)</option>
                <option>Rico Magbanua (organizer)</option>
                <option>Victorias Trail Club</option>
                <option>Co-hiker — Alex Reyes</option>
            </select>

            <label class="field-label" for="rev-event">Related event <span class="text-muted">(optional)</span></label>
            <select id="rev-event" class="input input--select" name="event">
                <option>Patag plateau loop — Apr 12, 2026</option>
                <option>Mambukal falls trail — Apr 20, 2026</option>
                <option>DSB pine ridge — Apr 18, 2026</option>
                <option>Gawahon eco loop — past</option>
            </select>

            <span class="field-label">Rating</span>
            <div class="star-picker" role="group" aria-label="Star rating">
                <label class="star-picker__opt"><input type="radio" name="stars" value="5" checked> ★★★★★ Excellent</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="4"> ★★★★☆ Very good</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="3"> ★★★☆☆ Okay</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="2"> ★★☆☆☆ Needs improvement</label>
            </div>

            <label class="field-label" for="rev-text">Your review</label>
            <textarea id="rev-text" class="input" name="text" rows="4" placeholder="Clear communication, safe pacing on the Patag saddle, and great local knowledge — thank you!"></textarea>

            <button type="button" class="btn-primary">Submit review</button>
        </form>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Recent feedback</h2>
        <article class="review-block">
            <div class="review-block__head">
                <span><strong>You</strong> → Mara Villanueva</span>
                <span class="stars">★★★★★</span>
                <time datetime="2026-03-20">Mar 20, 2026</time>
            </div>
            <p class="feed-item__body" style="margin:0">Fog rolled in fast; Mara called a sensible early turnaround. Felt safe on the Patag loop.</p>
        </article>
        <article class="review-block">
            <div class="review-block__head">
                <span><strong>Ian Concepcion</strong> → You</span>
                <span class="stars">★★★★☆</span>
                <time datetime="2026-03-15">Mar 15, 2026</time>
            </div>
            <p class="feed-item__body" style="margin:0">Solid group member on Mambukal — on time, carried whistle, helped newer hikers at the slippery steps.</p>
        </article>
        <article class="review-block review-block--muted">
            <div class="review-block__head">
                <span><strong>You</strong> → Victorias Trail Club</span>
                <span class="stars">★★★★★</span>
                <time datetime="2026-03-08">Mar 8, 2026</time>
            </div>
            <p class="feed-item__body" style="margin:0">Gawahon eco loop was well paced for kids; clear briefing at the gate.</p>
        </article>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
