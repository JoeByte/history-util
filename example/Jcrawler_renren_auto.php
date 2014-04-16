<?php
/**
 * 人人数据爬取
 * 人人网有浏览100个好友限制，100个后面获取不到数据，后续可自动识别验证码
 * 本程序可根据cookie爬取人人网用户信息
 * 本例使用phpQuery.php作为html分析程序
 * 本例中用到的其他3个类文件均可在此项目中找到 https://github.com/thendfeel/xxtime
 *
 * @package     xxtime/example/Jcrawler_renren_auto.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-04-16
 * @see         http://code.google.com/p/phpquery/
 * @see         http://code.google.com/p/phpquery/wiki/Selectors
 */
include '../phpQuery.php';
include '../Jutil.php';
include '../Jcurl.php';

$jcurl = new Jcurl();
$jcurl->cookie = 'anonymid=hqhua3nr-jbl05x; _r01_=1; depovince=GW; jebe_key=33c9afc4-38cd-494e-bd85-8287e2a80f89%7Cc4e0ea06c7f9f2bc5879e7a55a15104e%7C1397202231165%7C1%7C1397627322060; feedType=229165395_hot; JSESSIONID=abct8LNYPxtIN6N71lPvu; ick=dc45c591-03f2-482f-9ff0-413b11588c64; jebecookies=831dd436-13ac-488f-92dd-19dafe59b856|||||; ick_login=144cff16-4f8e-4696-8407-907a4e87be1e; _de=52D969BB6AA78EF7CDC3323DFA47FB42; p=15bfdcb75c6c8ec9d9b9be73e78d15c95; first_login_flag=1; t=d9057df991531d757fa52671a95efec85; societyguester=d9057df991531d757fa52671a95efec85; id=229165395; xnsid=96aefb42; loginfrom=syshome';
$url = 'http://friend.renren.com/groupsdata';
$data = $jcurl->get($url); //获取好友
//$data = file_get_contents('data.html');
preg_match("/\"friends\"\:[\s][^\r\n]*/im",$data, $match);
$data = substr($match[0], strpos($match[0], ' '), -1);
$data = json_decode($data, TRUE);
if ($data) {
	foreach ($data as $key => $friend) {
		$fid = $friend['fid'];
		$url = "http://www.renren.com/{$fid}/profile?v=info_ajax";
		$data = $jcurl->get($url); //获取好友详情
		$doc = phpQuery::newDocument($data); //装载数据
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
		write_file(json_encode($input), 'friends.html');
	}
}