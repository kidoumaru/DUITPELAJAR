# Requirements Document

## Introduction

This document specifies the requirements for adding a comprehensive **Admin Panel** to **DuitPelajar**, an existing Laravel personal finance tracking web application for students. The Admin Panel introduces role-based access control, centralized category management, user management, announcements, a message and help center, educational articles, application-wide statistics, and an admin profile area — all delivered through a dedicated admin layout that is visually consistent with the DuitPelajar identity but fully separate from the existing user-facing interface.

The feature is built on the existing Laravel 13 / PHP 8.3 / Tailwind v4 / Chart.js / SQLite stack and reuses the existing custom authentication and single-session enforcement. It must not alter the existing user-facing layout, views, authentication mechanism, or break any current user feature. Existing user-created categories are converted into global, admin-managed categories, preserving all existing transaction references.

## Glossary

- **DuitPelajar**: The existing personal finance tracking web application built with Laravel, used by students to record transactions, budgets, and savings goals.
- **Admin**: An authenticated user account whose `Role` column on the `users` table is set to `admin`. An Admin manages the application but has no access to individual user financial data.
- **RegularUser**: An authenticated user account whose `Role` column on the `users` table is set to `user`. A RegularUser records personal transactions and consumes admin-published content.
- **Role**: The `role` enum column on the `users` table, accepting the values `admin` and `user`, defaulting to `user`.
- **AdminPanel**: The complete set of admin-facing routes, controllers, and Blade views served under the `/admin` URL prefix, rendered using a dedicated admin layout.
- **AdminMiddleware**: The `EnsureUserIsAdmin` middleware that verifies the authenticated user's `Role` equals `admin` before allowing access to any AdminPanel route.
- **UserManagement**: The AdminPanel feature that lists, searches, filters, views, activates, deactivates, and deletes user accounts.
- **CategoryManagement**: The AdminPanel feature that performs create, read, update, delete, activate, and deactivate operations on categories.
- **AnnouncementSystem**: The feature that lets an Admin create, edit, delete, publish, and draft announcements that RegularUsers consume.
- **MessageCenter**: The two-way messaging and help feature that lets a RegularUser start a Conversation and an Admin reply, with conversation status tracking.
- **Conversation**: A message thread initiated by a RegularUser, containing the original message and any replies exchanged between the RegularUser and an Admin.
- **ArticleSystem**: The educational articles feature that lets an Admin create, edit, delete, publish, and draft articles that RegularUsers read.
- **StatisticsEngine**: The AdminPanel feature that aggregates application-wide statistics and charts.
- **AdminProfile**: The AdminPanel feature that lets an Admin view and update the Admin's own profile details, password, and profile photo.
- **ActiveCategory**: A category whose `is_active` column on the `categories` table is `true`.
- **DeactivatedCategory**: A category whose `is_active` column on the `categories` table is `false`.
- **ExistingAuth**: The existing `AuthController` and the `EnsureSingleLoginSession` middleware that handle login, registration, logout, and single-session enforcement.
- **PromoteCommand**: The Artisan command `admin:promote {email}` that grants the `admin` Role to an existing user account.

## Requirements

### Requirement 1: Role System and Authorization

**User Story:** As a system owner, I want role-based access control with an admin role, so that only authorized administrators can reach administrative features.

#### Acceptance Criteria

1. THE AdminPanel routes SHALL be guarded by the `auth` middleware, the `EnsureSingleLoginSession` middleware, and the `AdminMiddleware` applied in sequence.
2. IF an authenticated RegularUser requests any route under the `/admin` prefix, THEN THE AdminMiddleware SHALL respond with HTTP 403 Forbidden.
3. IF an unauthenticated visitor requests any route under the `/admin` prefix, THEN THE `auth` middleware SHALL redirect the visitor to the existing login page.
4. WHEN an administrator executes `php artisan admin:promote {email}` for an existing user account, THEN THE PromoteCommand SHALL set that account's `Role` to `admin` and print a confirmation message.
5. IF the PromoteCommand is executed with an email that does not match an existing user account, THEN THE PromoteCommand SHALL print an error message and leave all accounts unchanged.
6. WHEN an Admin logs in successfully through the existing login form, THEN THE `AuthController` SHALL redirect the Admin to the AdminPanel dashboard route.
7. WHEN a RegularUser logs in successfully through the existing login form, THEN THE `AuthController` SHALL redirect the RegularUser to the existing user dashboard route.
8. WHILE an Admin is authenticated, THE ExistingAuth single-session enforcement SHALL continue to apply unchanged to AdminPanel routes.

