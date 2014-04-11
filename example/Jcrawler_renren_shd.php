<?php
/**
 * 人人数据爬取分析
 * 本程序可根据cookie爬取人人网用户信息
 * 本例使用simple_html_dom.php作为html分析程序， 以DataSource.html文件为目标源文件
 * 本例中包含的另外三个类文件均可在github.com/thendfeel/xxtime项目上找到
 *
 * @package     xxtime/example/Jcrawler_renren_shd.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-02-25
 */
include '../simple_html_dom.php';
include '../Jutil.php';
include '../Jcurl.php';

$jcurl = new Jcurl();
$jcurl->cookie = 'anonymid=hqhua3nr-jbl05x; _r01_=1; JSESSIONID=abc-whI8Y7-UEdaQlnVqu; _urm_229165395=175935; timestamp=1392363322410; sign=2B4E0568DFB79B57307553544193ECBB; orignalSign=10a86cff6ad8895f07311796d9561073app_key12614735timestamp139236332241010a86cff6ad8895f07311796d9561073; depovince=GW; jebe_key=33c9afc4-38cd-494e-bd85-8287e2a80f89%7Cc4e0ea06c7f9f2bc5879e7a55a15104e%7C1393229670408%7C1; XNESSESSIONID=3f245656a8f1; ick_login=5d343dab-ba1d-4df7-920b-83de4ede785d; ick=2acb2169-fe6c-4e7c-8337-2e35b351b1c6; jebecookies=b9f8444e-f687-4584-b7fb-a83066c85541|||||; _de=52D969BB6AA78EF7CDC3323DFA47FB42; p=54c3d3ac5c6cddce191c761389a475515; t=f68dc9393363c26f9a9548a8a5d8750a5; societyguester=f68dc9393363c26f9a9548a8a5d8750a5; id=229165395; xnsid=97ee616f; loginfrom=null; feedType=229165395_hot';
$url = 'http://www.renren.com/238209939/profile?v=info_ajax';

// 本例用本地源文件，如抓取人人网线上数据请 修改cookie值和$url，即$jcurl->cookie和$url，其中cookie自己抓包获取
// $output = $jcurl->get($url);				// 用线上数据则取消注释本行
// $html = str_get_html($output);			// 用线上数据则取消注释本行
$html = file_get_html('DataSource.html');	// 用线上数据则注释掉本行

$input['plat'] = 'renren';
$input['uid'] = $html->find('input#fromno', 0)->value;
$input['name'] = $input['uid'];
$input['gender'] = $html->find('div#basicInfo dd', 0)->plaintext;
$input['realname'] = $html->find('input#title', 0)->value;
$input['age'] = date('Y') - $html->find('div#basicInfo dd a', 0)->plaintext;
$input['birthday'] = $html->find('div#basicInfo dd a', 0)->plaintext . '-' . $html->find('div#basicInfo dd a', 1)->plaintext . '-' . $html->find('div#basicInfo dd a', 2)->plaintext;
$input['qq'] = $html->find('dl#profile_info dd', 0)->plaintext;
$input['mobile'] = $html->find('dl#profile_info dd', 2)->plaintext;
$input['email'] = '';
$input['site'] = $html->find('dl#profile_info a', 0)->href;
$input['photo'] = $html->find('img#userpic', 0)->src;
$input['hometown'] = $html->find('div#basicInfo dd a', 4)->plaintext . ' ' . $html->find('div#basicInfo dd a', 5)->plaintext;
$input['college'] = $html->find('div#educationInfo dd a', 0)->plaintext;
$input['interest'] = $html->find('td.userProfileItemValue a', 0)->plaintext;
write_file(json_encode($input), 'result.txt');
print_r($input);