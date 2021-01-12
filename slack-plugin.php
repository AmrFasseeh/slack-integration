<?php
/**
* Plugin Name: Slack Plugin
* Plugin URI: http://localhost/slack-plugin/
* Description: This is a plugin to view and create slack channels.
* Version: 1.0
* Author: Amr Fasseeh
* Author URI: http://localhost/slack-plugin/
**/


function myplugin_register_settings() {
    add_option( 'SLACK_COMMAND_TOKEN', 'Enter API Token');
    register_setting( 'myplugin_options_group', 'SLACK_COMMAND_TOKEN', 'myplugin_callback' );
    add_option( 'channel', '');
    register_setting( 'myplugin_options_group', 'channel', 'myplugin_callback' );
 }
 add_action( 'admin_init', 'myplugin_register_settings' );

 function myplugin_register_options_page() {
    add_options_page('Page Title', 'Slack Plugin', 'manage_options', 'myplugin', 'myplugin_options_page');
  }
  add_action('admin_menu', 'myplugin_register_options_page');

  
  
  function view_channels() {
    $channels = array();
    $ch = curl_init("https://slack.com/api/conversations.list");
    $data = http_build_query([
        "token" => get_option('SLACK_COMMAND_TOKEN'),
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result);
    

  foreach ($result->{'channels'} as $channel) {
    $channels[] = $channel->{'name'};
  }
  return $channels;
  }


function createChannel() {
  $ch = curl_init("https://slack.com/api/conversations.create");
  $data = http_build_query([
      "token" => get_option('SLACK_COMMAND_TOKEN'),
      "name" => get_option('channel'),
  ]);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);
}


function myplugin_options_page()
{
?>
  <div>
  <?php screen_icon(); ?>
  <h2>Slack Plugin</h2>
  <form method="post" action="options.php" onsubmit="<?php createChannel() ?>">
  <?php settings_fields( 'myplugin_options_group' ); ?>
  <table>
  <tr valign="top">
  <th scope="row"><label for="SLACK_COMMAND_TOKEN">API Token: </label></th>
  <td><input type="text" id="SLACK_COMMAND_TOKEN" name="SLACK_COMMAND_TOKEN" value="<?php echo get_option('SLACK_COMMAND_TOKEN'); ?>" /></td>
  </tr>
  </table>
  <table>
  <tr valign="top">
  <th scope="row"><label for="channel">Channel Name: </label></th>
  <td><input type="text" id="channel" name="channel" placeholder="Enter Channel name" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  <?php 
  $channels = view_channels();
  if($channels != null){ ?>
  <table>
        <thead>
        <tr>
        <th>Channel Name</th>
        </tr>
        </thead>
        <tbody>
        <?php
    foreach($channels as $channel){
    ?>
      
        <tr>
        <td>
         <?php echo $channel; ?>
        </td>
        </tr>
        
    <?php
    }
    ?>
    </tbody>
      </table>
      <?php
  }
  ?>  
  </div>
<?php
}