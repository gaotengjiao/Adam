<?php
return array (
  'app' => 'Admin',
  'model' => 'Face',
  'action' => 'index',
  'data' => '',
  'type' => '1',
  'status' => '1',
  'name' => '颜值专属活动管理',
  'icon' => '',
  'remark' => '',
  'listorder' => '0',
  'children' => 
  array (
    array (
      'app' => 'Admin',
      'model' => 'Face',
      'action' => 'dated',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '过期活动',
      'icon' => '',
      'remark' => '过期活动',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Face',
      'action' => 'Faceadd',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '添加新活动',
      'icon' => '',
      'remark' => '添加新活动',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Face',
      'action' => 'activity',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '未过期活动',
      'icon' => '',
      'remark' => '未过期活动',
      'listorder' => '0',
    ),
  ),
);