### Requirement 2: Admin Dashboard

**User Story:** As an Admin, I want an overview dashboard showing application-wide aggregate statistics, so that I can monitor the health and activity of DuitPelajar.

#### Acceptance Criteria

1. WHEN the Admin opens the AdminPanel dashboard, THEN THE AdminPanel SHALL display the total number of users, the number of active users, the total number of transactions, the number of income categories, the number of expense categories, the number of incoming messages, and the number of published announcements.
2. WHEN the Admin opens the AdminPanel dashboard, THEN THE AdminPanel SHALL display a user growth chart rendered with Chart.js.
3. WHEN the Admin opens the AdminPanel dashboard, THEN THE AdminPanel SHALL display an application activity summary consisting of the aggregate statistics described in criterion 2.1 presented as dashboard cards, along with the user growth chart described in criterion 2.2.
4. THE AdminPanel dashboard SHALL display only aggregate application statistics and SHALL NOT expose any individual user financial transaction data.

### Requirement 3: User Management

**User Story:** As an Admin, I want to manage user accounts, so that I can oversee and control who can use DuitPelajar.

#### Acceptance Criteria

1. WHEN the Admin opens UserManagement, THEN THE UserManagement SHALL list all user accounts with pagination of 15 records per page.
2. WHEN the Admin submits a search query containing a name or email fragment, THEN THE UserManagement SHALL filter the user list to accounts whose name or email contains the submitted fragment.
3. WHEN the Admin requests to view a user account, THEN THE UserManagement SHALL display that account's profile details, including name, email, `school_level`, `Role`, and account status.
4. WHEN the Admin requests to view a user account, THEN THE UserManagement SHALL display profile details without exposing that user's transaction amounts or balances.
5. WHEN the Admin requests to activate an inactive account, THEN THE UserManagement SHALL set that account's `is_active` flag to `true`.
6. WHEN the Admin requests to deactivate an active account, THEN THE UserManagement SHALL set that account's `is_active` flag to `false`.
7. IF a deactivated account attempts to log in, THEN THE ExistingAuth login flow SHALL reject the login and display an error message.
8. IF the Admin attempts to delete an account whose `Role` is `admin`, THEN THE UserManagement SHALL prevent the deletion and display an error message.
9. WHEN the Admin requests to delete an account whose `Role` is `user`, THEN THE UserManagement SHALL remove the account record from the database.
10. WHILE an account is deactivated, THE UserManagement SHALL keep that account's stored data intact so the account can be reactivated later.

### Requirement 4: Centralized Category Management

**User Story:** As an Admin, I want centralized control over all categories, so that users select from a consistent, approved set of income and expense categories.

#### Acceptance Criteria

