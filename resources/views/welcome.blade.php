<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Jobberwocky — Situations Vacant</title>
<style>
  :root {
    --paper: #f2ead9;
    --paper-line: #d9cba9;
    --ink: #2b2417;
    --ink-soft: #6b5f45;
    --stamp: #a13a2c;
    --stamp-soft: #c96a5a;
    --good: #3f6b45;
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    background: var(--paper);
    background-image:
      repeating-linear-gradient(transparent, transparent 27px, rgba(43,36,23,0.05) 28px);
    color: var(--ink);
    font-family: "Iowan Old Style", "Palatino Linotype", Palatino, Georgia, "Noto Serif", serif;
    line-height: 1.5;
  }

  .mono {
    font-family: ui-monospace, "SF Mono", "Cascadia Code", Consolas, monospace;
  }

  a { color: var(--stamp); }

  .wrap {
    max-width: 980px;
    margin: 0 auto;
    padding: 2.6rem 1.6rem 5rem;
  }

  header.masthead {
    text-align: center;
    border-bottom: 4px double var(--ink);
    padding-bottom: 1.1rem;
    margin-bottom: 0.4rem;
  }
  .masthead .kicker {
    font-family: ui-monospace, monospace;
    text-transform: uppercase;
    letter-spacing: 0.25em;
    font-size: 0.72rem;
    color: var(--ink-soft);
  }
  .masthead h1 {
    font-size: clamp(2.6rem, 7vw, 4.2rem);
    margin: 0.2rem 0 0.3rem;
    letter-spacing: 0.01em;
    text-wrap: balance;
  }
  .masthead .tagline {
    font-style: italic;
    color: var(--ink-soft);
    font-size: 1rem;
    margin: 0;
  }
  .edition-row {
    display: flex;
    justify-content: space-between;
    font-family: ui-monospace, monospace;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--ink-soft);
    margin-top: 0.6rem;
  }

  section { margin: 2.6rem 0; }

  h2.section-title {
    font-family: ui-monospace, monospace;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.82rem;
    color: var(--stamp);
    border-bottom: 1px solid var(--paper-line);
    padding-bottom: 0.5rem;
    margin: 0 0 1.1rem;
  }

  form.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-bottom: 1.4rem;
  }
  form.filters input, form.filters select {
    font-family: inherit;
    font-size: 0.92rem;
    background: #fffdf7;
    border: 1px solid var(--paper-line);
    border-radius: 3px;
    padding: 0.45rem 0.6rem;
    color: var(--ink);
  }
  form.filters input[name="title"] { flex: 1 1 200px; }
  form.filters input[name="min_salary"],
  form.filters input[name="max_salary"] { width: 110px; }

  button {
    font-family: ui-monospace, monospace;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 0.78rem;
    background: var(--ink);
    color: var(--paper);
    border: none;
    border-radius: 3px;
    padding: 0.55rem 1.1rem;
    cursor: pointer;
  }
  button:hover { background: var(--stamp); }
  button:focus-visible { outline: 2px solid var(--stamp); outline-offset: 2px; }

  #listings {
    columns: 2 280px;
    column-gap: 2.2rem;
  }
  @media (max-width: 640px) { #listings { columns: 1; } }

  .ad {
    break-inside: avoid;
    border-bottom: 1px dashed var(--paper-line);
    padding: 0 0 1rem;
    margin: 0 0 1rem;
    position: relative;
  }
  .ad h3 {
    margin: 0 0 0.15rem;
    font-size: 1.05rem;
  }
  .ad .meta {
    font-family: ui-monospace, monospace;
    font-size: 0.72rem;
    color: var(--ink-soft);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.35rem;
  }
  .ad .desc {
    font-size: 0.92rem;
    margin: 0.3rem 0;
  }
  .ad .skills {
    font-family: ui-monospace, monospace;
    font-size: 0.7rem;
    color: var(--stamp);
  }
  .stamp-badge {
    display: inline-block;
    font-family: ui-monospace, monospace;
    font-size: 0.62rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--stamp);
    border: 1px solid var(--stamp-soft);
    border-radius: 999px;
    padding: 0.05em 0.55em;
    transform: rotate(-2deg);
    margin-left: 0.4em;
  }

  .empty, .loading {
    font-style: italic;
    color: var(--ink-soft);
    grid-column: 1 / -1;
  }

  .two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2.4rem;
  }
  @media (max-width: 700px) { .two-col { grid-template-columns: 1fr; } }

  .card {
    background: #fffdf7;
    border: 1px solid var(--paper-line);
    border-radius: 6px;
    padding: 1.2rem;
  }
  .card label {
    display: block;
    font-family: ui-monospace, monospace;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ink-soft);
    margin: 0.7rem 0 0.25rem;
  }
  .card label:first-of-type { margin-top: 0; }
  .card input, .card textarea {
    width: 100%;
    font-family: inherit;
    font-size: 0.92rem;
    background: var(--paper);
    border: 1px solid var(--paper-line);
    border-radius: 3px;
    padding: 0.5rem 0.6rem;
    color: var(--ink);
  }
  .card textarea { min-height: 70px; resize: vertical; }
  .card button { margin-top: 1rem; width: 100%; }

  .status {
    font-family: ui-monospace, monospace;
    font-size: 0.76rem;
    margin-top: 0.6rem;
    min-height: 1.1em;
  }
  .status.ok { color: var(--good); }
  .status.err { color: var(--stamp); }

  footer {
    text-align: center;
    margin-top: 3rem;
    padding-top: 1.4rem;
    border-top: 1px solid var(--paper-line);
    font-family: ui-monospace, monospace;
    font-size: 0.68rem;
    color: var(--ink-soft);
    text-transform: uppercase;
    letter-spacing: 0.06em;
  }
