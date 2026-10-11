<?php
/*
  The Feedmachine Solution

  Generate feeds for any product search engine, e.g. Google Product Search, PriceGrabber, BizRate,
  DealTime, mySimon, Shopping.com, Yahoo! Shopping, PriceRunner.
  
  Version 6
  
  Updated and refractored for Phoenixcart by @raiwa
  raiwa@phoenixcartaddons.com
  www.phoenixcartaddons.com

  Copyright (c) 2023 Rainer Schmied

  Released under commercial License

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org
  
  Copyright (c) 2021 Phoenix Cart

  copyright Copyright 2005-, Lech Madrzyk
  original author Lech Madrzyk
 */

  $always_valid_actions = ['update', 'install', 'uninstall'];
  require('includes/application_top.php');

  require 'includes/segments/process_action.php';

  // db installation
  if (!defined('FM_SAVE_LOCATION')) {
    require('includes/template_top.php');
    echo $Admin->button('Install/Update Feedmachine configurations and database table', 'fas fa-cogs', 'btn-success btn-lg btn-block my-5', $Admin->link('feedmachine_admin.php', ['action' => 'install']));
  } else {

    ini_set('display_errors', 'On');

    $installation_path = dirname(__FILE__) . '/feedmachine/';

    $catalog_path = DIR_FS_CATALOG;

    require_once($installation_path . 'feedmachine_loader.php');

    $feeds_location_web = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . FM_SAVE_LOCATION;
    $feeds_location_fs = $catalog_path . FM_SAVE_LOCATION;

    require('includes/template_top.php');

?>
<script><!--
function runFeedmachine() {
  window.open('<?php echo $Admin->link('feedmachine/feedmachine_auto.php', 'force_update=1'); ?>','feedmachine_console','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=700,height=400,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
  <div id="spiffycalendar" class="text"></div>

    <div class="row">
      <div class="col">
        <h1 class="display-4 my-2 ml-4"><?= 'Feedmachine Admin' ?></h1>
      </div>
      <div class="col text-center align-self-center my-2 mr-3">
        <input type="button" class="btn btn-secondary btn-block" onclick="runFeedmachine()" value="Generate and Upload Feeds Now">
      </div>
    </div>
    <div id="content-text" class="col">
      <?= new Form('feedmachine_admin', $Admin->link('feedmachine_admin.php')->set_parameter('action', 'update')); ?>
        <table class="table table-responsive table-sm table-striped">
          <thead class="thead-dark">
            <tr>
              <th>Config Filename</th>
              <th>Feed Filename</th>
              <th>View</th>
              <th>Language</th>
              <th>Currency</th>
              <th>Country</th>
              <th>URL Params</th>
              <th>FTP</th>
              <th>FTP Server</th>
              <th>FTP Port</th>
              <th>FTP Path</th>
              <th>FTP Username</th>
              <th>FTP Password</th>
              <th>Period</th>
            </tr>
          </thead>
<?php
      //Build Languages Array
      $languages_info = [];
      foreach (language::load_all() as $l) {
        $languages_info[] = ['id'   => $l['code'],
                             'text' => $l['code']];
      }

      //Build Currencies Array
      $currencies_info = [];
      foreach (Guarantor::ensure_global('currencies')->currencies as $key => $value) {
        $currencies_info[] = ['id'   => $key,
                              'text' => $key];
      }

      //Build Countries Array
      $countries_info = [];
      $countries_info = $GLOBALS['db']->fetch_all("SELECT countries_iso_code_2 as id, countries_iso_code_2 as text FROM countries");

      foreach( $feeds as $config_filename => $feed ) {
?>
        <tbody>
          <tr>
            <td style="width: 10%"><small><?= current(explode('.php', $config_filename)) ?></small></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][filename]', ['value' => $feed['filename'], 'class' => 'form-control form-control-sm']) ?></td>
            <td class="text-center align-middle"><?= file_exists($feeds_location_fs . $feed['filename']) ? '<a href="' . $feeds_location_web . $feed['filename'] . '" target="_blank"><i style="cursor: pointer;" class="far fa-eye text-primary"></i></a>' : '&nbsp;' ?></td>
            <td><?= (new Select('feeds_update[' . $config_filename . '][language_code]', $languages_info, ['class' => 'form-control form-control-sm']))->set_selection($feed['language_code']) ?></td>
            <td><?= (new Select('feeds_update[' . $config_filename . '][currency_code]', $currencies_info, ['class' => 'form-control form-control-sm']))->set_selection($feed['currency_code']) ?></td>
            <td><?= (new Select('feeds_update[' . $config_filename . '][countries_iso_2]', $countries_info, ['class' => 'form-control form-control-sm']))->set_selection($feed['countries_iso_2']) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][url_parameters]', ['value' => $feed['url_parameters'], 'class' => 'form-control form-control-sm']) ?></td>
            <td style="width: 6%"><?= (new Select('feeds_update[' . $config_filename . '][ftp_status]', [['id' => '0', 'text' => 'Off'], ['id' => '1', 'text' => 'FTP'], ['id' => '2', 'text' => 'SFTP']], ['class' => 'form-control form-control-sm']))->set_selection($feed['ftp_status']?? null) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][ftp_server]', ['value' => $feed['ftp_server']?? '', 'class' => 'form-control form-control-sm']) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][ftp_port]', ['value' => ((isset($feed['ftp_port']) && $feed['ftp_port'] != 0)? $feed['ftp_port'] : ''), 'class' => 'form-control form-control-sm']) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][ftp_path]', ['value' => $feed['ftp_path']?? '', 'class' => 'form-control form-control-sm']) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][ftp_username]', ['value' => $feed['ftp_username']?? '', 'class' => 'form-control form-control-sm']) ?></td>
            <td><?= new Input('feeds_update[' . $config_filename . '][ftp_password]', ['value' => $feed['ftp_password']?? '', 'class' => 'form-control form-control-sm']) ?></td>
            <td style="width: 2%"><?= new Input('feeds_update[' . $config_filename . '][ftp_upload_period]', ['value' => $feed['ftp_upload_period']?? '', 'class' => 'form-control form-control-sm']) ?></td>
          </tr>
