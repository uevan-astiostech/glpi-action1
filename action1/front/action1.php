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
    'software_deployment' => __('Software Management', 'action1'),
    'vulnerability_management' => __('Vulnerability Management', 'action1'),
];
?>

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
                case 'endpoint_details': // New case to handle individual endpoint details
                    include('endpoint_details.php');
                    break;
                case 'deploy': // New case to handle individual endpoint details
                    include('deploy.php');
                    break;
                case 'software_deployment':
                    include('software_deployment.php');
                    break;
                case 'vulnerability_management':
                    include('vulnerability_management.php');
                    break;
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
