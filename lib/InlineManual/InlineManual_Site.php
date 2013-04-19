<?php

class InlineManual_Site extends InlineManual {

  static public function fetchAllTopics() {
    $request = new InlineManual_ApiRequest('player/topics/', array('site_key' => InlineManual::$site_api_key));
    return $request->send();
  }

  static public function fetchTopic($id) {
    $request = new InlineManual_ApiRequest('player/topics/' . $id, array('site_key' => InlineManual::$site_api_key));
    return $request->send();
  }

}
