# UI Benchmark From Open-Source PM Systems

## Goal
Use proven UX patterns from mature open-source project management tools to make APERLEX cleaner, faster, and more consistent.

## Recommended Repositories

1. AppFlowy
- URL: https://github.com/AppFlowy-IO/AppFlowy
- Why reference:
  - Strong information hierarchy for workspace-level navigation.
  - Clean page density and readable spacing rhythm.
  - Better empty/loading states and component consistency.
- What to borrow as idea (not copy-paste blindly):
  - Left navigation grouping and section labeling.
  - Clear card/list visual states.
  - Editor and panel spacing proportions.

2. Focalboard
- URL: https://github.com/mattermost-community/focalboard
- Why reference:
  - Excellent board/list duality for task management.
  - Good filter/sort UX patterns for productivity-heavy screens.
  - Strong visual clarity for status and assignee contexts.
- What to borrow as idea:
  - Dense but readable table/list layouts.
  - Better quick actions and row-level controls.
  - Strong focus states and keyboard-friendly interactions.

3. Taiga Front
- URL: https://github.com/taigaio/taiga-front
- Why reference:
  - Mature agile workflow UX.
  - Practical role and project context switching.
  - Good separation between navigation and execution areas.
- What to borrow as idea:
  - Consistent action placement and page scaffolding.
  - Better issue/task metadata chips and visual priority handling.

## Licensing Notes (Important)
- AppFlowy: AGPL-3.0
- Focalboard: repository notes should be reviewed for current maintenance and license details.
- Taiga Front: AGPL-3.0

Before reusing code directly, verify:
- License compatibility with your product/distribution model.
- Attribution and disclosure obligations.
- Whether your deployment model triggers copyleft obligations.

If uncertain, use these repos as UX references and re-implement patterns in your own codebase.

## Patterns APERLEX Should Prioritize

1. Unified Filter Bar
- One shared visual component for search + selects + actions.
- Consistent height, paddings, and focus ring.

2. Unified Data Table
- Sticky table headers.
- Consistent hover state and row affordance.
- Shared chip system for status/priority.

3. Unified Sidebar and List Cards
- Shared active/hover states.
- Better contrast for selected conversation/group.
- Balanced spacing for dense data screens.

4. Empty/Loading/Error States
- One visual language for all modules.
- Actionable CTAs in every empty state.

5. Mobile First Refinements
- Collapse filter controls cleanly.
- Preserve readability with reduced table paddings.
- Keep key actions reachable.

## What Was Implemented In This Workspace
- Added reusable UI consistency classes in global stylesheet:
  - table-shell, table-clean, table-empty
  - panel-toolbar, search-wrap, search-field, filter-select
  - meta-chip, meta-counter
  - list-card, list-card-active
- Applied these classes to major views:
  - Projects index
  - Tasks index
  - Messenger sidebar lists

## Next Upgrade Wave (Recommended)
1. Build Blade components for `filter-bar`, `status-chip`, `empty-state`, and `data-table-shell`.
2. Add skeleton loading placeholders on Projects/Tasks/Messenger first paint.
3. Add keyboard navigation support for messenger list and search results.
4. Add visual density toggle (Comfortable/Compact) for power users.
5. Standardize all form labels/help text/error text into one component style.

## Quick Validation Checklist
- Same search field style appears in Projects, Tasks, Messenger.
- Same status chip and priority chip style appears across pages.
- Active row/card state looks consistent in lists and sidebars.
- Mobile views keep spacing readable and controls usable.
- Empty state messaging always includes one clear next action.
