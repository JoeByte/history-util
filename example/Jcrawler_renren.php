<?php
/**
 * 人人数据爬取分析
 * 本程序可根据cookie爬取人人网用户信息
 * 本例使用phpQuery.php作为html分析程序， 以DataSource.html文件为目标源文件
 *
 * @package     xxtime/Simple/Jcrawler_renren.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-02-25
 * @see         http://code.google.com/p/phpquery/
 * @see         http://code.google.com/p/phpquery/wiki/Selectors
 */
include '../phpQuery.php';
include '../Jutil.php';
include '../Jcurl.php';

Jcurl::$cookie = 'anonymid=hqhua3nr-jbl05x; _r01_=1; JSESSIONID=abc-whI8Y7-UEdaQlnVqu; _urm_229165395=175935; timestamp=1392363322410; sign=2B4E0568DFB79B57307553544193ECBB; orignalSign=10a86cff6ad8895f07311796d9561073app_key12614735timestamp139236332241010a86cff6ad8895f07311796d9561073; depovince=GW; jebe_key=33c9afc4-38cd-494e-bd85-8287e2a80f89%7Cc4e0ea06c7f9f2bc5879e7a55a15104e%7C1393229670408%7C1; XNESSESSIONID=3f245656a8f1; ick_login=5d343dab-ba1d-4df7-920b-83de4ede785d; ick=2acb2169-fe6c-4e7c-8337-2e35b351b1c6; jebecookies=b9f8444e-f687-4584-b7fb-a83066c85541|||||; _de=52D969BB6AA78EF7CDC3323DFA47FB42; p=54c3d3ac5c6cddce191c761389a475515; t=f68dc9393363c26f9a9548a8a5d8750a5; societyguester=f68dc9393363c26f9a9548a8a5d8750a5; id=229165395; xnsid=97ee616f; loginfrom=null; feedType=229165395_hot';
$url = 'http://www.renren.com/238209939/profile?v=info_ajax';

phpQuery::newDocumentFileHTML('DataSource.html');
$element = pq('.userProfileItem:contains("电影")');
$content = $element->text();
dbx($content);

$input['plat'] = '';
$input['uid'] = '';
$input['name'] = '';
$input['gender'] = '';
$input['realname'] = '';
$input['age'] = '';
$input['birthday'] = '';
$input['qq'] = '';
$input['mobile'] = '';
$input['email'] = '';
$input['site'] = '';
$input['photo'] = '';
$input['hometown'] = '';
$input['college'] = '';
$input['interest'] = '';
Jcurl::writeFile($input['name'], 'result.txt');
print_r($input);