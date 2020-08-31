<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
 exit;

class OAuth2Conf {
  public const OATH_CLIENTID = 'SEYFRrZ9JATgNndYE99xeKVCNe6r2Qkq6sQlzXYEq5udniBqPzHmWTGGs5kMfFsa';
  public const OATH_CLIENT_SECRET = '1WTiTXU8dcik1CNMS13zsYOlI9kUBcMT4v9N7sEw0crM6aoloftQv69L2QSyRbwN';
  public const OATH_AUTHURL = 'https://cloud.mogul.network/apps/oauth2/authorize';
  public const OATH_PROVIDER = 'cloud.mogul.network';
  public const OATH_TOKENURL = 'https://cloud.mogul.network/apps/oauth2/api/v1/token';
  public const OATH_DATAURL = 'https://cloud.mogul.network/ocs/v1.php/cloud/users';
}