</style>
</head>
<body>
<div class="wrap">

  <header class="masthead">
    <p class="kicker">Est. this afternoon &middot; No. 1</p>
    <h1>The Jobberwocky Gazette</h1>
    <p class="tagline">&ldquo;'Twas brillig, and the slithy toves did apply for the wabe jobs&rdquo;</p>
    <div class="edition-row">
      <span id="job-count">— positions listed</span>
      <span>Situations Vacant &amp; Wanted</span>
    </div>
  </header>

  <section>
    <h2 class="section-title">Browse the classifieds</h2>
    <form class="filters" id="search-form">
      <input type="text" name="title" placeholder="Search by title&hellip;">
      <input type="number" name="min_salary" placeholder="Min $">
      <input type="number" name="max_salary" placeholder="Max $">
      <select name="source">
        <option value="all">All sources</option>
        <option value="internal">Internal only</option>
        <option value="external">External only</option>
      </select>
      <button type="submit">Search</button>
    </form>

    <div id="listings">
      <p class="loading">Fetching the latest wanted ads&hellip;</p>
    </div>
  </section>

  <section class="two-col">
    <div class="card">
      <h2 class="section-title">Place a listing</h2>
      <form id="job-form">
        <label for="j-title">Position</label>
        <input id="j-title" name="title" required>
        <label for="j-company">Company</label>
        <input id="j-company" name="company" required>
        <label for="j-location">Location</label>
        <input id="j-location" name="location">
        <label for="j-description">Description</label>
        <textarea id="j-description" name="description" required></textarea>
        <label for="j-skills">Skills (comma-separated)</label>
        <input id="j-skills" name="skills" placeholder="php, laravel, mysql">
        <button type="submit">Post position</button>
        <p class="status" id="job-status"></p>
      </form>
    </div>

    <div class="card">
      <h2 class="section-title">Subscribe to alerts</h2>
      <form id="sub-form">
        <label for="s-email">Your email</label>
        <input id="s-email" name="email" type="email" required>
        <label for="s-pattern">Notify me about (optional)</label>
        <input id="s-pattern" name="search_pattern" placeholder="e.g. laravel">
        <button type="submit">Subscribe</button>
        <p class="status" id="sub-status"></p>
      </form>
    </div>
  </section>

  <footer>Jobberwocky &middot; a home project, mimsy but functional</footer>