1. THE CategoryManagement SHALL provide create, read, update, delete, activate, and deactivate operations for categories, and SHALL restrict these operations to an Admin.
2. WHEN the Admin creates a category, THEN THE CategoryManagement SHALL persist the category `name`, `type`, `icon`, `color`, and `is_active` values provided by the Admin.
3. WHEN the Admin updates a category, THEN THE CategoryManagement SHALL persist the submitted `name`, `type`, `icon`, `color`, and `is_active` changes.
4. WHEN the Admin deactivates a category, THEN THE CategoryManagement SHALL set that category's `is_active` flag to `false`.
5. WHEN the Admin activates a category, THEN THE CategoryManagement SHALL set that category's `is_active` flag to `true`.
6. IF the Admin attempts to delete a category that has one or more associated transactions, THEN THE CategoryManagement SHALL prevent the deletion and display an error message stating the category is in use.
7. WHEN the Admin deletes a category that has zero associated transactions, THEN THE CategoryManagement SHALL remove the category record from the database.
8. THE user-facing `CategoryController` SHALL remove the create, update, and delete capabilities for RegularUsers while preserving the read-only category listing.
9. WHEN a RegularUser creates or edits a transaction, THEN THE `TransactionController` SHALL present only ActiveCategory records for category selection.
10. IF a RegularUser submits a transaction referencing a DeactivatedCategory, THEN THE `TransactionController` SHALL reject the submission with a validation error.
11. WHEN a RegularUser submits a transaction referencing an ActiveCategory, THEN THE `TransactionController` SHALL accept and persist the transaction.
12. WHILE an existing transaction references a category that was converted from a user-created category to a global category, THE DuitPelajar application SHALL preserve the transaction's `category_id` reference and continue to display that category correctly.
13. WHEN the category migration runs, THEN THE DuitPelajar application SHALL add an `is_active` boolean column to the `categories` table defaulting to `true`.
14. WHEN the category migration runs, THEN THE DuitPelajar application SHALL convert existing user-created categories to global categories by setting their `user_id` to `null` while preserving each category record.

### Requirement 5: Announcement Management

**User Story:** As an Admin, I want to create and publish announcements, so that users receive important information within DuitPelajar.

#### Acceptance Criteria

1. WHEN the Admin creates an announcement, THEN THE AnnouncementSystem SHALL persist the announcement `title`, `body`, `status`, `author_id`, and `published_at` values.
2. WHEN the Admin edits an announcement, THEN THE AnnouncementSystem SHALL persist the submitted changes.
3. WHEN the Admin deletes an announcement, THEN THE AnnouncementSystem SHALL remove the announcement record.
4. WHEN the Admin sets an announcement `status` to `published` with a `published_at` date in the past or present, THEN THE AnnouncementSystem SHALL display that announcement to RegularUsers.
5. WHEN the Admin sets an announcement `status` to `published` with a `published_at` date in the future, THEN THE AnnouncementSystem SHALL hide that announcement from RegularUsers until the `published_at` date is reached.
6. WHEN the Admin sets an announcement `status` to `draft`, THEN THE AnnouncementSystem SHALL hide that announcement from RegularUsers.

### Requirement 6: Message and Help Center

**User Story:** As a RegularUser, I want to send messages to the Admin and receive replies, so that I can get help with DuitPelajar; and as an Admin, I want to read and reply to user messages, so that I can support users.

#### Acceptance Criteria

1. WHEN a RegularUser submits a message containing a `title` and a `body`, THEN THE MessageCenter SHALL create a Conversation owned by that RegularUser with a status of `new`.
2. WHEN a RegularUser opens the MessageCenter, THEN THE MessageCenter SHALL list only the Conversations owned by that RegularUser, each with its current status.
3. WHEN a RegularUser opens an owned Conversation, THEN THE MessageCenter SHALL display the original message and all replies in chronological order.
4. WHEN an Admin opens the MessageCenter, THEN THE MessageCenter SHALL list all Conversations across all RegularUsers, each with its current status.
5. WHEN an Admin submits a reply to a Conversation, THEN THE MessageCenter SHALL store the reply, record the Admin as the sender, and set the Conversation status to `processing`.
6. WHEN a RegularUser submits a reply to an owned Conversation, THEN THE MessageCenter SHALL store the reply and record the RegularUser as the sender.
7. WHEN an Admin sets a Conversation status to `done`, THEN THE MessageCenter SHALL update and persist the status as `done`.
8. IF a RegularUser requests a Conversation owned by a different RegularUser, THEN THE MessageCenter SHALL respond with HTTP 403 Forbidden.
9. THE MessageCenter SHALL support the three conversation statuses `new`, `processing`, and `done`.

### Requirement 7: Educational Articles

**User Story:** As an Admin, I want to publish educational articles, so that users can learn about personal finance; and as a RegularUser, I want to read published articles.

#### Acceptance Criteria

