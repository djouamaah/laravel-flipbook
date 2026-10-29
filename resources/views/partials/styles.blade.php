@once
<style>
    .fb-root { --fb-surface: #fff; --fb-text: #2b2724; --fb-muted: #7a726b; --fb-border: #e4ddd3;
               --fb-accent: #8a5a2b; --fb-accent-hover: #6f4620; --fb-danger: #b3261e; color: var(--fb-text); }
    .fb-root *, .fb-root *::before, .fb-root *::after { box-sizing: border-box; }
    .fb-root a { color: var(--fb-accent); text-decoration: none; }
    .fb-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .fb-head h1 { font-size: 1.6rem; margin: 0; }
    .fb-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .fb-actions form { margin: 0; }
    .fb-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; border: 1px solid transparent;
              background: var(--fb-accent); color: #fff !important; font: inherit; font-weight: 500; cursor: pointer; }
    .fb-btn:hover { background: var(--fb-accent-hover); }
    .fb-btn-outline { background: transparent; color: var(--fb-text) !important; border-color: var(--fb-border); }
    .fb-btn-outline:hover { background: #f7f3ee; }
    .fb-btn-danger { background: transparent; color: var(--fb-danger) !important; border-color: #f0c9c6; }
    .fb-btn-danger:hover { background: #fdf0ef; }
    .fb-alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; background: #e8f5e9; color: #1b5e20; }
    .fb-card { background: var(--fb-surface); border: 1px solid var(--fb-border); border-radius: 12px; }
</style>
@endonce