</div>

<script>
(function () {
  const listings = document.getElementById('listings');
  const jobCount = document.getElementById('job-count');
  const searchForm = document.getElementById('search-form');
  const jobForm = document.getElementById('job-form');
  const subForm = document.getElementById('sub-form');
  const jobStatus = document.getElementById('job-status');
  const subStatus = document.getElementById('sub-status');

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  function renderJob(job) {
    const salary = job.salary && (job.salary.min || job.salary.max)
      ? `$${job.salary.min ?? '?'}&ndash;$${job.salary.max ?? '?'} ${job.salary.currency ?? ''}`
      : 'Salary undisclosed';
    const badge = job.source === 'external' ? '<span class="stamp-badge">syndicated</span>' : '';
    const skills = (job.skills || []).map(escapeHtml).join(' &middot; ');
    return `
      <article class="ad">
        <h3>${escapeHtml(job.title)}${badge}</h3>
        <div class="meta">${escapeHtml(job.company || 'Company withheld')} &mdash; ${escapeHtml(job.location || 'Location unspecified')} &mdash; ${salary}</div>
        <p class="desc">${escapeHtml(job.description || 'No further particulars supplied.')}</p>
        ${skills ? `<div class="skills">${skills}</div>` : ''}
      </article>
    `;
  }

  async function loadJobs(params) {
    listings.innerHTML = '<p class="loading">Fetching the latest wanted ads&hellip;</p>';
    try {
      const query = new URLSearchParams(params).toString();
      const res = await fetch('/api/jobs' + (query ? `?${query}` : ''));
      const body = await res.json();
      const jobs = body.data || [];
      jobCount.textContent = `${body.meta?.total ?? jobs.length} positions listed`;
      listings.innerHTML = jobs.length
        ? jobs.map(renderJob).join('')
        : '<p class="empty">No positions match your search. Try loosening the type.</p>';
    } catch (e) {
      listings.innerHTML = '<p class="empty">Could not reach the presses. Is the server running?</p>';
    }
  }

  searchForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(searchForm).entries());
    Object.keys(data).forEach((k) => { if (!data[k]) delete data[k]; });
    loadJobs(data);
  });

  jobForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const raw = Object.fromEntries(new FormData(jobForm).entries());
    const payload = {
      title: raw.title,
      company: raw.company,
      location: raw.location || null,
      description: raw.description,
      skills: raw.skills ? raw.skills.split(',').map((s) => s.trim()).filter(Boolean) : [],
    };
    jobStatus.textContent = 'Sending to the printer&hellip;';
    jobStatus.className = 'status';
    try {
      const res = await fetch('/api/jobs', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
      });
      if (!res.ok) throw new Error();
      jobForm.reset();
      jobStatus.textContent = 'Published. It now appears above.';
      jobStatus.className = 'status ok';
      loadJobs({});
    } catch (e) {
      jobStatus.textContent = 'Could not publish — check the required fields.';
      jobStatus.className = 'status err';
    }
  });

  subForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const raw = Object.fromEntries(new FormData(subForm).entries());
    subStatus.textContent = 'Registering your subscription&hellip;';
    subStatus.className = 'status';
    try {
      const res = await fetch('/api/subscriptions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ email: raw.email, search_pattern: raw.search_pattern || null }),
      });
      if (!res.ok) throw new Error();
      subForm.reset();
      subStatus.textContent = 'Subscribed. Watch your inbox.';
      subStatus.className = 'status ok';
    } catch (e) {
      subStatus.textContent = 'Could not subscribe — check the email address.';
      subStatus.className = 'status err';
    }
  });

  loadJobs({});
})();
</script>
</body>
</html>