1. WHEN the Admin creates an article, THEN THE ArticleSystem SHALL persist the article `title`, `slug`, `thumbnail` image path, `content`, article `category`, `status`, `author_id`, and `published_at` values.
2. WHEN the Admin edits an article, THEN THE ArticleSystem SHALL persist the submitted changes.
3. WHEN the Admin deletes an article, THEN THE ArticleSystem SHALL remove the article record.
4. WHEN an article `status` is `published` and its `published_at` date is in the past or present, THEN THE ArticleSystem SHALL display that article to RegularUsers.
5. WHEN an article `status` is `draft`, THEN THE ArticleSystem SHALL hide that article from RegularUsers.
6. WHEN the Admin uploads a thumbnail image for an article, THEN THE ArticleSystem SHALL store the image file and record its path on the article record.

### Requirement 8: App Statistics

**User Story:** As an Admin, I want detailed application statistics, so that I can understand usage trends across DuitPelajar.

#### Acceptance Criteria

1. WHEN the Admin opens the StatisticsEngine, THEN THE StatisticsEngine SHALL display a user growth chart, users grouped by registration period, the total number of transactions, the count of help messages, the count of published articles, and the count of published announcements.
2. THE StatisticsEngine SHALL display only aggregate application statistics and SHALL NOT display any individual user financial information.

### Requirement 9: Admin Profile Settings

**User Story:** As an Admin, I want to manage my own profile, so that I can keep my account details and credentials up to date.

#### Acceptance Criteria

1. WHEN the Admin opens AdminProfile, THEN THE AdminProfile SHALL display the Admin's name, email, and current profile photo.
2. WHEN the Admin updates the profile name or email, THEN THE AdminProfile SHALL validate and persist the submitted changes.
3. WHEN the Admin updates the password, THEN THE AdminProfile SHALL hash the new password using the existing hashing mechanism and persist the change.
4. WHEN the Admin uploads a profile photo, THEN THE AdminProfile SHALL store the image file and link the stored path to the Admin's account.
5. WHEN the Admin requests to log out, THEN THE AdminPanel SHALL end the session through the ExistingAuth logout mechanism.

### Requirement 10: Admin UI Layout and Navigation

**User Story:** As an Admin, I want a clean, responsive admin interface in Indonesian, so that I can manage DuitPelajar efficiently.

#### Acceptance Criteria

1. THE AdminPanel SHALL render a dedicated admin layout that is separate from the existing user-facing layout.
2. THE AdminPanel SHALL display a sidebar navigation containing the items, in order: Dashboard, Manajemen Pengguna, Manajemen Kategori, Pengumuman, Pesan dan Bantuan, Artikel Edukasi, Statistik Aplikasi, Profil Admin, and Logout.
3. THE AdminPanel SHALL display all interface text in the Indonesian language.
4. THE AdminPanel SHALL render responsively across desktop and mobile viewport widths.
5. THE AdminPanel SHALL apply a clean, minimalist visual style consistent with the DuitPelajar identity.
6. THE AdminPanel SHALL NOT modify any existing user-facing layout or view file, except for the category-centralization changes specified in Requirement 4.

### Requirement 11: Database Schema and Migrations

**User Story:** As a system owner, I want clean migrations for the new schema, so that the feature integrates with the existing database without breaking current data.

#### Acceptance Criteria

