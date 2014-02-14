<?php

/**
 * a script to notify you when there is a new golden medal for a specific country
 *
 * @author Martin Schurig <hello@schurig.pw>
 * @version 0.5
 * @copyright (c) 14-Feb-2014, Martin Schurig
 * @link http://martinschurig.com, Martin Schurig
 * @license http://www.gnu.org/licenses/ GNU General Public License, version 3 (GPL-3.0)
 */

  class Gold {

    // ****************************/
    // general Settings
    private $file = './gold.txt'; // enter the path including the filename that will contains the number of gold medals from the last fetch
    private $source = 'http://www.sochi2014.com/en/team-germany'; // the url of the team page where the gold medals are displayed [number]

    // notification Settings
    private $pushover = true; // send Pushover Notifications?
    private $pushover_token = 'ENTER YOUR PUSHOVER TOKEN HERE';
    private $pushover_user = 'ENTER YOUR PUSHOVER USER-KEY HERE';
    private $pushover_title = '++ NEW MEDAL!! FOR GERMANY ++';
    private $pushover_message = "Germany just won a new gold-medal!! :)\nNow they have a total of: %=goldCount=%"; // %=goldCount=% stands for the total count of medals

    private $email = true; // send E-Mail Notifications?
    private $email_recipient = 'ENTER YOUR EMAIL ADRESS HERE';
    private $email_subject = '++ NEW MEDAL!! FOR GERMANY ++';
    private $email_content = "Germany just won a new gold-medal!! :)\nNow they have a total of: %=goldCount=%"; // %=goldCount=% stands for the total count of medals
    // ****************************/



    private $current = false;
    private $saved = false;

    function __construct(){

      if(!$this->getMedalCount(true)) {

        die('<b>error:</b> something went wrong while fetching the count of gold medals on source');
      }

      if(!$this->getMedalCount()) {

        die('<b>error:</b> something went wrong while fetching the count of gold medals from the file');
      }

      if($this->saved < $this->current) {

        $this->notify();
        $this->saveCountToFile();

      } else {

        die('no new gold medal :(');
      }

      echo 'New Medal, notified, updated local file';
      exit;
    }

    private function getMedalCount($new = false) {

      if($new) { // get current medal count from source

        $source = file_get_contents($this->source);

        if (preg_match( '/<li class="medal gold"><span class="outer">(.*?)<\/span><\/li>/s', $source, $matches)) {

            $this->current = $matches[1];
            return true;

        } else {

            return false;
        }

      } else { // get old medal count from file

        $content = file_get_contents($this->file);

        if($content) {

          $this->saved = $content;
          return true;

        } else {

          return false;
        }
      }

      return false;
    }

    private function saveCountToFile() {

      $handle = fopen($this->file, 'w') or die('Cannot open file: ' . $this->file);

      if(!fwrite($handle, $this->current)) {

        die('cannot write the file');
      }

      fclose($handle);

      return true;
    }

    private function notify() {

      if($this->pushover) {

        if (strpos($this->pushover_message,'%=goldCount=%')) {

            $message = str_replace("%=goldCount=%", $this->current, $this->pushover_message);

            $this->pushover_message = $message;
        }

        curl_setopt_array($ch = curl_init(), array(
        CURLOPT_URL => "https://api.pushover.net/1/messages.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => array(
          "token" => $this->pushover_token,
          "user" => $this->pushover_user,
          "title" => $this->pushover_title,
          "message" => $this->pushover_message,
        )));
        curl_exec($ch);
        curl_close($ch);
      }

      if($this->email) {

        if (strpos($this->email_content,'%=goldCount=%')) {

            $message = str_replace("%=goldCount=%", $this->current, $this->email_content);

            $this->email_content = $message;
        }

        if(!mail($this->email_recipient, $this->email_subject, $this->email_content)) {

          echo '<b>error:</b> failed sending email';
        }
      }

      return true;
    }
  }

  $medal = new Gold;

?>