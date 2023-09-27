<?php
class WooPanel_Updater{

    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $username; // GitHub username
    private $repo; // GitHub repo name
    private $pluginFile; // __FILE__ of our plugin
    private $githubAPIResult; // holds data from GitHub
    private $accessToken; // GitHub private repo token

    function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' ) {
        add_filter( "pre_set_site_transient_update_plugins", array( $this, "modify_plugins_transient" ) );

        $this->pluginFile = $pluginFile;
        $this->username = $gitHubUsername;
        $this->repo = $gitHubProjectName;
        $this->accessToken = $accessToken;

        mang($this->pluginFile);
    }
 
    // Get information regarding our plugin from WordPress
    private function initPluginData() {
        $this->slug = plugin_basename( $this->pluginFile );
        $this->pluginData = get_plugin_data( $this->pluginFile );
    }
 
    // Get information regarding our plugin from GitHub
    private function getRepoReleaseInfo() {
        // code here
    }
 
    // Push in plugin version information to get the update notification
    public function modify_plugins_transient( $transient ) {

        // bail early if no response (error)
        if( !isset($transient->response) ) return $transient;


        return $transient;
    }
 
    // Push in plugin version information to display in the details lightbox
    public function setPluginInfo( $false, $action, $response ) {
        // code ehre
        return $response;
    }
 
    // Perform additional actions to successfully install our plugin
    public function postInstall( $true, $hook_extra, $result ) {
        // code here
        return $result;
    }
}