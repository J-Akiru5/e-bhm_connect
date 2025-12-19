<?php
/**
 * English Language File
 * E-BHM Connect - Barangay Health Management System
 */

return [
    // ========================
    // General / Common
    // ========================
    'app_name' => 'E-BHM Connect',
    'app_tagline' => 'Barangay Health Management System',
    'welcome' => 'Welcome',
    'hello' => 'Hello',
    'loading' => 'Loading...',
    'please_wait' => 'Please wait...',
    'success' => 'Success',
    'error' => 'Error',
    'warning' => 'Warning',
    'info' => 'Information',
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'save_changes' => 'Save Changes',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'view' => 'View',
    'add' => 'Add',
    'create' => 'Create',
    'update' => 'Update',
    'search' => 'Search',
    'filter' => 'Filter',
    'clear' => 'Clear',
    'reset' => 'Reset',
    'close' => 'Close',
    'back' => 'Back',
    'next' => 'Next',
    'previous' => 'Previous',
    'submit' => 'Submit',
    'yes' => 'Yes',
    'no' => 'No',
    'all' => 'All',
    'none' => 'None',
    'select' => 'Select',
    'required' => 'Required',
    'optional' => 'Optional',
    'actions' => 'Actions',
    'status' => 'Status',
    'date' => 'Date',
    'time' => 'Time',
    'details' => 'Details',
    'description' => 'Description',
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'address' => 'Address',
    'no_data' => 'No data available',
    'no_results' => 'No results found',
    
    // ========================
    // User Roles
    // ========================
    'roles' => [
        'guest' => 'Guest',
        'bhw' => 'Health Worker',
        'admin' => 'Administrator',
        'superadmin' => 'Super Admin',
    ],
    
    // ========================
    // Navigation
    // ========================
    'nav' => [
        'dashboard' => 'Dashboard',
        'patients' => 'Patients',
        'messages' => 'Messages',
        'inventory' => 'Inventory',
        'inventory_categories' => 'Inventory Categories',
        'announcements' => 'Announcements',
        'reports' => 'Reports',
        'programs' => 'Programs',
        'bhw_users' => 'BHW Users',
        'bhw_management' => 'BHW Management',
        'settings' => 'Settings',
        'account_settings' => 'Account Settings',
        'app_settings' => 'App Settings',
        'profile' => 'Profile',
        'logout' => 'Logout',
        'login' => 'Login',
        'register' => 'Register',
        'home' => 'Home',
        'admin' => 'Admin',
        'main_menu' => 'Main Menu',
        'inventory_section' => 'Inventory',
        'content_section' => 'Content',
        'administration' => 'Administration',
        'toggle_sidebar' => 'Toggle Sidebar',
        'view_public_site' => 'View Public Site',
        'db_backup' => 'DB Backup',
        'audit_logs' => 'Audit Logs',
        'user_roles' => 'User Roles',
    ],
    
    // ========================
    // Authentication
    // ========================
    'auth' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'register' => 'Register',
        'forgot_password' => 'Forgot Password?',
        'reset_password' => 'Reset Password',
        'change_password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'username' => 'Username',
        'password' => 'Password',
        'remember_me' => 'Remember Me',
        'login_success' => 'Login successful!',
        'login_failed' => 'Invalid username or password.',
        'logout_success' => 'You have been logged out.',
        'session_expired' => 'Your session has expired. Please login again.',
        'unauthorized' => 'You are not authorized to access this page.',
        'account_pending' => 'Your account is pending approval.',
        'account_not_verified' => 'Please verify your email address.',
        'verification_sent' => 'Verification email sent!',
        'verification_success' => 'Email verified successfully!',
        'password_changed' => 'Password changed successfully!',
        'password_mismatch' => 'Passwords do not match.',
        'password_requirements' => 'Password must be at least 8 characters.',
    ],
    
    // ========================
    // Dashboard
    // ========================
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome_message' => 'Welcome back, :name!',
        'overview' => 'Overview',
        'quick_actions' => 'Quick Actions',
        'recent_activity' => 'Recent Activity',
        'statistics' => 'Statistics',
        'total_patients' => 'Total Patients',
        'total_bhws' => 'Total BHWs',
        'total_inventory' => 'Inventory Items',
        'sms_sent' => 'SMS Sent',
        'sms_failed' => 'SMS Failed',
        'sms_pending' => 'SMS Pending',
        'low_stock_items' => 'Low Stock Items',
        'pending_approvals' => 'Pending Approvals',
        'recent_registrations' => 'Recent Registrations',
        'recent_visits' => 'Recent Visits',
        'inventory_overview' => 'Inventory Overview',
        'audit_logs' => 'Audit Logs',
        'sms_delivery_status' => 'SMS Delivery Status',
        'patient_registration_trends' => 'Patient Registration Trends',
        'medicine_stock_levels' => 'Medicine Stock Levels',
    ],
    
    // ========================
    // Patients
    // ========================
    'patients' => [
        'title' => 'Patients',
        'add_patient' => 'Add Patient',
        'edit_patient' => 'Edit Patient',
        'view_patient' => 'View Patient',
        'patient_list' => 'Patient List',
        'patient_details' => 'Patient Details',
        'full_name' => 'Full Name',
        'birthdate' => 'Date of Birth',
        'age' => 'Age',
        'sex' => 'Sex',
        'male' => 'Male',
        'female' => 'Female',
        'contact_number' => 'Contact Number',
        'medical_history' => 'Medical History',
        'vitals' => 'Vitals',
        'visits' => 'Visits',
        'medications' => 'Medications',
        'no_patients' => 'No patients found.',
        'patient_added' => 'Patient added successfully!',
        'patient_updated' => 'Patient updated successfully!',
        'patient_deleted' => 'Patient deleted successfully!',
        'confirm_delete' => 'Are you sure you want to delete this patient?',
    ],
    
    // ========================
    // Inventory
    // ========================
    'inventory' => [
        'title' => 'Medication & Supply Inventory',
        'add_item' => 'Add Item',
        'edit_item' => 'Edit Item',
        'item_name' => 'Item Name',
        'category' => 'Category',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'stock_level' => 'Stock Level',
        'in_stock' => 'In Stock',
        'low_stock' => 'Low Stock',
        'out_of_stock' => 'Out of Stock',
        'last_restock' => 'Last Restock',
        'dispense' => 'Dispense',
        'dispense_medicine' => 'Dispense Medicine',
        'quantity_dispensed' => 'Quantity Dispensed',
        'dispensed_to' => 'Dispensed To',
        'dispensed_by' => 'Dispensed By',
        'dispense_success' => 'Medicine dispensed successfully!',
        'insufficient_stock' => 'Insufficient stock.',
        'item_added' => 'Item added successfully!',
        'item_updated' => 'Item updated successfully!',
        'item_deleted' => 'Item deleted successfully!',
    ],
    
    // ========================
    // Announcements
    // ========================
    'announcements' => [
        'title' => 'Announcements',
        'add_announcement' => 'Add Announcement',
        'create_announcement' => 'New Announcement',
        'edit_announcement' => 'Edit Announcement',
        'announcement_title' => 'Title',
        'content' => 'Content',
        'posted_by' => 'Posted By',
        'posted_on' => 'Posted On',
        'no_announcements' => 'No announcements yet.',
        'announcement_added' => 'Announcement posted successfully!',
        'announcement_updated' => 'Announcement updated successfully!',
        'announcement_deleted' => 'Announcement deleted successfully!',
    ],
    
    // ========================
    // Settings
    // ========================
    'settings' => [
        'title' => 'Settings',
        'account_settings' => 'Account Settings',
        'app_settings' => 'Application Settings',
        'preferences' => 'Preferences',
        'appearance' => 'Appearance',
        'theme' => 'Theme',
        'theme_light' => 'Light Mode',
        'theme_dark' => 'Dark Mode',
        'theme_system' => 'System Default',
        'light_mode' => 'Light',
        'dark_mode' => 'Dark',
        'language' => 'Language',
        'language_en' => 'English',
        'language_tl' => 'Tagalog',
        'notifications' => 'Notifications',
        'email_notifications' => 'Email Notifications',
        'sms_notifications' => 'SMS Notifications',
        'profile_picture' => 'Profile Picture',
        'upload_avatar' => 'Upload Avatar',
        'remove_avatar' => 'Remove Avatar',
        'personal_info' => 'Personal Information',
        'profile_information' => 'Profile Information',
        'security' => 'Security',
        'settings_saved' => 'Settings saved successfully!',
        'preferences_saved' => 'Preferences saved successfully!',
        'save_preferences' => 'Save Preferences',
        'profile_updated' => 'Profile updated successfully!',
        'general' => 'General',
        'features' => 'Features',
        'maintenance' => 'Maintenance',
        'maintenance_mode' => 'Maintenance Mode',
        'enable_maintenance' => 'Enable Maintenance Mode',
        'maintenance_message' => 'Maintenance Message',
        'manage_your_account' => 'Manage your account settings and preferences',
        'account_created' => 'Account Created',
        'last_login' => 'Last Login',
        'verified' => 'Verified',
        'pending' => 'Pending Verification',
        'error_required_fields' => 'Please fill in all required fields.',
        'incorrect_password' => 'Current password is incorrect.',
    ],
    
    // ========================
    // Notifications
    // ========================
    'notifications' => [
        'title' => 'Notifications',
        'loading' => 'Loading notifications...',
        'mark_read' => 'Mark as Read',
        'mark_all_read' => 'Mark All as Read',
        'delete_notification' => 'Delete Notification',
        'no_notifications' => 'No notifications',
        'new_notifications' => 'You have :count new notification(s)',
        'view_all' => 'View All Notifications',
    ],
    
    // ========================
    // BHW Users (Admin)
    // ========================
    'bhw' => [
        'title' => 'BHW Users',
        'bhw_list' => 'BHW List',
        'add_bhw' => 'Add BHW',
        'edit_bhw' => 'Edit BHW',
        'bhw_details' => 'BHW Details',
        'unique_id' => 'BHW ID',
        'assigned_area' => 'Assigned Area',
        'training_cert' => 'Training Certificate',
        'employment_status' => 'Employment Status',
        'account_status' => 'Account Status',
        'pending' => 'Pending',
        'verified' => 'Verified',
        'approved' => 'Approved',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'approve_bhw' => 'Approve BHW Account',
        'reject_bhw' => 'Reject BHW Account',
        'bhw_approved' => 'BHW account approved successfully!',
        'bhw_rejected' => 'BHW account rejected.',
        'superadmin' => 'Super Admin',
        'regular_bhw' => 'Regular BHW',
    ],
    
    // ========================
    // Reports
    // ========================
    'reports' => [
        'title' => 'Reports',
        'generate_report' => 'Generate Report',
        'patient_list_report' => 'Patient List Report',
        'inventory_report' => 'Inventory Stock Report',
        'chronic_disease_report' => 'Chronic Disease Report',
        'download_pdf' => 'Download PDF',
        'export_csv' => 'Export CSV',
    ],
    
    // ========================
    // Programs
    // ========================
    'programs' => [
        'title' => 'Health Programs',
        'add_program' => 'Add Program',
        'edit_program' => 'Edit Program',
        'program_name' => 'Program Name',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'active' => 'Active',
        'completed' => 'Completed',
        'upcoming' => 'Upcoming',
    ],
    
    // ========================
    // Chatbot
    // ========================
    'chatbot' => [
        'title' => 'Chat with Gabby',
        'assistant_name' => 'Gabby',
        'placeholder' => 'Type your message...',
        'send' => 'Send',
        'typing' => 'Gabby is typing...',
        'welcome' => 'Hi! I\'m Gabby, your health assistant. How can I help you today?',
        'greeting' => 'Hi! I\'m Gabby, your health assistant. How can I help you today?',
        'error' => 'Sorry, I encountered an error. Please try again.',
    ],
    
    // ========================
    // Audit Logs
    // ========================
    'audit' => [
        'title' => 'Audit Logs',
        'user' => 'User',
        'action' => 'Action',
        'entity' => 'Entity',
        'ip_address' => 'IP Address',
        'timestamp' => 'Timestamp',
        'details' => 'Details',
        'login' => 'User Login',
        'logout' => 'User Logout',
        'create' => 'Created',
        'update' => 'Updated',
        'delete' => 'Deleted',
        'view' => 'Viewed',
    ],
    
    // ========================
    // Validation Messages
    // ========================
    'validation' => [
        'required' => 'This field is required.',
        'email' => 'Please enter a valid email address.',
        'min_length' => 'Must be at least :min characters.',
        'max_length' => 'Must not exceed :max characters.',
        'numeric' => 'Must be a number.',
        'date' => 'Please enter a valid date.',
        'phone' => 'Please enter a valid phone number.',
        'unique' => 'This value is already taken.',
        'confirm' => 'Values do not match.',
    ],
    
    // ========================
    // Time/Date
    // ========================
    'time' => [
        'just_now' => 'Just now',
        'minutes_ago' => 'minutes ago',
        'hours_ago' => 'hours ago',
        'days_ago' => 'days ago',
        'weeks_ago' => 'weeks ago',
        'months_ago' => 'months ago',
        'years_ago' => 'years ago',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'tomorrow' => 'Tomorrow',
    ],
    
    // ========================
    // Footer
    // ========================
    'footer' => [
        'copyright' => '© :year E-BHM Connect. All rights reserved.',
        'powered_by' => 'Powered by Barangay Bacong Health Center',
        'all_rights_reserved' => 'All Rights Reserved.',
        'about' => 'About',
        'privacy' => 'Privacy',
        'help' => 'Help',
    ],
    
    // ========================
    // App
    // ========================
    'app' => [
        'tagline' => 'Health Management System',
    ],
    
    // ========================
    // Roles
    // ========================
    'roles' => [
        'superadmin' => 'Super Admin',
        'admin' => 'Administrator',
        'bhw' => 'Health Worker',
        'patient' => 'Patient',
        'guest' => 'Guest',
    ],
    
    // ========================
    // Dialogs
    // ========================
    'dialogs' => [
        'confirm_delete_title' => 'Are you sure?',
        'confirm_delete_text' => 'You won\'t be able to revert this!',
        'yes_delete' => 'Yes, delete it!',
        'cancel' => 'Cancel',
        'deleted' => 'Deleted!',
        'deleted_text' => 'The item has been deleted.',
    ],
    
    // ========================
    // Chatbot
    // ========================
    // Chatbot settings merged into main chatbot key above
    // 'chatbot' => [
    //     'greeting' => 'Hi! I\'m Gabby. How can I help you today?',
    //     'placeholder' => 'Ask a question...',
    // ],
    
    // ========================
    // Database Backup
    // ========================
    'backup' => [
        'title' => 'Database Backup & Restore',
        'description' => 'Create and manage database backups',
        'create_backup' => 'Create Backup',
        'restore_backup' => 'Restore from Backup',
        'restore_now' => 'Restore Now',
        'download_backup' => 'Download',
        'delete_backup' => 'Delete',
        'existing_backups' => 'Existing Backups',
        'filename' => 'Filename',
        'size' => 'Size',
        'created_at' => 'Created',
        'select_file' => 'Select SQL Backup File',
        'file_hint' => 'Only .sql files are accepted',
        'no_backups' => 'No backups found. Create your first backup!',
        'backup_created' => 'Backup created successfully!',
        'restore_success' => 'Database restored successfully!',
        'confirm_restore' => 'WARNING: This will overwrite all current data! Are you sure you want to restore from this backup?',
        'confirm_delete' => 'Are you sure you want to delete this backup?',
    ],
    
    // ========================
    // Health Records
    // ========================
    'health_records' => [
        'title' => 'Health Records Dashboard',
        'subtitle' => 'Comprehensive health tracking for barangay residents',
        'total_records' => 'Total Records',
        'pregnancies' => 'Pregnancies',
        'births' => 'Births',
        'deaths' => 'Deaths',
        'chronic' => 'Chronic',
        'tb_cases' => 'TB Cases',
        'wra' => 'WRA',
        'view_all' => 'View All',
        'records' => 'Records',
        
        // Record Types
        'pregnancy_tracking' => 'Pregnancy Tracking',
        'pregnancy_desc' => 'Monitor pregnant women from identification to delivery outcome',
        'child_care' => 'Child Care (12-59 Months)',
        'child_care_desc' => 'Track child immunization and supplementation records',
        'natality' => 'Natality Records',
        'natality_desc' => 'Register and track all birth records in the barangay',
        'mortality' => 'Mortality Records',
        'mortality_desc' => 'Document and track death records with cause analysis',
        'chronic_diseases' => 'Hypertensive & Diabetic',
        'chronic_desc' => 'Masterlist of patients with chronic conditions and medications',
        'ntp_tracking' => 'NTP Client Monitoring',
        'ntp_desc' => 'Tuberculosis program tracking with treatment progress',
        'wra_tracking' => 'WRA Tracking',
        'wra_desc' => 'Women of Reproductive Age tracking with family planning',
        
        // Quick Actions
        'quick_actions' => 'Quick Actions',
        'add_pregnancy' => 'Add Pregnancy',
        'add_child' => 'Add Child',
        'add_birth' => 'Register Birth',
        'add_mortality' => 'Record Mortality',
    ],
];
