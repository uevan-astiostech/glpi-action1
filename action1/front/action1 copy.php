<?php
// Include GLPI's common header
include('../../../inc/includes.php');
include('../inc/session_manager.php');


// Check if the user has super admin rights
Session::checkRight('config', READ); 

// To be available when the plugin is not activated
Plugin::load('action1');

Html::header(__('Action1 Configuration', 'action1'), $_SERVER['PHP_SELF'], "tools", "action1");

// Define the menu items (these will be your tab links)
$menu_items = [
    'dashboard' => __('Dashboard', 'action1'),
    'endpoints' => __('Endpoints', 'action1'),
    // 'data_source' => __('Data Source', 'action1'),
    // 'script_library' => __('Script Library', 'action1'),
    // 'advance_setting' => __('Advanced Setting', 'action1'),
    // 'reports' => __('Reports', 'action1'),
    // 'software_repository' => __('Software Repository', 'action1'),
    'software_deployment' => __('Software Management', 'action1'),
    // 'policy_schedules' => __('Policy Schedules', 'action1'),
    // 'policy_instances' => __('Policy Instances', 'action1'),
    // 'action_template' => __('Action Template', 'action1'),
    // 'diagnostic_logging' => __('Diagnostic Logging', 'action1'),
    // 'license' => __('Licence', 'action1'),
    // 'security' => __('Security', 'action1'),
    // 'mfa' => __('Multi-Factor Authentication', 'action1'),
    // 'subscription' => __('Subscription', 'action1'),
    'vulnerability_management' => __('Vulnerability Management', 'action1'),
    // 'audit_trail' => __('Audit Trail', 'action1'),
];
?>

<!-- Inline CSS for sidebar layout -->
<!-- <style>
/* Set up the container for sidebar and content */
.action1-container {
    display: flex;
    height: 100vh; /* Full height */
}

/* Sidebar styling */
.action1-sidebar {
    width: 250px;
    background-color: #333;
    padding-top: 20px;
    display: flex;
    flex-direction: column;
}

.action1-sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    color: white;
    display: block;
}

.action1-sidebar a:hover {
    background-color: #575757;
}

.action1-sidebar .action1-active a {
    background-color: #4CAF50;
    font-weight: bold;
}

/* Content area next to the sidebar */
.action1-content {
    flex-grow: 1;
    padding: 20px;
    background-color: #f9f9f9;
}

/* Style the content area */
.action1-tab-content {
    padding: 20px;
    border: 1px solid #ccc;
    background-color: #fff;
}
</style> -->

<div class="action1-container">
    <!-- Create the sidebar menu -->
    <div class="action1-sidebar">
        <?php foreach ($menu_items as $key => $label): ?>
            <div class="action1-<?php echo $key; ?> <?php echo isset($_GET['page']) && $_GET['page'] == $key ? 'action1-active' : ''; ?>">
                <a href="?page=<?php echo $key; ?>"><?php echo $label; ?></a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Create the content area where forms will be displayed -->
    <div class="action1-content">
        <div class="action1-tab-content">
            <?php
            // Determine which page (tab) to load based on the query parameter
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

            // Load the corresponding form based on the selected menu item (tab)
            switch ($page) {
                case 'dashboard':
                    include('dashboard.php');
                    break;
                case 'endpoints':
                    include('endpoints.php');
                    break;
                // case 'data_source':
                //     include('data_source.php');
                //     break;
                // case 'script_library':
                //     include('script_library.php');
                //     break;
                // case 'advance_setting':
                //     include('advance_setting.php');
                //     break;
                // case 'reports':
                //     include('reports.php');
                //     break;
                // case 'software_repository':
                //     include('software_repository.php');
                //     break;
                case 'software_deployment':
                    include('software_deployment.php');
                    break;
                // case 'policy_schedules':
                //     include('policy_schedules.php');
                //     break;
                // case 'policy_instances':
                //     include('policy_instances.php');
                //     break;
                // case 'action_template':
                //     include('action_template.php');
                //     break;
                // case 'diagnostic_logging':
                //     include('diagnostic_logging.php');
                //     break;
                // case 'license':
                //     include('license.php');
                //     break;
                // case 'security':
                //     include('security.php');
                //     break;
                // case 'mfa':
                //     include('mfa.php');
                //     break;
                // case 'subscription':
                //     include('subscription.php');
                //     break;
                case 'vulnerability_management':
                    include('vulnerability_management.php');
                    break;
                // case 'audit_trail':
                //     include('audit_trail.php');
                //     break;
                // Add cases for all other pages
                default:
                    echo __('Page not found', 'action1');
            }
            ?>
        </div>
    </div>
</div>

<?php
// Include GLPI's common footer
Html::footer();
?>