1. WHEN the migrations run, THEN THE DuitPelajar application SHALL add a `role` enum column accepting `admin` and `user` to the `users` table, defaulting to `user`.
2. WHEN the migrations run, THEN THE DuitPelajar application SHALL add an `is_active` boolean column to the `users` table, defaulting to `true`.
3. WHEN the migrations run, THEN THE DuitPelajar application SHALL add an `is_active` boolean column to the `categories` table, defaulting to `true`.
4. WHEN the migrations run, THEN THE DuitPelajar application SHALL create an `announcements` table containing `id`, `title`, `body`, a `status` enum accepting `published` and `draft`, a nullable `published_at` timestamp, an `author_id` foreign key referencing `users`, and timestamps.
5. WHEN the migrations run, THEN THE DuitPelajar application SHALL create a `messages` table containing `id`, a `user_id` foreign key referencing `users` with cascade delete, a `title`, a `body`, a `status` enum accepting `new`, `processing`, and `done`, and timestamps.
6. WHEN the migrations run, THEN THE DuitPelajar application SHALL create a `message_replies` table containing `id`, a `message_id` foreign key referencing `messages` with cascade delete, a `sender_id` foreign key referencing `users`, a `body`, and timestamps.
7. WHEN the migrations run, THEN THE DuitPelajar application SHALL create an `articles` table containing `id`, `title`, a unique `slug`, a nullable `thumbnail` path, a `content` long text, a `category` string, a `status` enum accepting `published` and `draft`, a nullable `published_at` timestamp, an `author_id` foreign key referencing `users`, and timestamps.
8. THE migrations SHALL define foreign keys, indexes, and column constraints for every new table and column.
9. THE migrations SHALL be reversible, providing a `down` method that drops each added table or column.

### Requirement 12: Security and Backward Compatibility

**User Story:** As a system owner, I want the AdminPanel secured and existing features preserved, so that the application stays safe and stable.

#### Acceptance Criteria

1. THE DuitPelajar application SHALL protect every AdminPanel form submission with a CSRF token.
2. THE DuitPelajar application SHALL apply mass-assignment protection by defining an explicit `$fillable` whitelist on every model.
3. THE DuitPelajar application SHALL validate every AdminPanel form input on the backend before persisting data.
4. THE DuitPelajar application SHALL hash every stored password using the existing hashing mechanism.
5. WHILE the AdminPanel is active, THE DuitPelajar application SHALL preserve the ExistingAuth login, registration, logout, and single-session behavior unchanged for RegularUsers.
6. THE DuitPelajar application SHALL keep the existing user-facing routes, controllers, and views functional without modification, except for the category-centralization changes specified in Requirement 4.
7. THE DuitPelajar application SHALL enforce conversation privacy so that a RegularUser can access only Conversations that the RegularUser owns.

### Requirement 13: Testing

**User Story:** As a developer, I want automated tests covering the AdminPanel, so that I can verify the feature works and existing behavior is preserved.

#### Acceptance Criteria

1. WHEN an Admin logs in with valid credentials, THEN the test suite SHALL assert a successful redirect to the AdminPanel dashboard.
2. WHEN a RegularUser logs in with valid credentials, THEN the test suite SHALL assert a successful redirect to the existing user dashboard.
3. WHEN an unauthenticated visitor requests an AdminPanel route, THEN the test suite SHALL assert a redirect to the login page.
4. WHEN a RegularUser requests an AdminPanel route, THEN the test suite SHALL assert an HTTP 403 response.
5. WHEN an Admin performs create, update, activate, deactivate, and delete operations on categories, THEN the test suite SHALL assert the database state reflects each operation.
6. WHEN a RegularUser views the transaction category selector, THEN the test suite SHALL assert only ActiveCategory records appear.
7. WHEN a RegularUser submits a transaction referencing a DeactivatedCategory, THEN the test suite SHALL assert a validation error response.
8. WHILE an existing transaction references a converted global category, THEN the test suite SHALL assert the transaction's `category_id` association remains intact and resolvable.
9. WHEN an Admin creates, publishes, and drafts announcements, THEN the test suite SHALL assert the correct announcements are visible to RegularUsers.
10. WHEN a RegularUser sends a message and an Admin replies, THEN the test suite SHALL assert the Conversation status transitions from `new` to `processing`.
11. WHEN a RegularUser requests a Conversation owned by another RegularUser, THEN the test suite SHALL assert an HTTP 403 response.
12. WHEN an Admin creates, publishes, and drafts articles, THEN the test suite SHALL assert the correct articles are visible to RegularUsers.
13. WHEN an Admin attempts to delete an account whose `Role` is `admin`, THEN the test suite SHALL assert the account is not deleted and an error is returned.
14. THE test suite SHALL execute with zero errors and zero failing assertions across the AdminPanel feature tests.
