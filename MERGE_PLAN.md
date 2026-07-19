# Merge Plan: Integrating `local_parentassign` into `local_parentportal`

## Overview
The goal of this merge is to unify the background automation of `local_parentassign` (which automatically pairs students and parents based on events) with the frontend interface of `local_parentportal` (which provides a dashboard and manual child creation). The result will be a single, robust plugin (`local_parentportal`) that handles both B2B (bulk uploads/event-driven) and B2C (manual frontend) parent onboarding.

## Phase 1: Database and Schema Adjustments
Because `parentportal` requires certain metadata (`sex`, `grade`, `curriculum`) that is not available during the automated event triggers of `parentassign`, the database schema must be adjusted to prevent errors during automatic synchronization.

1.  **Modify `db/install.xml` in `parentportal`**:
    *   Change the `sex`, `grade`, and `curriculum` fields in the `local_parentportal_children` table to allow `NULL` (i.e., `NOTNULL="false"`) or define sensible defaults.
2.  **Update `db/upgrade.php`**:
    *   Write the necessary upgrade steps to alter the table structure on existing installations so that these fields are no longer strictly required.

## Phase 2: File and Code Migration
Move the core background logic from `parentassign` into the `parentportal` directory, ensuring all namespaces and references are updated.

1.  **Events Configuration**:
    *   Copy `db/events.php` from `parentassign` to `parentportal/db/events.php`.
    *   Change the callback references from `\local_parentassign\observer` to `\local_parentportal\observer`.
2.  **Observer Logic**:
    *   Copy `classes/observer.php` from `parentassign` into `parentportal/classes/`.
    *   Change the namespace from `namespace local_parentassign;` to `namespace local_parentportal;`.
3.  **Scheduled Tasks**:
    *   Copy `db/tasks.php` and the `classes/task/` directory from `parentassign` into `parentportal`.
    *   Update all namespaces and class references to `local_parentportal`.

## Phase 3: Logic Refactoring (The Core Merge)
The most critical part of the merge is updating the observer logic so it aligns with `parentportal`'s custom database architecture.

1.  **Update `manager::assign_parent()`**:
    *   Move the `assign_parent` function (and its helpers like `create_parent_user`) from `parentassign/classes/manager.php` into `parentportal/classes/manager.php`.
    *   **Crucial Change**: When `assign_parent` auto-creates a parent and assigns the Moodle role, it **must also insert a new record into the `local_parentportal_children` table**. 
    *   If it fails to insert this record, the parent will log in to the portal and see an empty dashboard, even though they technically have the Moodle role.
2.  **Handle Missing Data**:
    *   When inserting into `local_parentportal_children` automatically, populate the `timecreated` field, but pass `null` or empty strings for `sex`, `grade`, and `curriculum` (as updated in Phase 1).

## Phase 4: UI Enhancements for Auto-assigned Users
Since users auto-assigned via the backend won't have complete metadata, the frontend should gracefully handle this.

1.  **Update `index.php` (Dashboard)**:
    *   When listing children, check if `grade` or `curriculum` is empty/null. If so, display a "Pending Configuration" or "Not specified" badge instead of breaking or showing blank spaces.
2.  **Add Edit Capabilities (Optional but Recommended)**:
    *   Allow parents to "Edit Child Details" from the dashboard so they can fill in the missing `grade`, `curriculum`, and `sex` for accounts that were auto-imported by the school.

## Phase 5: Cleanup and Deprecation
1.  **Uninstall `parentassign`**: 
    *   Once the merge is complete and tested, the standalone `local_parentassign` plugin should be uninstalled from the Moodle instance and its directory deleted.
2.  **Clean up Profile Fields**: 
    *   Consider migrating away from the core custom profile fields (`parent_email`, `parent_name`) in the future, fetching/storing this data directly into the `parentportal` custom tables to keep data centralized.

## Phase 6: Testing Scenarios
Before deploying to production, run these two critical paths:
1.  **The B2B Flow (Automated)**: Create a student user via Site Admin (or CSV upload) with the custom profile fields `parent_email` and `parent_name` populated. Verify that:
    *   The parent account is created.
    *   The parent receives an email with their password.
    *   The parent logs in, sees the portal dashboard, and the child is listed there automatically.
2.  **The B2C Flow (Manual)**: Log in as an existing parent, go to the portal, and fill out the "Add Child" form. Verify that:
    *   The child is created successfully.
    *   The child appears immediately on the dashboard with the correct grade and curriculum metadata.
