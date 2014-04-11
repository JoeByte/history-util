<?php
/**
 * 人人数据爬取分析
 * 本程序可根据cookie爬取人人网用户信息
 * 本例使用phpQuery.php作为html分析程序， 以DataSource.html文件为目标源文件
 * 本例中用到的其他3个类文件均可在此项目中找到 https://github.com/thendfeel/xxtime
 *
 * @package     xxtime/example/Jcrawler_renren.php
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

$jcurl = new Jcurl();
$jcurl->cookie = 'anonymid=hqhua3nr-jbl05x; _r01_=1; depovince=GW; JSESSIONID=abcLxMmIirzkU-KXgPovu; jebe_key=33c9afc4-38cd-494e-bd85-8287e2a80f89%7Ccfcd208495d565ef66e7dff9f98764da%7C1397183774814%7C0%7C1397183736794; jebecookies=46dd6b07-dffb-41fa-a999-95a487c1a7d6|||||; ick_login=d9216c76-18db-4bda-ab78-5ae406ac0f6c; _de=52D969BB6AA78EF7CDC3323DFA47FB42; p=1248f9b4ccd20020a9b995b3dfe055f25; first_login_flag=1; t=06fdc7e984837ec380c5c007d1d8550c5; societyguester=06fdc7e984837ec380c5c007d1d8550c5; id=229165395; xnsid=b077ba43; loginfrom=syshome';
$url = 'http://www.renren.com/238209939/profile?v=info_ajax';

// $data = $jcurl->get($url); //获取在线数据
// $doc = phpQuery::newDocument($data); //装载数据

phpQuery::newDocumentFileHTML('DataSource.html');

$input['plat'] = 'renren';
$input['uid'] = pq('input#fromno')->val();
$input['name'] = $input['uid'];
$input['gender'] = pq('div #basicInfo dd:first')->text();
$input['realname'] = pq('#title')->val();
$input['age'] = date('Y') - pq('div#basicInfo dd a:first')->text();
$input['birthday'] = pq('div#basicInfo dd a:first')->text() . '-' . pq('div#basicInfo dd a:eq(1)')->text() . '-' . pq('div#basicInfo dd a:eq(2)')->text();
$input['qq'] = pq('dl#profile_info dd:eq(0)')->text();
$input['mobile'] = pq('dl#profile_info dd:eq(2)')->text();
$input['email'] = '';
$input['site'] = pq('dl#profile_info a')->attr('href');
$input['photo'] = pq('img#userpic')->attr('src');
$input['hometown'] = pq('div#basicInfo dd:last a:first')->text() . ' ' . pq('div#basicInfo dd:last a:eq(1)')->text();
$input['college'] = pq('div#educationInfo dd a:first')->text();
$input['interest'] = pq('td.userProfileItemValue a:first')->text();
write_file($input['realname'], 'result.txt');
dbx($input);
