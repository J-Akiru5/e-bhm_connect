<?php
/**
 * Tagalog Language File
 * E-BHM Connect - Sistema ng Pamamahala ng Kalusugan ng Barangay
 */

return [
    // ========================
    // General / Common
    // ========================
    'app_name' => 'E-BHM Connect',
    'app_tagline' => 'Sistema ng Pamamahala ng Kalusugan ng Barangay',
    'welcome' => 'Maligayang Pagdating',
    'hello' => 'Kumusta',
    'loading' => 'Naglo-load...',
    'please_wait' => 'Mangyaring maghintay...',
    'success' => 'Tagumpay',
    'error' => 'May Mali',
    'warning' => 'Babala',
    'info' => 'Impormasyon',
    'confirm' => 'Kumpirmahin',
    'cancel' => 'Kanselahin',
    'save' => 'I-save',
    'save_changes' => 'I-save ang mga Pagbabago',
    'delete' => 'Tanggalin',
    'edit' => 'I-edit',
    'view' => 'Tingnan',
    'add' => 'Idagdag',
    'create' => 'Lumikha',
    'update' => 'I-update',
    'search' => 'Maghanap',
    'filter' => 'Salain',
    'clear' => 'Burahin',
    'reset' => 'I-reset',
    'close' => 'Isara',
    'back' => 'Bumalik',
    'next' => 'Susunod',
    'previous' => 'Nakaraan',
    'submit' => 'Ipasa',
    'yes' => 'Oo',
    'no' => 'Hindi',
    'all' => 'Lahat',
    'none' => 'Wala',
    'select' => 'Pumili',
    'required' => 'Kinakailangan',
    'optional' => 'Opsyonal',
    'actions' => 'Aksyon',
    'status' => 'Katayuan',
    'date' => 'Petsa',
    'time' => 'Oras',
    'details' => 'Mga Detalye',
    'description' => 'Paglalarawan',
    'name' => 'Pangalan',
    'email' => 'Email',
    'phone' => 'Telepono',
    'address' => 'Tirahan',
    'no_data' => 'Walang datos na makita',
    'no_results' => 'Walang nahanap na resulta',
    
    // ========================
    // Navigation
    // ========================
    'nav' => [
        'dashboard' => 'Dashboard',
        'patients' => 'Mga Pasyente',
        'messages' => 'Mga Mensahe',
        'inventory' => 'Imbentaryo',
        'inventory_categories' => 'Mga Kategorya ng Imbentaryo',
        'announcements' => 'Mga Anunsyo',
        'reports' => 'Mga Ulat',
        'programs' => 'Mga Programa',
        'bhw_users' => 'Mga BHW User',
        'bhw_management' => 'Pamamahala ng BHW',
        'settings' => 'Mga Setting',
        'account_settings' => 'Mga Setting ng Account',
        'app_settings' => 'Mga Setting ng App',
        'profile' => 'Profile',
        'logout' => 'Mag-logout',
        'login' => 'Mag-login',
        'register' => 'Magrehistro',
        'home' => 'Home',
        'admin' => 'Admin',
        'main_menu' => 'Pangunahing Menu',
        'inventory_section' => 'Imbentaryo',
        'content_section' => 'Nilalaman',
        'administration' => 'Administrasyon',
        'toggle_sidebar' => 'I-toggle ang Sidebar',
        'view_public_site' => 'Tingnan ang Public Site',
    ],
    
    // ========================
    // Authentication
    // ========================
    'auth' => [
        'login' => 'Mag-login',
        'logout' => 'Mag-logout',
        'register' => 'Magrehistro',
        'forgot_password' => 'Nakalimutan ang Password?',
        'reset_password' => 'I-reset ang Password',
        'change_password' => 'Palitan ang Password',
        'current_password' => 'Kasalukuyang Password',
        'new_password' => 'Bagong Password',
        'confirm_password' => 'Kumpirmahin ang Password',
        'username' => 'Username',
        'password' => 'Password',
        'remember_me' => 'Tandaan Ako',
        'login_success' => 'Matagumpay na naka-login!',
        'login_failed' => 'Maling username o password.',
        'logout_success' => 'Ikaw ay naka-logout na.',
        'session_expired' => 'Nag-expire na ang iyong session. Mangyaring mag-login muli.',
        'unauthorized' => 'Hindi ka awtorisado na ma-access ang pahinang ito.',
        'account_pending' => 'Ang iyong account ay naghihintay ng pag-apruba.',
        'account_not_verified' => 'Mangyaring i-verify ang iyong email address.',
        'verification_sent' => 'Naipadala na ang verification email!',
        'verification_success' => 'Matagumpay na na-verify ang email!',
        'password_changed' => 'Matagumpay na napalitan ang password!',
        'password_mismatch' => 'Hindi tugma ang mga password.',
        'password_requirements' => 'Ang password ay dapat hindi bababa sa 8 karakter.',
    ],
    
    // ========================
    // Dashboard
    // ========================
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome_message' => 'Maligayang pagbabalik, :name!',
        'overview' => 'Buod',
        'quick_actions' => 'Mabilis na Aksyon',
        'recent_activity' => 'Kamakailang Aktibidad',
        'statistics' => 'Mga Istatistika',
        'total_patients' => 'Kabuuang Pasyente',
        'total_bhws' => 'Kabuuang BHW',
        'total_inventory' => 'Mga Item sa Imbentaryo',
        'sms_sent' => 'Naipadala na SMS',
        'sms_failed' => 'Nabigong SMS',
        'sms_pending' => 'Naghihintay na SMS',
        'low_stock_items' => 'Mababang Stock na Item',
        'pending_approvals' => 'Naghihintay na Pag-apruba',
        'recent_registrations' => 'Kamakailang Rehistrasyon',
        'recent_visits' => 'Kamakailang Bisita',
        'inventory_overview' => 'Buod ng Imbentaryo',
        'audit_logs' => 'Mga Audit Log',
        'sms_delivery_status' => 'Status ng Pagpapadala ng SMS',
        'patient_registration_trends' => 'Trend ng Rehistrasyon ng Pasyente',
        'medicine_stock_levels' => 'Antas ng Stock ng Gamot',
    ],
    
    // ========================
    // Patients
    // ========================
    'patients' => [
        'title' => 'Mga Pasyente',
        'add_patient' => 'Magdagdag ng Pasyente',
        'edit_patient' => 'I-edit ang Pasyente',
        'view_patient' => 'Tingnan ang Pasyente',
        'patient_list' => 'Listahan ng Pasyente',
        'patient_details' => 'Mga Detalye ng Pasyente',
        'full_name' => 'Buong Pangalan',
        'birthdate' => 'Petsa ng Kapanganakan',
        'age' => 'Edad',
        'sex' => 'Kasarian',
        'male' => 'Lalaki',
        'female' => 'Babae',
        'contact_number' => 'Numero ng Telepono',
        'medical_history' => 'Kasaysayang Medikal',
        'vitals' => 'Mga Vital',
        'visits' => 'Mga Bisita',
        'medications' => 'Mga Gamot',
        'no_patients' => 'Walang nahanap na pasyente.',
        'patient_added' => 'Matagumpay na naidagdag ang pasyente!',
        'patient_updated' => 'Matagumpay na na-update ang pasyente!',
        'patient_deleted' => 'Matagumpay na natanggal ang pasyente!',
        'confirm_delete' => 'Sigurado ka bang gusto mong tanggalin ang pasyenteng ito?',
    ],
    
    // ========================
    // Inventory
    // ========================
    'inventory' => [
        'title' => 'Imbentaryo ng Gamot at Supplies',
        'add_item' => 'Magdagdag ng Item',
        'edit_item' => 'I-edit ang Item',
        'item_name' => 'Pangalan ng Item',
        'category' => 'Kategorya',
        'quantity' => 'Dami',
        'unit' => 'Yunit',
        'stock_level' => 'Antas ng Stock',
        'in_stock' => 'May Stock',
        'low_stock' => 'Mababang Stock',
        'out_of_stock' => 'Wala nang Stock',
        'last_restock' => 'Huling Restock',
        'dispense' => 'Ibigay',
        'dispense_medicine' => 'Ibigay ang Gamot',
        'quantity_dispensed' => 'Dami na Ibinigay',
        'dispensed_to' => 'Ibinigay Kay',
        'dispensed_by' => 'Ibinigay Ni',
        'dispense_success' => 'Matagumpay na naibigay ang gamot!',
        'insufficient_stock' => 'Hindi sapat ang stock.',
        'item_added' => 'Matagumpay na naidagdag ang item!',
        'item_updated' => 'Matagumpay na na-update ang item!',
        'item_deleted' => 'Matagumpay na natanggal ang item!',
    ],
    
    // ========================
    // Announcements
    // ========================
    'announcements' => [
        'title' => 'Mga Anunsyo',
        'add_announcement' => 'Magdagdag ng Anunsyo',
        'edit_announcement' => 'I-edit ang Anunsyo',
        'announcement_title' => 'Pamagat',
        'content' => 'Nilalaman',
        'posted_by' => 'Nai-post Ni',
        'posted_on' => 'Nai-post Noong',
        'no_announcements' => 'Wala pang mga anunsyo.',
        'announcement_added' => 'Matagumpay na naipaskil ang anunsyo!',
        'announcement_updated' => 'Matagumpay na na-update ang anunsyo!',
        'announcement_deleted' => 'Matagumpay na natanggal ang anunsyo!',
    ],
    
    // ========================
    // Settings
    // ========================
    'settings' => [
        'title' => 'Mga Setting',
        'account_settings' => 'Mga Setting ng Account',
        'app_settings' => 'Mga Setting ng Aplikasyon',
        'preferences' => 'Mga Kagustuhan',
        'appearance' => 'Hitsura',
        'theme' => 'Tema',
        'theme_light' => 'Light Mode',
        'theme_dark' => 'Dark Mode',
        'theme_system' => 'Default ng Sistema',
        'light_mode' => 'Maliwanag',
        'dark_mode' => 'Madilim',
        'language' => 'Wika',
        'language_en' => 'English',
        'language_tl' => 'Tagalog',
        'notifications' => 'Mga Notification',
        'email_notifications' => 'Mga Email Notification',
        'sms_notifications' => 'Mga SMS Notification',
        'profile_picture' => 'Larawan sa Profile',
        'upload_avatar' => 'Mag-upload ng Avatar',
        'remove_avatar' => 'Tanggalin ang Avatar',
        'personal_info' => 'Personal na Impormasyon',
        'profile_information' => 'Impormasyon ng Profile',
        'security' => 'Seguridad',
        'settings_saved' => 'Matagumpay na na-save ang mga setting!',
        'preferences_saved' => 'Matagumpay na na-save ang mga kagustuhan!',
        'save_preferences' => 'I-save ang mga Kagustuhan',
        'profile_updated' => 'Matagumpay na na-update ang profile!',
        'general' => 'Pangkalahatan',
        'features' => 'Mga Feature',
        'maintenance' => 'Maintenance',
        'maintenance_mode' => 'Maintenance Mode',
        'enable_maintenance' => 'I-enable ang Maintenance Mode',
        'maintenance_message' => 'Mensahe sa Maintenance',
        'manage_your_account' => 'Pamahalaan ang iyong mga setting at kagustuhan ng account',
        'account_created' => 'Petsa ng Paggawa ng Account',
        'last_login' => 'Huling Pag-login',
        'verified' => 'Na-verify',
        'pending' => 'Naghihintay ng Beripikasyon',
        'error_required_fields' => 'Mangyaring punan ang lahat ng kinakailangang field.',
        'incorrect_password' => 'Mali ang kasalukuyang password.',
    ],
    
    // ========================
    // Notifications
    // ========================
    'notifications' => [
        'title' => 'Mga Notification',
        'mark_read' => 'Markahan bilang Nabasa',
        'mark_all_read' => 'Markahan Lahat bilang Nabasa',
        'delete_notification' => 'Tanggalin ang Notification',
        'no_notifications' => 'Walang mga notification',
        'new_notifications' => 'Mayroon kang :count bagong notification',
        'view_all' => 'Tingnan Lahat ng Notification',
    ],
    
    // ========================
    // BHW Users (Admin)
    // ========================
    'bhw' => [
        'title' => 'Mga BHW User',
        'bhw_list' => 'Listahan ng BHW',
        'add_bhw' => 'Magdagdag ng BHW',
        'edit_bhw' => 'I-edit ang BHW',
        'bhw_details' => 'Mga Detalye ng BHW',
        'unique_id' => 'BHW ID',
        'assigned_area' => 'Itinalagang Lugar',
        'training_cert' => 'Sertipiko ng Pagsasanay',
        'employment_status' => 'Status ng Trabaho',
        'account_status' => 'Status ng Account',
        'pending' => 'Naghihintay',
        'verified' => 'Na-verify',
        'approved' => 'Na-apruba',
        'approve' => 'Aprubahan',
        'reject' => 'Tanggihan',
        'approve_bhw' => 'Aprubahan ang Account ng BHW',
        'reject_bhw' => 'Tanggihan ang Account ng BHW',
        'bhw_approved' => 'Matagumpay na na-apruba ang account ng BHW!',
        'bhw_rejected' => 'Tinanggihan ang account ng BHW.',
        'superadmin' => 'Super Admin',
        'regular_bhw' => 'Regular na BHW',
    ],
    
    // ========================
    // Reports
    // ========================
    'reports' => [
        'title' => 'Mga Ulat',
        'generate_report' => 'Lumikha ng Ulat',
        'patient_list_report' => 'Ulat ng Listahan ng Pasyente',
        'inventory_report' => 'Ulat ng Stock ng Imbentaryo',
        'chronic_disease_report' => 'Ulat ng Chronic Disease',
        'download_pdf' => 'I-download ang PDF',
        'export_csv' => 'I-export sa CSV',
    ],
    
    // ========================
    // Programs
    // ========================
    'programs' => [
        'title' => 'Mga Programang Pangkalusugan',
        'add_program' => 'Magdagdag ng Programa',
        'edit_program' => 'I-edit ang Programa',
        'program_name' => 'Pangalan ng Programa',
        'start_date' => 'Petsa ng Simula',
        'end_date' => 'Petsa ng Wakas',
        'active' => 'Aktibo',
        'completed' => 'Nakumpleto',
        'upcoming' => 'Paparating',
    ],
    
    // ========================
    // Chatbot
    // ========================
    'chatbot' => [
        'title' => 'Makipag-usap kay Gabby',
        'assistant_name' => 'Gabby',
        'placeholder' => 'I-type ang iyong mensahe...',
        'send' => 'Ipadala',
        'typing' => 'Nagta-type si Gabby...',
        'welcome' => 'Kumusta! Ako si Gabby, ang iyong health assistant. Paano kita matutulungan ngayon?',
        'error' => 'Paumanhin, may naganap na error. Mangyaring subukan muli.',
    ],
    
    // ========================
    // Audit Logs
    // ========================
    'audit' => [
        'title' => 'Mga Audit Log',
        'user' => 'User',
        'action' => 'Aksyon',
        'entity' => 'Entity',
        'ip_address' => 'IP Address',
        'timestamp' => 'Timestamp',
        'details' => 'Mga Detalye',
        'login' => 'Nag-login ang User',
        'logout' => 'Nag-logout ang User',
        'create' => 'Ginawa',
        'update' => 'Na-update',
        'delete' => 'Tinanggal',
        'view' => 'Tiningnan',
    ],
    
    // ========================
    // Validation Messages
    // ========================
    'validation' => [
        'required' => 'Ang field na ito ay kinakailangan.',
        'email' => 'Mangyaring maglagay ng valid na email address.',
        'min_length' => 'Dapat ay hindi bababa sa :min karakter.',
        'max_length' => 'Hindi dapat lumampas sa :max karakter.',
        'numeric' => 'Dapat ay numero.',
        'date' => 'Mangyaring maglagay ng valid na petsa.',
        'phone' => 'Mangyaring maglagay ng valid na numero ng telepono.',
        'unique' => 'Ang value na ito ay ginagamit na.',
        'confirm' => 'Hindi tugma ang mga value.',
    ],
    
    // ========================
    // Time/Date
    // ========================
    'time' => [
        'just_now' => 'Ngayon lang',
        'minutes_ago' => ':count minuto ang nakakaraan',
        'hours_ago' => ':count oras ang nakakaraan',
        'days_ago' => ':count araw ang nakakaraan',
        'weeks_ago' => ':count linggo ang nakakaraan',
        'months_ago' => ':count buwan ang nakakaraan',
        'years_ago' => ':count taon ang nakakaraan',
        'today' => 'Ngayon',
        'yesterday' => 'Kahapon',
        'tomorrow' => 'Bukas',
        'minutes_ago' => 'minuto ang nakakaraan',
        'hours_ago' => 'oras ang nakakaraan',
        'days_ago' => 'araw ang nakakaraan',
    ],
    
    // ========================
    // Footer
    // ========================
    'footer' => [
        'copyright' => '© :year E-BHM Connect. Lahat ng karapatan ay nakalaan.',
        'powered_by' => 'Pinapagana ng Barangay Bacong Health Center',
        'all_rights_reserved' => 'Lahat ng Karapatan ay Nakalaan.',
        'about' => 'Tungkol Sa',
        'privacy' => 'Privacy',
        'help' => 'Tulong',
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
        'admin' => 'Tagapangasiwa',
        'bhw' => 'Health Worker',
        'patient' => 'Pasyente',
        'guest' => 'Bisita',
    ],
    
    // ========================
    // Dialogs
    // ========================
    'dialogs' => [
        'confirm_delete_title' => 'Sigurado ka ba?',
        'confirm_delete_text' => 'Hindi mo na ito maibabalik!',
        'yes_delete' => 'Oo, tanggalin!',
        'cancel' => 'Kanselahin',
        'deleted' => 'Tinanggal na!',
        'deleted_text' => 'Ang item ay natanggal na.',
    ],
    
    // ========================
    // Chatbot
    // ========================
    'chatbot' => [
        'greeting' => 'Kumusta! Ako si Gabby. Paano kita matutulungan ngayon?',
        'placeholder' => 'Magtanong...',
    ],
];
