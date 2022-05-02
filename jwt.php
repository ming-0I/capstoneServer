<?php
/**
 * PHP  jwt
 */
class Jwt {

  //  
  private static $header=array(
    'alg'=>'HS256', //  signature   
    'typ'=>'JWT'  //  
  );

  //  HMAC             
  private static $key='123456';


  /**
   *   jwt token
   * @param array $payload jwt           
   * [
   * 'iss'=>'jwt_admin', // JWT    
   * 'iat'=>time(), //    
   * 'exp'=>time()+7200, //    
   * 'nbf'=>time()+60, //           Token
   * 'sub'=>'www.admin.com', //     
   * 'jti'=>md5(uniqid('JWT').time()) // Token    
   * ]
   * @return bool|string
   */
  public static function getToken(array $payload)
  {
    if(is_array($payload))
    {
      $base64header=self::base64UrlEncode(json_encode(self::$header,JSON_UNESCAPED_UNICODE));
      $base64payload=self::base64UrlEncode(json_encode($payload,JSON_UNESCAPED_UNICODE));
      $token=$base64header.'.'.$base64payload.'.'.self::signature($base64header.'.'.$base64payload,self::$key,self::$header['alg']);
      return $token;
    }else{
      return false;
    }
  }


  /**
   *   token    ,    exp,nbf,iat  
   * @param string $Token      token
   * @return bool|string
   */
  public static function verifyToken(string $Token)
  {
    $tokens = explode('.', $Token);
    if (count($tokens) != 3)
      return false;

    list($base64header, $base64payload, $sign) = $tokens;

    //  jwt  
    $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
    if (empty($base64decodeheader['alg']))
      return false;

    //    
    if (self::signature($base64header . '.' . $base64payload, self::$key, $base64decodeheader['alg']) !== $sign)
      return false;

    $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

    //                 
    if (isset($payload['iat']) && $payload['iat'] > time())
      return false;

    //                 
    if (isset($payload['exp']) && $payload['exp'] < time())
      return false;

    // nbf          Token
    if (isset($payload['nbf']) && $payload['nbf'] > time())
      return false;

    return $payload;
  }




  /**
   * base64UrlEncode  https://jwt.io/  base64UrlEncode    
   * @param string $input         
   * @return string
   */
  private static function base64UrlEncode(string $input)
  {
    return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
  }

  /**
   * base64UrlEncode https://jwt.io/  base64UrlEncode    
   * @param string $input         
   * @return bool|string
   */
  private static function base64UrlDecode(string $input)
  {
    $remainder = strlen($input) % 4;
    if ($remainder) {
      $addlen = 4 - $remainder;
      $input .= str_repeat('=', $addlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
  }

  /**
   * HMACSHA256    https://jwt.io/  HMACSHA256    
   * @param string $input  base64UrlEncode(header).".".base64UrlEncode(payload)
   * @param string $key
   * @param string $alg      
   * @return mixed
   */
  private static function signature(string $input, string $key, string $alg = 'HS256')
  {
    $alg_config=array(
      'HS256'=>'sha256'
    );
    return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key,true));
  }
}

  //         begin
  //$payload=array('sub'=>'1234567890','name'=>'John Doe','iat'=>1516239022);
  //$jwt=new Jwt;
  //$token=$jwt->getToken($payload);
  //echo "<pre>";
  //echo $token;
  
  // token      
  //$getPayload=$jwt->verifyToken($token);
  //echo "<br><br>";
  //var_dump($getPayload);
  //echo "<br><br>";
  //         end
  
  
  //      begin
  //$payload_test=array('iss'=>'admin','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>'www.admin.com','jti'=>md5(uniqid('JWT').time()));;
  //$token_test=Jwt::getToken($payload_test);
  //echo "<pre>";
  //echo $token_test;
  
  // token      
  //$getPayload_test=Jwt::verifyToken($token_test);
  //echo "<br><br>";
  //var_dump($getPayload_test);
  //echo "<br><br>";
  //      end
?>