<?php
      }
?>
          </tbody>
        </table>

        <div class="col text-right align-self-center">
          <?= new Button(IMAGE_UPDATE, 'fas fa-sync fa-fw', 'btn-light'); ?>
        </div>
      </form>

      <div class="row mt-5">
        <div class="col-8 alert alert-default">
          <p><b>File System Location of your feeds configuration directory (where to upload new feed configurations)</b><br><?= $installation_path . 'fm-feed-configs/'; ?></p>
          <p><b>Web Location of your feeds</b><br><?= $feeds_location_web; ?></p>
          <p><b>File System Location of your feeds</b><br><?= $feeds_location_fs; ?></p>
          <p><b>Automation</b><br>To automate feed generation and upload, create a cron job to run the following command every 2 hours:<br><br>php <?= $installation_path . 'feedmachine_auto.php'; ?><br><br>Feedmachine Auto will generate and upload your feeds when it detects that your catalog has been updated. It also features a period of grace to ensure that your feeds are not generated in the middle of a catalog update.</p>
          <p><b>Development</b><br>If you need help setting up more complex feed configurations or have other Phoenixcart development work, please visit my website <a target="_blank" href="http://www.phoenixcartaddons.com/">phoenixcartaddons.com</a>.</p>
        </div>
        <div class="col-4 alert alert-info">
          <p class="lead"><b>Key</b></p>
          <p>
            <b>Config Filename:</b> Filename of the feed configuration in <i>fm-feed-configs/</i> (displayed without the &quot;.php&quot; extension)<br>
            <b>Feed Filename:</b> Filename of the feed that is created from the configuration<br>
            <b>View:</b> Click on icon to view the feed (if it exists)<br>
            <b>Country:</b> This is used to determine the tax set-up of the feed.<br>
            <b>URL Params:</b> Additional parameters (in the form <i>var1=val1&var2=var2</i>) to add to the links output in the feed. E.g. for Google Analytics: <i>utm_source=[SOURCE]&utm_medium=[MEDIUM]&utm_campaign=[CAMPAIGN]</i><br>
            <b>Period:</b> How often (in days) to re-upload a feed if there haven't been any changes. This can be set to prevent your feeds from expiring. <i>Applies to Feedmachine Auto only</i><br>
          </p>
        </div>
      </div>
    </div>
    <div class="col text-right align-self-center mt-5">
      <?= $Admin->button('Uninstall Feedmachine DB entries', 'fas fa-exclamation-triangle', 'btn-danger btn-sm', $Admin->link('feedmachine_admin.php', ['action' => 'uninstall']), ['onClick' => 'return validate();']) ?>
    </div>
<script>
function validate() {
  return confirm('Are you sure that you want to uninstall Feedmachine?');
}
</script>

<?php
  } // end check installation

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
